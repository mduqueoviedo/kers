<?php

return [
    'description' => 'Consulta eventos sísmicos recientes obtenidos del Servicio Geológico de Estados Unidos.',
    'empty' => [
        'description' => 'No hay eventos recientes disponibles en este momento.',
        'heading' => 'No se encontraron eventos sísmicos',
    ],
    'errors' => [
        'heading' => 'Los datos de USGS no están disponibles',
        'unavailable' => 'No se han podido obtener los últimos eventos sísmicos. Inténtalo más tarde.',
    ],
    'event_label' => 'Evento de USGS',
    'import' => [
        'button' => 'Crear incidente',
        'heading' => 'Crear un incidente a partir de un evento sísmico',
        'instructions' => '1. Selecciona un evento sísmico de la lista. 2. Selecciona el Kaiju responsable. 3. Crea el incidente y continúa en su página de detalle.',
        'kaiju_label' => 'Kaiju responsable',
        'kaiju_placeholder' => 'Selecciona un Kaiju',
        'no_event' => 'Selecciona un evento de la lista inferior.',
        'selected_label' => 'Evento seleccionado:',
    ],
    'magnitude' => 'Magnitud',
    'not_available' => 'No disponible',
    'occurred_at' => 'Ocurrido',
    'title' => 'Eventos sísmicos recientes',
    'validation' => [
        'catalogue_unavailable' => 'No se han podido obtener los eventos actuales de USGS. Inténtalo más tarde.',
        'event_unavailable' => 'El evento de USGS seleccionado ya no está disponible. Elige otro evento.',
    ],
    'view_source' => 'Ver detalles de USGS',
];
