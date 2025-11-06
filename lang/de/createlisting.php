<?php

return [
    'buttons' => [
        'submit' => 'Inserat erstellen',
        'submitting' => 'Wird erstellt...'
    ],
    'description' => 'Wählen Sie einen Inseratstyp und füllen Sie die Details aus, um Ihr Angebot zu veröffentlichen.',
    'fields' => [
        'buy_it_now_price' => [
            'label' => 'Sofort-Kauf-Preis (€) (optional)',
            'placeholder' => 'Ermöglicht sofortigen Kauf'
        ],
        'category' => [
            'label' => 'Kategorie',
            'placeholder' => 'Kategorie auswählen'
        ],
        'condition' => [
            'label' => 'Zustand',
            'options' => [
                'new' => 'Neu',
                'refurbished' => 'Überholt',
                'used' => 'Gebraucht'
            ]
        ],
        'description' => [
            'label' => 'Beschreibung',
            'placeholder' => 'Beschreiben Sie Ihr Inserat im Detail ...'
        ],
        'donation_goal' => [
            'label' => 'Spendenziel (€)',
            'placeholder' => 'z.B. 5000'
        ],
        'ends_at' => [
            'label' => 'Auktionsende',
            'placeholder' => 'Enddatum wählen'
        ],
        'expires_at' => [
            'label' => 'Ablaufdatum (optional)',
            'placeholder' => 'Datum auswählen'
        ],
        'is_goal_flexible' => [
            'label' => 'Flexibles Ziel (Gelder auch bei Nichterreichen behalten)'
        ],
        'location' => [
            'label' => 'Standort',
            'placeholder' => 'z.B. „Berlin, Deutschland“'
        ],
        'media' => [
            'description' => 'Laden Sie ...',
            'documents' => 'Dokumente',
            'dropzone' => 'Dateien hier ablegen oder durchsuchen',
            'images' => 'Fotos',
            'label' => 'Datei (Fotos, Dokumente...)',
            'videos' => 'Videos'
        ],
        'price' => [
            'label' => 'Preis (€)',
            'placeholder' => 'z.B. 499.99'
        ],
        'quantity' => [
            'label' => 'Verfügbare Menge'
        ],
        'reserve_price' => [
            'label' => 'Mindestpreis (€) (optional)',
            'placeholder' => 'Wird nicht öffentlich angezeigt'
        ],
        'start_price' => [
            'label' => 'Startgebot (€)',
            'placeholder' => 'z.B. 1.00'
        ],
        'starts_at' => [
            'label' => 'Auktionsstart (optional)',
            'placeholder' => 'Startdatum wählen'
        ],
        'title' => [
            'label' => 'Titel',
            'placeholder' => 'z.B. „Moderne Stadtwohnung“'
        ]
    ],
    'notifications' => [
        'error' => 'Fehler beim Erstellen des Inserats. Bitte überprüfen Sie Ihre Eingaben.',
        'success' => 'Inserat erfolgreich erstellt! Es wird nun überprüft.'
    ],
    'sections' => [
        'core' => 'Kerndetails',
        'details' => 'Spezifische Details',
        'type' => 'Inseratstyp auswählen'
    ],
    'title' => 'Neues Inserat erstellen',
    'types' => [
        'auction' => [
            'description' => 'Artikel an den Höchstbietenden verkaufen.',
            'title' => 'Auktion'
        ],
        'buy_now' => [
            'description' => 'Fester Preis für Artikel oder Dienstleistungen.',
            'title' => 'Sofort-Kauf'
        ],
        'donation' => [
            'description' => 'Gelder für einen bestimmten Zweck sammeln.',
            'title' => 'Spendenaktion'
        ]
    ]
];
