<?php

return [
    'title' => 'Neues Inserat erstellen',
    'description' => 'Wählen Sie eine Kategorie und füllen Sie die Details aus, um Ihre Kampagne zu starten.',

    'buttons' => [
        'submit' => 'Kampagne anlegen',
        'submitting' => 'Wird erstellt...',
        'preview' => 'Vorschau ansehen',
        'save_draft' => 'Als Entwurf speichern',
    ],

    'types' => [
        'private' => [
            'title' => 'Private Anlässe',
            'description' => 'Unterstützen Sie Geburtstage oder besondere Ereignisse im Kreis von Familie und Freunden – ganz einfach durch private Schenkungen.',
        ],
        'creative' => [
            'title' => 'Gründer:innen & Kreative',
            'description' => 'Verwirklichen Sie Ihre kreativen Ideen: Präsentieren Sie Ihr Projekt unserer Community und sammeln Sie die Unterstützung, die Sie für die Umsetzung benötigen.',
        ],
        'charity' => [
            'title' => 'Spendenaktionen',
            'description' => 'Für ehrenamtliches Engagement: Stellen Sie lokale Initiativen oder internationale Hilfsprojekte vor und erhalten Sie gezielte Unterstützung.',
        ],
        'charity_auction' => [
            'title' => 'Wohltätige Aktionen',
            'description' => 'Bieten Sie Produkte zur Auktion oder zum Direktkauf an und unterstützen Sie mit den Erlösen einen guten Zweck.',
        ],
    ],

    'sections' => [
        'core' => 'Basisdaten',
        'type' => 'Kategorie wählen',
        'details' => 'Details zur Kampagne',
        'media' => 'Medien (Bilder, Dokumente, Videos)',
        'design' => 'Gestaltung & Vorschau',
        'settings' => 'Einstellungen & Privatsphäre',
        'verification' => 'Verifizierung & Rechtliches',
    ],

    'fields' => [
        'title' => [
            'label' => 'Titel der Kampagne',
            'placeholder' => 'z.B. "Hilfe für das Tierheim" oder "Mein 30. Geburtstag"',
        ],
        'description' => [
            'label' => 'Beschreibung & Gestaltung',
            'placeholder' => 'Beschreiben Sie Ihr Vorhaben. Nutzen Sie Fettgedrucktes und verschiedene Schriftarten zur Gestaltung...',
            'hint' => 'Sie können hier Text formatieren.',
        ],
        'category' => [
            'label' => 'Kategorie',
            'placeholder' => 'Kategorie auswählen',
        ],
        
        'is_private' => [
            'label' => 'Privat (Nur mit Einladungslink)',
            'help' => 'Wenn aktiviert, wird die Seite nicht öffentlich gelistet. Sie ist nur für Personen sichtbar, die den Einladungslink (WhatsApp, SMS, E-Mail) erhalten. Keine Registrierung für Spender notwendig.',
        ],

        'media' => [
            'label' => 'Dateien & Medien',
            'images' => 'Bilder hochladen',
            'videos' => 'Videos',
            'video_embed' => 'Video einbetten (YouTube/Vimeo Link)',
            'documents' => 'Dokumente',
            'dropzone' => 'Dateien hier ablegen oder klicken',
        ],

        'association_check' => [
            'label' => 'Ich handele im Namen eines eingetragenen Vereins (e.V.)',
            'help' => 'Spendenaktionen dürfen nur von in Deutschland registrierten Vereinen erstellt werden.',
        ],
        'association_proof' => [
            'label' => 'Nachweis Gemeinnützigkeit / Vereinsregisterauszug',
            'placeholder' => 'Dokument hochladen',
        ],
        'tax_receipt_info' => [
            'label' => 'Hinweis zu Spendenquittungen',
            'text' => 'Wir sind verpflichtet, für jede Spende über 300 Euro eine Spendenquittung oder Rechnung auszustellen. Bitte bestätigen Sie, dass Sie dies gewährleisten können.',
        ],

        'donation_goal' => [
            'label' => 'Spendenziel (€)',
            'placeholder' => 'z.B. 5000',
        ],
        'price' => [
            'label' => 'Festpreis (€)',
            'placeholder' => 'z.B. 49.99',
        ],
        'start_price' => [
            'label' => 'Startgebot (€)',
            'placeholder' => 'z.B. 1.00',
        ],
        'reserve_price' => [
            'label' => 'Mindestpreis (€)',
            'placeholder' => 'Nicht öffentlich sichtbar',
        ],
        'buy_it_now_price' => [
            'label' => 'Sofort-Kaufen Preis (€)',
            'placeholder' => 'Ermöglicht sofortigen Kauf',
        ],
        'quantity' => [
            'label' => 'Verfügbare Menge',
        ],
        'starts_at' => [
            'label' => 'Startdatum',
            'placeholder' => 'Startdatum wählen',
        ],
        'ends_at' => [
            'label' => 'Laufzeit Ende',
            'placeholder' => 'Enddatum wählen',
        ],
        'expires_at' => [
            'label' => 'Ablaufdatum (optional)',
            'placeholder' => 'Datum auswählen',
        ],
        'is_goal_flexible' => [
            'label' => 'Flexibles Ziel (Gelder auch bei Nichterreichen behalten)',
        ],
        'investment_goal' => [
            'label' => 'Investitionsziel',
        ],
        'minimum_investment' => [
            'label' => 'Mindestinvestition',
        ],
        'shares_offered' => [
            'label' => 'Angebotene Anteile',
        ],
        'share_price' => [
            'label' => 'Preis pro Anteil',
        ],
        'location' => [
            'label' => 'Ort / Standort',
            'placeholder' => 'z.B. Berlin, Deutschland',
        ],
    ],

    'notifications' => [
        'error' => 'Fehler beim Erstellen. Bitte überprüfen Sie die Eingaben (z.B. Vereinsnachweis).',
        'success' => 'Kampagne erfolgreich angelegt! Sie wird nun überprüft.',
        'preview_mode' => 'Sie befinden sich im Vorschau-Modus. Klicken Sie auf "Kampagne anlegen", um zu veröffentlichen.',
    ],

    'tooltips' => [
        'preview' => 'Sehen Sie hier, wie die Seite für Besucher aussehen wird.',
        'invitation_link' => 'Diesen Link können Sie nach Erstellung kopieren und per WhatsApp/SMS versenden.',
    ],
    
    'placeholders' => [
        'video_embed' => 'https://www.youtube.com/watch?v=...',
        'investment_goal' => 'z.B. 50000',
        'minimum_investment' => 'z.B. 500',
        'shares_offered' => 'z.B. 1000',
        'share_price' => 'z.B. 50',
    ],
    
    'validation' => [
        'association_required' => 'Für Spendenaktionen ist ein Nachweis als eingetragener Verein erforderlich.',
        'receipt_agreement' => 'Sie müssen der Ausstellung von Spendenquittungen ab 300€ zustimmen.',
    ],

    'terms' => [
        'title' => 'Allgemeine Geschäftsbedingungen',
        'description' => 'Mit der Erstellung eines Inserats stimmen Sie unseren AGBs zu.',
        'agree' => 'Ich stimme den',
        'link' => 'Allgemeine Geschäftsbedingungen',
    ],
];

