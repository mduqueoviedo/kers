<?php

namespace App\Enums;

enum KaijuCategory: string
{
    case Aquatic = 'aquatic';
    case Terrestrial = 'terrestrial';
    case Aerial = 'aerial';
    case Amphibious = 'amphibious';
    case Unknown = 'unknown';
}
