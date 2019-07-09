<?php

namespace Modules\User\Events;

use Illuminate\Queue\SerializesModels;
use Modules\User\Entities\File;

class FileWasUploaded
{
    /**
     * @var File
     */
    public $file;

    /**
     * @param File $file
     */
    public function __construct(File $file)
    {
        $this->file = $file;
    }
}
