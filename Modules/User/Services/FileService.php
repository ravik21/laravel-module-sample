<?php

namespace Modules\User\Services;

use Illuminate\Contracts\Filesystem\Factory;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Modules\User\Entities\File;
use Modules\User\Repositories\FileRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileService
{
    use DispatchesJobs;

    /**
     * @var FileRepository
     */
    private $file;
    /**
     * @var Factory
     */
    private $filesystem;

    public function __construct(FileRepository $file, Factory $filesystem)
    {
        $this->file = $file;
        $this->filesystem = $filesystem;
    }

    /**
     * @param  UploadedFile $file
     * @param int $parentId
     * @return mixed
     */
    public function store(UploadedFile $file, int $parentId = 0)
    {
        $savedFile = $this->file->createFromFile($file, $parentId);

        $path = $this->getDestinationPath($savedFile->getOriginal('path'));
        $stream = fopen($file->getRealPath(), 'r+');
        $this->filesystem->disk($this->getConfiguredFilesystem())->writeStream($path, $stream, [
            'visibility' => 'public',
            'mimetype' => $savedFile->mimetype,
        ]);

        return $savedFile;
    }

    /**
     * @param string $path
     * @return string
     */
    private function getDestinationPath($path)
    {
        if ($this->getConfiguredFilesystem() === 'local') {
            return basename(public_path()) . $path;
        }

        return $path;
    }

    /**
     * @return string
     */
    private function getConfiguredFilesystem()
    {
        return config('user.config.filesystem');
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function destroy($id)
    {
        $file = $this->file->delete($id);
        return $file;
    }
}
