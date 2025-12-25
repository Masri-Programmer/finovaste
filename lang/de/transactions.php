<?php

return [
    'title' => 'Transaktionsverlauf',
    'description' => 'Sehen Sie Ihre Käufe, Investitionen, Spenden und Auktionsaktivitäten ein.',
    'search_placeholder' => 'Transaktionen suchen...',
    'item_unavailable' => 'Artikel nicht verfügbar',
    'empty_state' => 'Keine Transaktionen gefunden.',

    'filters' => [
        'all' => 'Alle',
        'purchases' => 'Käufe',
        'donations' => 'Spenden',
        'auctions' => 'Auktionen',
    ],

    'columns' => [
        'date' => 'Datum',
        'type' => 'Typ',
        'item' => 'Artikel',
        'amount' => 'Betrag',
        'status' => 'Status',
        'action' => 'Aktion',
    ],

    'types' => [
        'purchase' => 'Kauf',
        'auction_purchase' => 'Auktion (Sofortkauf)',
        'donation' => 'Spende',
        'auction' => 'Auktion',
    ],

    'status' => [
        'completed' => 'Abgeschlossen',
        'pending' => 'Ausstehend',
        'failed' => 'Fehlgeschlagen',
        'cancelled' => 'Abgebrochen',
    ],

    'actions' => [
        'view' => 'Ansehen',
        'download' => 'Herunterladen',
    ],
];