<?php

return [
    'title' => 'Historique des transactions',
    'description' => 'Consultez vos achats, investissements, dons et activités d\'enchères.',
    'search_placeholder' => 'Rechercher des transactions...',
    'item_unavailable' => 'Article non disponible',
    'empty_state' => 'Aucune transaction trouvée.',
    'filters' => [
        'all' => 'Tout',
        'purchases' => 'Achats',
        'investments' => 'Investissements',
        'donations' => 'Dons',
        'auctions' => 'Enchères',
    ],
    'columns' => [
        'date' => 'Date',
        'type' => 'Type',
        'item' => 'Article',
        'amount' => 'Montant',
        'status' => 'Statut',
        'action' => 'Action',
    ],
    'types' => [
        'purchase' => 'Achat',
        'auction_buy_now' => 'Enchère (Achat immédiat)',
        'investment' => 'Investissement',
        'donation' => 'Don',
        'auction' => 'Enchère',
    ],
    'status' => [
        'completed' => 'Terminé',
        'pending' => 'En attente',
        'failed' => 'Échoué',
        'cancelled' => 'Annulé',
    ],
    'actions' => [
        'view' => 'Voir',
    ],
];
