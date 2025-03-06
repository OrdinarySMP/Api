<?php

namespace App\Enums;

enum DiscordButton: int
{
    case Primary = 1;
    case Secondary = 2;
    case Success = 3;
    case Danger = 4;
    case Link = 5;
}
