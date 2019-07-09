<?php

namespace Modules\User\Enums;

class ApprovalStatusEnum
{
    const PENDING = 0;
    const ACCEPTED = 1;
    const REJECTED = 2;
    const ACTIVATED = 3;
    const NOTACTIVATED = 4;
    const SUSPENDED = 5;

    public static function toArray()
    {
        return [self::PENDING, self::ACCEPTED, self::REJECTED, self::ACTIVATED, self::NOTACTIVATED, self::SUSPENDED];
    }

    public static function getApprovalStatus($status)
    {
        $statuses = [
          0 => self::PENDING,
          1 => self::ACCEPTED,
          2 => self::REJECTED,
          3 => self::ACTIVATED,
          4 => self::NOTACTIVATED,
          5 => self::SUSPENDED
        ];

        return isset($statuses[$status]) ? $statuses[$status] : self::PENDING;
    }
}
