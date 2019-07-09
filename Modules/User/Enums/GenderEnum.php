<?php

namespace Modules\User\Enums;

class GenderEnum
{
    const MALE = 1;
    const FEMALE = 2;
    const RATHERNOTSAY = 3;

    public static function toArray()
    {
        return [self::MALE, self::FEMALE, self::RATHERNOTSAY];
    }
}
