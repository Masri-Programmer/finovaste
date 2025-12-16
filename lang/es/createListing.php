<?php

return [
    'buttons' => [
        'submit' => 'Crear anuncio',
        'submitting' => 'Creando...',
    ],
    'description' => 'Selecciona un tipo de anuncio y completa los detalles para publicar tu oferta.',
    'fields' => [
        'buy_it_now_price' => [
            'label' => 'Precio de compra directa (€) (opcional)',
            'placeholder' => 'Permite la compra inmediata',
        ],
        'category' => [
            'label' => 'Categoría',
            'placeholder' => 'Seleccionar categoría',
        ],
        'condition' => [
            'label' => 'estado',
            'options' => [
                'new' => 'Nuevo',
                'refurbished' => 'reacondicionado',
                'used' => 'usado',
            ],
        ],
        'description' => [
            'label' => 'Descripción',
            'placeholder' => 'Describe tu anuncio en detalle...',
        ],
        'donation_goal' => [
            'label' => 'Meta de donación (€)',
            'placeholder' => 'ej. 5000',
        ],
        'ends_at' => [
            'label' => 'fin de subasta',
            'placeholder' => 'Seleccionar fecha de fin',
        ],
        'expires_at' => [
            'label' => 'Fecha de caducidad (opcional)',
            'placeholder' => 'Seleccionar fecha',
        ],
        'is_goal_flexible' => [
            'label' => 'Meta flexible (conservar fondos incluso si no se alcanza)',
        ],
        'location' => [
            'label' => 'Ubicación',
            'placeholder' => 'ej. “Berlín, Alemania”',
        ],
        'media' => [
            'description' => 'Descargar ...',
            'documents' => 'Documentos',
            'dropzone' => 'Arrastra o busca archivos aquí',
            'images' => 'Fotos',
            'label' => 'Archivo (fotos, documentos...)',
            'videos' => 'vídeos',
        ],
        'price' => [
            'label' => 'Precio (€)',
            'placeholder' => 'ej. 499.99',
        ],
        'quantity' => [
            'label' => 'Cantidad disponible',
        ],
        'reserve_price' => [
            'label' => 'Precio mínimo (€) (opcional)',
            'placeholder' => 'No se muestra públicamente',
        ],
        'start_price' => [
            'label' => 'Puja inicial (€)',
            'placeholder' => 'ej. 1.00',
        ],
        'starts_at' => [
            'label' => 'Inicio de subasta (opcional)',
            'placeholder' => 'Seleccionar fecha de inicio',
        ],
        'title' => [
            'label' => 'Título',
            'placeholder' => 'ej. “Apartamento moderno en la ciudad”',
        ],
        'investment_goal' => 'Objetivo de Inversión',
        'minimum_investment' => 'Inversión Mínima',
        'shares_offered' => 'Acciones Ofrecidas',
        'share_price' => 'Precio por Acción',
        'images' => 'Imágenes',
        'documents' => 'Documentos',
        'videos' => 'Vídeos',
    ],
    'notifications' => [
        'error' => 'Error al crear el anuncio. Por favor, revisa tus datos.',
        'success' => '¡Anuncio creado con éxito! Ahora está siendo revisado.',
    ],
    'sections' => [
        'core' => 'detalles principales',
        'type' => 'Tipo de Anuncio',
        'common' => 'Detalles Comunes',
        'details' => 'Detalles del Anuncio',
        'media' => 'Multimedia (Imágenes, Documentos, Vídeos)',
    ],
    'title' => 'Crear nuevo anuncio',
    'types' => [
        'auction' => [
            'description' => 'Vende un artículo al mejor postor.',
            'title' => 'Subasta',
        ],
        'purchase' => [
            'description' => 'Precio fijo para artículos o servicios.',
            'title' => 'Compra Directa',
        ],
        'donation' => [
            'description' => 'Recauda fondos para un propósito específico.',
            'title' => 'Recaudación de Fondos',
        ],
        'investment' => 'Inversión',
    ],
    'placeholders' => [
        'title' => 'ej., "Chaqueta de cuero vintage"',
        'description' => 'Describe tu artículo, proyecto u objetivo en detalle...',
        'category' => 'Selecciona una categoría',
        'location' => 'ej., "Berlín, Alemania"',
        'price' => 'ej., 99.50',
        'start_price' => 'ej., 10.00',
        'reserve_price' => 'ej., 50.00',
        'buy_it_now_price' => 'ej., 150.00',
        'donation_goal' => 'ej., 5000',
        'investment_goal' => 'ej., 50000',
        'minimum_investment' => 'ej., 500',
        'shares_offered' => 'ej., 1000',
        'share_price' => 'ej., 50',
    ],
    'conditions' => [
        'new' => 'Nuevo',
        'used' => 'Usado',
        'refurbished' => 'Reacondicionado',
    ],
    'tooltips' => [
        'is_goal_flexible' => 'Si está marcada, las donaciones pueden continuar incluso después de alcanzar la meta.',
    ],
    'media' => [
        'dropzone' => 'Arrastra archivos aquí o haz clic para buscar',
        'remove' => 'Eliminar',
        'existing' => 'Multimedia Existente',
    ],
    'listings' => [
        'edit' => [
            'title' => 'Editar "{title}"',
            'description' => 'Actualiza los detalles de tu anuncio, multimedia y configuraciones específicas a continuación.',
            'actions' => [
                'save' => 'Guardar Cambios',
                'saving' => 'Guardando...',
            ],
        ],
    ],
    'edit' => [
        'notifications' => [
            'success' => 'Anuncio actualizado con éxito.',
        ],
    ],
];
