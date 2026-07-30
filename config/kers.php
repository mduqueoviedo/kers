<?php

use App\Enums\IncidentStatus;
use App\Enums\KaijuCategory;

return [
    'badges' => [
        'incident_statuses' => [
            IncidentStatus::Open->value => 'red',
            IncidentStatus::Contained->value => 'amber',
            IncidentStatus::Closed->value => 'green',
        ],
        'kaiju_categories' => [
            KaijuCategory::Aquatic->value => 'blue',
            KaijuCategory::Terrestrial->value => 'amber',
            KaijuCategory::Aerial->value => 'sky',
            KaijuCategory::Amphibious->value => 'green',
            KaijuCategory::Unknown->value => 'zinc',
        ],
    ],
    'pagination' => [
        'incidents_per_page' => 9,
        'kaijus_per_page' => 9,
    ],
];
