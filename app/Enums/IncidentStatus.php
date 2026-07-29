<?php

namespace App\Enums;

enum IncidentStatus: string
{
    case Open = 'open';
    case Contained = 'contained';
    case Closed = 'closed';
}
