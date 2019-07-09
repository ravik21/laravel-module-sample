<?php

namespace Modules\User\Traits;

use Modules\User\Entities\UserInterface;
use Modules\User\Repositories\UserTokenRepository;
use Storage;

trait CanUploadChatMessageFiles
{
    protected $filesystem;

    public function uploadBase64File($payload)
    {
        $directory      = 'assets/uploads/';
        $file      = [];

        if ($payload) {
            $imageInfo  =  $payload['imagePayload'];

            if(isset($imageInfo['name']) && isset($imageInfo['file'])) {
              $directory .= $payload['chatable']['id'] .'/' . uniqid().md5($imageInfo['name']).'/';

              if (
                preg_match('/^data:image\/(\w+)/', $imageInfo['file'])
                || preg_match('/^data:application\/(\w+)/', $imageInfo['file'])
                || preg_match('/^data:text\/(\w+)/', $imageInfo['file'])
                || preg_match('/^data:audio\/(\w+)/', $imageInfo['file'])
                || preg_match('/^data:video\/(\w+)/', $imageInfo['file'])
              ) {

                $data = substr($imageInfo['file'], strpos($imageInfo['file'], ',') + 1);
                $data = base64_decode($data);

                $fileName   = str_replace('.'.$imageInfo['extension'] , '', $imageInfo['name']).'.'.$imageInfo['extension'];
                $directory .= $fileName;

                $fileTypes = explode('/', $imageInfo['type']);

                $tempPath = tempnam(sys_get_temp_dir(), 'chatables');
                $fullPath  = $this->getDestinationPath($directory);

                $this->getFilesystemDisk()->makeDirectory($this->getDestinationPath($directory));

                $path      = $directory;
                $fullPath  = $this->getDestinationPath($path);

                file_put_contents($tempPath, $data);

                $stream = fopen($tempPath, 'r+');
                $this->getFilesystemDisk()->putStream($fullPath, $stream, ['visibility' => 'public']);

                is_resource($stream) && fclose($stream);

                $file = [
                  'name'      => $fileName,
                  'mime_type' => $imageInfo['type'],
                  'size'      => $imageInfo['size'],
                  'fullpath'  => Storage::disk(config('user.config.filesystem'))->url($path),
                  'path'      => $path
                ];
              }
            }
        }

        return $file;
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
