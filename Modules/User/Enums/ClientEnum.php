<?php

namespace Modules\User\Enums;

class ClientEnum
{
    const ADMIN = 1;
    const WEB = 2;
    const MOBILE = 3;

    public static function toArray()
    {
        return [self::ADMIN, self::WEB, self::MOBILE];
    }
}
