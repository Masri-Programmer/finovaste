<?php

return [
    'welcome' => 'Willkommen bei unserer Anwendung',
    'user'   => 'Benutzer',
    'listing_created' => 'Eintrag erfolgreich erstellt!',
    'listing_create_failed' => 'Ihr Eintrag konnte nicht gespeichert werden. Bitte versuchen Sie es erneut.',
    'listing_updated' => 'Eintrag erfolgreich aktualisiert.',
    'listing_update_failed' => 'Eintrag konnte nicht aktualisiert werden: ',
    'listing_deleted' => 'Eintrag erfolgreich gelöscht.',
    'listing_delete_failed' => 'Eintrag konnte nicht gelöscht werden: ',
    'listing_liked' => 'Eintrag gefällt mir!',
    'listing_unliked' => 'Eintrag gefällt mir nicht.',
    'titles' => [
        'success' => 'Erfolg',
        'error' => 'Fehler',
        'warning' => 'Achtung',
        'info' => 'Information',
    ],

    'success' => [
        'created' => ' :model wurde erfolgreich erstellt.',
        'updated' => ' :model wurde erfolgreich aktualisiert.',
        'deleted' => ' :model wurde erfolgreich gelöscht.',
        'restored' => ' :model wurde erfolgreich wiederhergestellt.',
        'generic' => 'Aktion erfolgreich abgeschlossen.',
        'liked'   => ':model liked erfolgreich.',
        'unliked' => ':model unliked erfolgreich.',
        'bid_placed' => 'Gebot erfolgreich abgegeben!',
    ],

    'errors' => [
        'generic_user' => 'Es ist ein Fehler aufgetreten. Bitte versuchen Sie es später erneut.',
        'not_found' => 'Das angeforderte :model konnte nicht gefunden werden.',
        'unauthorized' => 'Sie sind nicht berechtigt, diese Aktion durchzuführen.',
        'own_listing' => 'Sie können diese Aktion nicht an Ihrem eigenen Inserat durchführen.',
        'own_bid' => 'Sie können nicht auf Ihr eigenes Inserat bieten.',
        'own_review' => 'Sie können Ihr eigenes Inserat nicht bewerten.',
        'already_reviewed' => 'Sie haben dieses Inserat bereits bewertet.',
        'listing_expired' => 'Dieses Inserat ist bereits abgelaufen.',
        'not_an_auction' => 'Dieses Inserat ist keine Auktion.',
        'auction_not_started' => 'Die Auktion hat noch nicht begonnen.',
        'auction_ended' => 'Die Auktion ist beendet.',
        'bid_too_low' => 'Ihr Gebot muss mindestens :amount sein.',
        'buy_now_not_available' => 'Sofort-Kauf für diese Auktion nicht verfügbar.',
        'invalid_listing_type' => 'Ungültiger Inseratstyp.',
        'payment_init_failed' => 'Zahlungsinitialisierung fehlgeschlagen.',
        'invalid_session' => 'Ungültige Sitzung.',
        'payment_not_completed' => 'Zahlung nicht abgeschlossen oder wird verarbeitet.',
        'model_not_found' => 'Modell :model nicht gefunden.',
        'no_items_selected' => 'Keine Elemente zum Löschen ausgewählt.',
        'not_implemented' => ':feature noch nicht implementiert.',
    ],

    'auth' => [
        'login_required' => 'Sie müssen eingeloggt sein, um diese Aktion durchzuführen.',
    ],
];
