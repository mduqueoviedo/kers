<?php

return [
    'create' => [
        'description' => 'Register an event involving one known Kaiju.',
        'empty_description' => 'Register a Kaiju before returning to this form.',
        'empty_heading' => 'A known Kaiju is required before recording an incident.',
        'title' => 'Record incident',
    ],
    'delete' => [
        'action' => 'Delete incident',
        'confirmation' => 'Are you sure you want to delete :title? This action cannot be undone.',
        'heading' => 'Delete incident?',
    ],
    'edit' => [
        'description' => 'Correct the recorded details or update the incident status.',
        'title' => 'Edit incident',
    ],
    'filters' => [
        'all_kaijus' => 'All Kaijus',
        'all_statuses' => 'All statuses',
        'newest_first' => 'Newest first',
        'occurrence_order' => 'Occurrence order',
        'oldest_first' => 'Oldest first',
        'search_placeholder' => 'Search by title or location',
    ],
    'index' => [
        'description' => 'Find recorded Kaiju incidents by their current details.',
        'empty_description' => 'Recorded Kaiju activity will appear here.',
        'empty_filtered_description' => 'Try different criteria or clear the current filters.',
        'empty_filtered_heading' => 'No incidents match the current search and filters.',
        'empty_heading' => 'No incidents have been recorded.',
        'record' => 'Incident record',
        'title' => 'Incident catalogue',
    ],
    'kaiju_involved' => 'Kaiju involved',
    'recorded_incident' => 'Recorded incident',
    'select_kaiju' => 'Select a Kaiju',
    'statuses' => [
        'closed' => 'Closed',
        'contained' => 'Contained',
        'open' => 'Open',
    ],
    'success' => [
        'created' => 'Incident recorded successfully.',
        'deleted' => 'Incident deleted successfully.',
        'updated' => 'Incident updated successfully.',
    ],
    'title' => 'Incident details',
];
