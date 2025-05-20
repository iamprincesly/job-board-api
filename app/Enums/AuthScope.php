<?php

namespace App\Enums;

use App\Traits\BaseEnum;

enum AuthScope: string
{
    use BaseEnum;

    case CANDIDATE = 'candidate';
    case COMPANY = 'company';
}
