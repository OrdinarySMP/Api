<?php

namespace App\Enums;

enum ApplicationRoleType: int
{
    case Restricted = 0;
    case Accepted = 1;
    case Denied = 2;
    case Ping = 3;
    case AcceptRemoval = 4;
    case DenyRemoval = 5;
    case Pending = 6;
    case Required = 7;
}
