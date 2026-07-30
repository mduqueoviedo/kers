<?php

return [
    'categories' => [
        'aerial' => 'Aéreo',
        'amphibious' => 'Anfibio',
        'aquatic' => 'Acuático',
        'terrestrial' => 'Terrestre',
        'unknown' => 'Desconocido',
    ],
    'create' => [
        'description' => 'Añade una criatura conocida al catálogo de respuesta ante emergencias.',
        'form_description' => 'Observaciones opcionales sobre la criatura.',
        'title' => 'Registrar kaiju',
    ],
    'delete' => [
        'action' => 'Eliminar kaiju',
        'confirmation' => '¿Seguro que quieres eliminar :name? Esta acción no se puede deshacer.',
        'heading' => '¿Eliminar kaiju?',
        'warning_many' => 'Este kaiju tiene :count incidentes asociados. Al eliminarlo también se eliminarán permanentemente esos incidentes.',
        'warning_one' => 'Este kaiju tiene 1 incidente asociado. Al eliminarlo también se eliminará permanentemente ese incidente.',
        'warning_zero' => 'Este kaiju no tiene incidentes asociados.',
    ],
    'edit' => [
        'description' => 'Corrige los datos conocidos de esta criatura.',
        'title' => 'Editar kaiju',
    ],
    'filters' => [
        'all_categories' => 'Todas las categorías',
        'all_threat_levels' => 'Todos los niveles de amenaza',
        'search_placeholder' => 'Buscar por nombre',
    ],
    'history' => [
        'description' => 'Actividad registrada relacionada con este kaiju, de más reciente a más antigua.',
        'empty_description' => 'La nueva actividad relacionada con esta criatura aparecerá aquí.',
        'empty_heading' => 'No se han registrado incidentes para este kaiju.',
        'heading' => 'Historial de incidentes',
    ],
    'index' => [
        'description' => 'Criaturas conocidas supervisadas por el Sistema de Respuesta ante Emergencias Kaiju.',
        'empty_description' => 'Las criaturas conocidas aparecerán aquí cuando se registren.',
        'empty_filtered_description' => 'Prueba otros criterios o limpia los filtros actuales.',
        'empty_filtered_heading' => 'Ningún kaiju coincide con la búsqueda y los filtros actuales.',
        'empty_heading' => 'No se han catalogado kaijus.',
        'known_creature' => 'Criatura conocida',
        'title' => 'Catálogo de kaijus',
    ],
    'level' => 'Nivel :level',
    'detail_level_of_five' => 'Nivel :level de 5',
    'level_of_five' => 'Nivel de amenaza :level de 5',
    'record' => 'Registro de criatura conocida',
    'select_category' => 'Selecciona una categoría',
    'success' => [
        'created' => 'Kaiju registrado correctamente.',
        'deleted' => 'Kaiju eliminado correctamente.',
        'updated' => 'Kaiju actualizado correctamente.',
    ],
];
