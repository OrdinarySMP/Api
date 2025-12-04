<?php

declare(strict_types=1);

namespace App\Enums;

enum ApplicationResponseType: int
{
    case Accepted = 0;
    case Denied = 1;
}
