<?php

return [
    // Page Headers
    'title' => 'Admin-Dashboard',
    'subtitle' => 'Verwalten Sie Ihren Marktplatz, Einträge und Benutzer',

    // Section: Resource Index (The generic Table Page)
    'index' => [
        // "Alle Benutzer"
        'title' => 'Alle :resource',
        // "Benutzer verwalten"
        'description' => ':resource verwalten',
    ],

    // Section: Actions specific to the Dashboard logic (Complex phrases)
    'actions' => [
        // "Benutzer hinzufügen" (Using :resource allows correct German grammar order)
        'add_resource' => ':resource hinzufügen',
        // "Benutzer durchsuchen..."
        'search_placeholder' => ':resource durchsuchen...',
    ],

    // Section: Table Headers
    'table' => [
        'actions_header' => 'Aktionen',
        // You can keep specific column headers here if you pass them manually
        'headers' => [
            'title' => 'Titel',
            'name' => 'Name',
            'email' => 'E-Mail',
            'category' => 'Kategorie',
            'status' => 'Status',
            'price' => 'Preis',
            'date' => 'Datum',
        ],
    ],

    // Section: Empty States
    'states' => [
        'empty' => 'Keine :resource gefunden',
        'active' => 'Aktiv',
        'inactive' => 'Inaktiv',
    ],

    // Section: Dialogs
    'dialogs' => [
        'delete' => [
            'title' => 'Sind Sie sicher?',
            // :item is the specific name of the thing being deleted (e.g. "User John Doe")
            'description' => 'Diese Aktion kann nicht rückgängig gemacht werden. Dies wird ":item" dauerhaft löschen.',
        ],
    ],

    'stats' => [
        'total_listings' => 'Gesamteinträge',
        'listings_desc' => 'In allen Kategorien',
        'total_users' => 'Benutzer insgesamt',
        'active_listings' => 'Aktive Einträge',
    ],

    'notifications' => [
        'saved' => ':resource erfolgreich gespeichert.',
        'created' => ':resource erfolgreich erstellt.',
        'deleted' => ':resource erfolgreich gelöscht.',
    ],
];
