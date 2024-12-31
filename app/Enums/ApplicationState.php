<?php

namespace App\Enums;

enum ApplicationState: int
{
    case Pending = 0;
    case Accepted = 1;
    case Denied = 2;
    case InProgress = 3;
}
