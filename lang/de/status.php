<?php

return [
    // 1xx: Informationen
    100 => 'Fortfahren',
    101 => 'Protokolle werden gewechselt',
    102 => 'In Bearbeitung',
    103 => 'Frühe Hinweise',

    // 2xx: Erfolg
    200 => 'OK',
    201 => 'Erstellt',
    202 => 'Akzeptiert',
    203 => 'Nicht autoritative Informationen',
    204 => 'Kein Inhalt',
    205 => 'Inhalt zurücksetzen',
    206 => 'Teilinhalt',
    207 => 'Multi-Status',
    208 => 'Bereits gemeldet',
    226 => 'IM verwendet',

    // 3xx: Umleitung
    300 => 'Mehrere Möglichkeiten',
    301 => 'Dauerhaft verschoben',
    302 => 'Gefunden',
    303 => 'Siehe andere',
    304 => 'Nicht modifiziert',
    305 => 'Proxy verwenden',
    307 => 'Temporäre Weiterleitung',
    308 => 'Dauerhafte Weiterleitung',

    // 4xx: Client-Fehler
    400 => 'Fehlerhafte Anfrage',
    401 => 'Nicht autorisiert',
    402 => 'Zahlung erforderlich',
    403 => 'Verboten',
    404 => 'Nicht gefunden',
    405 => 'Methode nicht erlaubt',
    406 => 'Nicht akzeptabel',
    407 => 'Proxy-Authentifizierung erforderlich',
    408 => 'Zeitüberschreitung der Anforderung',
    409 => 'Konflikt',
    410 => 'Verschwunden',
    411 => 'Länge erforderlich',
    412 => 'Vorbedingung fehlgeschlagen',
    413 => 'Nutzlast zu groß',
    414 => 'URI zu lang',
    415 => 'Nicht unterstützter Medientyp',
    416 => 'Bereich nicht erfüllbar',
    417 => 'Erwartung fehlgeschlagen',
    418 => 'Ich bin eine Teekanne', // RFC 2324
    419 => 'Seite abgelaufen', // Common Framework specific
    421 => 'Fehlgeleitete Anforderung',
    422 => 'Unverarbeitbare Entität',
    423 => 'Gesperrt',
    424 => 'Fehlgeschlagene Abhängigkeit',
    425 => 'Zu früh',
    426 => 'Upgrade erforderlich',
    428 => 'Vorbedingung erforderlich',
    429 => 'Zu viele Anfragen',
    431 => 'Anforderungskopfzeilen zu groß',
    444 => 'Verbindung ohne Antwort geschlossen', // Nginx
    449 => 'Wiederholen mit', // Microsoft
    451 => 'Aus rechtlichen Gründen nicht verfügbar',
    499 => 'Client hat die Anfrage geschlossen', // Nginx

    // 5xx: Server-Fehler
    500 => 'Interner Serverfehler',
    501 => 'Nicht implementiert',
    502 => 'Bad Gateway',
    503 => 'Dienst nicht verfügbar',
    504 => 'Gateway-Zeitüberschreitung',
    505 => 'HTTP-Version nicht unterstützt',
    506 => 'Variante verhandelt auch',
    507 => 'Unzureichender Speicher',
    508 => 'Schleife erkannt',
    510 => 'Nicht erweitert',
    511 => 'Netzwerk-Authentifizierung erforderlich',
    599 => 'Netzwerk-Verbindungs-Timeout',

    // Anwendungsstatus
    'pending' => 'Ausstehend',
    'success' => 'Erfolg',
    'completed' => 'Abgeschlossen',
    'testing' => 'Im Test',
    'failed' => 'Fehlgeschlagen',
    'error' => 'Fehler',
    'cancelled' => 'Abgebrochen',
    'approved' => 'Genehmigt',
    'rejected' => 'Abgelehnt',
    'processing' => 'In Bearbeitung',
    'active' => 'Aktiv',
    'inactive' => 'Inaktiv',
    'draft' => 'Entwurf',
    'published' => 'Veröffentlicht',
    'archived' => 'Archiviert',
];