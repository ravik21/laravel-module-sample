<?php

namespace Modules\User\Enums;

class UserFileTypeEnum
{
    const TERMS_CONDITIONS = 1;
    const CHAT_FILES = 2;

    public static function toArray()
    {
        return [self::TERMS_CONDITIONS, self::CHAT_FILES];
    }

    public static function getType($type)
    {
        $types = [
          'TERMS_CONDITIONS' => self::TERMS_CONDITIONS,
          'CHAT_FILES' => self::CHAT_FILES
        ];

        return isset($types[$type]) ? $types[$type] : self::CHAT_FILES;
    }
}
