<?php

return [
    'categories' => [
        'aerial' => 'Aerial',
        'amphibious' => 'Amphibious',
        'aquatic' => 'Aquatic',
        'terrestrial' => 'Terrestrial',
        'unknown' => 'Unknown',
    ],
    'create' => [
        'description' => 'Add a known creature to the emergency response catalogue.',
        'form_description' => 'Optional observations about the creature.',
        'title' => 'Register kaiju',
    ],
    'delete' => [
        'action' => 'Delete kaiju',
        'confirmation' => 'Are you sure you want to delete :name? This action cannot be undone.',
        'heading' => 'Delete kaiju?',
        'warning_many' => 'This kaiju has :count associated incidents. Deleting it will also permanently delete those incidents.',
        'warning_one' => 'This kaiju has 1 associated incident. Deleting it will also permanently delete that incident.',
        'warning_zero' => 'This kaiju has no associated incidents.',
    ],
    'edit' => [
        'description' => 'Correct the known details for this creature.',
        'title' => 'Edit kaiju',
    ],
    'filters' => [
        'all_categories' => 'All categories',
        'all_threat_levels' => 'All threat levels',
        'search_placeholder' => 'Search by name',
    ],
    'history' => [
        'description' => 'Recorded activity involving this Kaiju, newest first.',
        'empty_description' => 'New activity involving this creature will appear here.',
        'empty_heading' => 'No incidents have been recorded for this Kaiju.',
        'heading' => 'Incident history',
    ],
    'index' => [
        'description' => 'Known creatures monitored by the Kaiju Emergency Response System.',
        'empty_description' => 'Known creatures will appear here once they are registered.',
        'empty_filtered_description' => 'Try different criteria or clear the current filters.',
        'empty_filtered_heading' => 'No kaijus match the current search and filters.',
        'empty_heading' => 'No kaijus have been catalogued.',
        'known_creature' => 'Known creature',
        'title' => 'Kaiju catalogue',
    ],
    'level' => 'Level :level',
    'detail_level_of_five' => 'Level :level of 5',
    'level_of_five' => 'Threat level :level of 5',
    'record' => 'Known creature record',
    'select_category' => 'Select a category',
    'success' => [
        'created' => 'Kaiju registered successfully.',
        'deleted' => 'Kaiju deleted successfully.',
        'updated' => 'Kaiju updated successfully.',
    ],
];
