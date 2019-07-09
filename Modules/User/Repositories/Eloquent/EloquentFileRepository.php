<?php

namespace Modules\User\Repositories\Eloquent;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\User\Entities\File;
use Modules\Media\Helpers\FileHelper;
use Illuminate\Database\Eloquent\Collection;
use Modules\User\Repositories\FileRepository;
use Modules\Media\Repositories\FolderRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;

class EloquentFileRepository extends EloquentBaseRepository implements FileRepository
{
    public function create($data)
    {
        return parent::create($data);
    }

    public function createFromFile(UploadedFile $file, int $parentId = 0)
    {
        $dimensions = $this->identifyDimensions($file);

        $fileName = FileHelper::slug($file->getClientOriginalName());

        $exists = $this->model->where('name', $fileName)->where('folder_id', $parentId)->first();

        if ($exists) {
            $fileName = $this->getNewUniqueFilename($fileName);
        }

        $data = [
            'name' => $fileName,
            'path' => $this->getPathFor($fileName, $parentId),
            'extension' => substr(strrchr($fileName, '.'), 1),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getFileInfo()->getSize(),
            'folder_id' => $parentId,
            'is_folder' => 0,
            'width' => $dimensions['w'],
            'height' => $dimensions['h']
        ];

        // event($event = new FileIsCreating($data));

        $file = $this->model->create($data);

        // event(new FileWasCreated($file));

        return $file;
    }

    private function getPathFor(string $filename, int $folderId)
    {
        if ($folderId !== 0) {
            $parent = app(FolderRepository::class)->findFolder($folderId);
            if ($parent !== null) {
                return $parent->path->getRelativeUrl() . '/'. $filename;
            }
        }

        return config('user.config.files-path') . $filename;
    }

    /**
    * Attempts to identify the file dimensions.
    *
    * @param UploadedFile $file
    * @return Array $dimensions
    */
    public function identifyDimensions(UploadedFile $file)
    {
        $dimensions = ['w' => 0, 'h' => 0];

        $getID3   = new \getID3();
        $fileInfo = $getID3->analyze($file->getRealPath());

        if (isset($fileInfo['video']) && is_array($fileInfo['video']) && isset($fileInfo['video']['resolution_x']) && isset($fileInfo['video']['resolution_y'])) {
          $dimensions['w'] = $fileInfo['video']['resolution_x'];
          $dimensions['h'] = $fileInfo['video']['resolution_y'];
        }

        return $dimensions;
    }

    public function incrementFileName(File $file) : File
    {
        $newFileName = $this->getNewUniqueFilename($file->path);
        $file->name = $newFileName;
        $file->save();

        return $file;
    }


    /**
     * @param $fileName
     * @return string
     */
    private function getNewUniqueFilename($fileName)
    {
        $fileNameOnly = pathinfo($fileName, PATHINFO_FILENAME);
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);

        $models = $this->model->where('name', 'LIKE', "$fileNameOnly%")->get();

        $versionCurrent = $models->reduce(function ($carry, $model) {
            $latestFilename = pathinfo($model->filename, PATHINFO_FILENAME);

            if (preg_match('/_([0-9]+)$/', $latestFilename, $matches) !== 1) {
                return $carry;
            }

            $version = (int)$matches[1];

            return ($version > $carry) ? $version : $carry;
        }, 0);

        return $fileNameOnly . '_' . ($versionCurrent+1) . '.' . $extension;
    }

    public function delete($id)
    {
      $file =  $this->model->destroy($id);
      return $file;
    }
}
