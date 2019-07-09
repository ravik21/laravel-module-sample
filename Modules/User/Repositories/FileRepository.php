<?php

namespace Modules\User\Repositories;

use Modules\Core\Repositories\BaseRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface FileRepository extends BaseRepository
{
    /**
     * Create the favourites
     * @param Array $favourites
     * @return mixed|void
     */
    public function create($file);

    /**
    * Create a file row from the given file
    * @param  UploadedFile $file
    * @param int $parentId
    * @return mixed
    */
    public function createFromFile(UploadedFile $file, int $parentId = 0);
}
