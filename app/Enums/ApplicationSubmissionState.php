<?php

namespace App\Enums;

enum ApplicationSubmissionState: int
{
    case InProgress = 0;
    case Pending = 1;
    case Accepted = 2;
    case Denied = 3;
    case Cancelled = 4;
}
