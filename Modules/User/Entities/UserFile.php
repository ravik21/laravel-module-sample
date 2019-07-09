<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\User\Enums\UserFileTypeEnum;

class UserFile extends Model
{
    protected $fillable = [
      'user_id',
      'file_id',
      'type'
    ];

    protected $table = 'user_files';

}
