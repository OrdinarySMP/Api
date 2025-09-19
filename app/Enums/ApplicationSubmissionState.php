<?php

namespace App\Enums;

enum ApplicationSubmissionState: int
{
    case InProgress = 0;
    case Pending = 1;
    case Accepted = 2;
    case Denied = 3;
    case Cancelled = 4;

    public function label(): string
    {
        return match ($this) {
            self::InProgress => 'in progress',
            self::Pending => 'pendig',
            self::Accepted => 'accepted',
            self::Denied => 'denied',
            self::Cancelled => 'cancelled',
        };
    }
}
