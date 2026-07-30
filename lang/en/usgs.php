<?php

return [
    'description' => 'Review recent seismic events retrieved from the United States Geological Survey.',
    'empty' => [
        'description' => 'No recent events are currently available.',
        'heading' => 'No seismic events found',
    ],
    'errors' => [
        'heading' => 'USGS data is unavailable',
        'unavailable' => 'The latest seismic events could not be retrieved. Please try again later.',
    ],
    'event_label' => 'USGS event',
    'magnitude' => 'Magnitude',
    'not_available' => 'Not available',
    'occurred_at' => 'Occurred',
    'title' => 'Recent seismic events',
    'validation' => [
        'catalogue_unavailable' => 'The current USGS events could not be retrieved. Please try again later.',
        'event_unavailable' => 'The selected USGS event is no longer available. Please choose another event.',
    ],
    'view_source' => 'View USGS details',
];
