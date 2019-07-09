<?php

namespace Modules\User\Traits;

use Storage;

trait UploadFileToS3
{
    protected $filesystem;


    public function uploadFileToS3($name, $folder, $path)
    {
        $directory = config('user.config.files-path').$folder;

        $this->getFilesystemDisk()->makeDirectory($this->getDestinationPath($directory));

        $fullPath   = $this->getDestinationPath($directory).'/'.$name;
        $stream     = fopen($path, 'r+');

        $this->getFilesystemDisk()->putStream($fullPath, $stream, ['visibility' => 'public']);

        return Storage::disk(config('user.config.filesystem'))->url(trim($fullPath, '/'));
    }

    private function getFilesystemDisk()
    {
        if (!$this->filesystem) {
            $this->filesystem = app(\Illuminate\Contracts\Filesystem\Factory::class);
        }

        return $this->filesystem->disk(config('user.config.filesystem'));
    }

    private function getDestinationPath($path)
    {
        if (config('user.config.filesystem') === 'local') {
            return basename(public_path()) . $path;
        }

        return $path;
    }
}
