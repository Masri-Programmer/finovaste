<?php

return [
    'buttons' => [
        'submit' => 'Inserat erstellen',
        'submitting' => 'Wird erstellt...',
    ],
    'description' => 'Wählen Sie einen Inseratstyp und füllen Sie die Details aus, um Ihr Angebot zu veröffentlichen.',
    'fields' => [
        'buy_it_now_price' => [
            'label' => 'Sofort-Kauf-Preis (€) (optional)',
            'placeholder' => 'Ermöglicht sofortigen Kauf',
        ],
        'category' => [
            'label' => 'Kategorie',
            'placeholder' => 'Kategorie auswählen',
        ],
        'condition' => [
            'label' => 'Zustand',
            'options' => [
                'new' => 'Neu',
                'refurbished' => 'Überholt',
                'used' => 'Gebraucht',
            ],
        ],
        'description' => [
            'label' => 'Beschreibung',
            'placeholder' => 'Beschreiben Sie Ihr Inserat im Detail ...',
        ],
        'donation_goal' => [
            'label' => 'Spendenziel (€)',
            'placeholder' => 'z.B. 5000',
        ],
        'ends_at' => [
            'label' => 'Auktionsende',
            'placeholder' => 'Enddatum wählen',
        ],
        'expires_at' => [
            'label' => 'Ablaufdatum (optional)',
            'placeholder' => 'Datum auswählen',
        ],
        'is_goal_flexible' => [
            'label' => 'Flexibles Ziel (Gelder auch bei Nichterreichen behalten)',
        ],
        'location' => [
            'label' => 'Standort',
            'placeholder' => 'z.B. „Berlin, Deutschland“',
        ],
        'investment_goal' => 'Investitionsziel',
        'minimum_investment' => 'Mindestinvestition',
        'shares_offered' => 'Angebotene Anteile',
        'share_price' => 'Preis pro Anteil',
        'images' => 'Bilder',
        'documents' => 'Dokumente',
        'videos' => 'Videos',
        'media' => [
            'description' => 'Laden Sie ...',
            'documents' => 'Dokumente',
            'dropzone' => 'Dateien hier ablegen oder durchsuchen',
            'images' => 'Fotos',
            'label' => 'Datei (Fotos, Dokumente...)',
            'videos' => 'Videos',
        ],
        'price' => [
            'label' => 'Preis (€)',
            'placeholder' => 'z.B. 499.99',
        ],
        'quantity' => [
            'label' => 'Verfügbare Menge',
        ],
        'reserve_price' => [
            'label' => 'Mindestpreis (€) (optional)',
            'placeholder' => 'Wird nicht öffentlich angezeigt',
        ],
        'start_price' => [
            'label' => 'Startgebot (€)',
            'placeholder' => 'z.B. 1.00',
        ],
        'starts_at' => [
            'label' => 'Auktionsstart (optional)',
            'placeholder' => 'Startdatum wählen',
        ],
        'title' => [
            'label' => 'Titel',
            'placeholder' => 'z.B. „Moderne Stadtwohnung“',
        ],
    ],
    'notifications' => [
        'error' => 'Fehler beim Erstellen des Inserats. Bitte überprüfen Sie Ihre Eingaben.',
        'success' => 'Inserat erfolgreich erstellt! Es wird nun überprüft.',
    ],
    'title' => 'Neues Inserat erstellen',
    'types' => [
        'auction' => [
            'description' => 'Artikel an den Höchstbietenden verkaufen.',
            'title' => 'Auktion',
        ],
        'purchase' => [
            'description' => 'Fester Preis für Artikel oder Dienstleistungen.',
            'title' => 'Sofort-Kauf',
        ],
        'donation' => [
            'description' => 'Gelder für einen bestimmten Zweck sammeln.',
            'title' => 'Spendenaktion',
        ],
        'investment' => [
            'title' => 'Investition',
            'description' => 'Investieren Sie in Startups, Immobilien oder andere Projekte.',
        ],
    ],
    'sections' => [
        'common' => 'Allgemeine Details',
        'media' => 'Medien (Bilder, Dokumente, Videos)',
        'core' => 'Kerndetails',
        'details' => 'Spezifische Details',
        'type' => 'Inseratstyp auswählen',
    ],
    'placeholders' => [
        'title' => 'z.B. "Vintage Lederjacke"',
        'description' => 'Beschreiben Sie Ihren Artikel, Ihr Projekt oder Ihr Ziel im Detail...',
        'category' => 'Wählen Sie eine Kategorie',
        'location' => 'z.B. "Berlin, Deutschland"',
        'price' => 'z.B. 99,50',
        'start_price' => 'z.B. 10,00',
        'reserve_price' => 'z.B. 50,00',
        'buy_it_now_price' => 'z.B. 150,00',
        'donation_goal' => 'z.B. 5000',
        'investment_goal' => 'z.B. 50000',
        'minimum_investment' => 'z.B. 500',
        'shares_offered' => 'z.B. 1000',
        'share_price' => 'z.B. 50',
    ],
    'conditions' => [
        'new' => 'Neu',
        'used' => 'Gebraucht',
        'refurbished' => 'Überholt',
    ],
    'tooltips' => [
        'is_goal_flexible' => 'Wenn aktiviert, können Spenden auch nach Erreichen des Ziels fortgesetzt werden.',
    ],
    'media' => [
        'dropzone' => 'Dateien hier ablegen oder zum Durchsuchen klicken',
        'remove' => 'Entfernen',
        'existing' => 'Vorhandene Medien',
    ],
    'listings' => [
        'edit' => [
            'title' => '"{title}" bearbeiten',
            'description' => 'Aktualisieren Sie unten Ihre Inseratsdetails, Medien und spezifischen Einstellungen.',
            'actions' => [
                'save' => 'Änderungen speichern',
                'saving' => 'Speichert...',
            ],
        ],
    ],
    'edit' => [
        'notifications' => [
            'success' => 'Inserat erfolgreich aktualisiert.',
        ],
    ],
];
