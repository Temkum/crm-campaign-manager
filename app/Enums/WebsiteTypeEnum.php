<?php

namespace App\Enums;

enum WebsiteTypeEnum: int
{
    case UNKNOWN = 0;
    case WORDPRESS = 1;
    case STATIC = 2;
    case LARAVEL = 3;
}