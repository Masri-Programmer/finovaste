<?php

return [
    'buttons' => [
        'submit' => 'Créer une annonce',
        'submitting' => 'Création en cours...',
    ],
    'description' => 'Sélectionnez un type d\'annonce et remplissez les détails pour publier votre offre.',
    'fields' => [
        'buy_it_now_price' => [
            'label' => 'Prix d\'achat immédiat (€) (facultatif)',
            'placeholder' => 'Permet un achat immédiat',
        ],
        'category' => [
            'label' => 'Catégorie',
            'placeholder' => 'Sélectionnez une catégorie',
        ],
        'condition' => [
            'label' => 'État',
            'options' => [
                'new' => 'Neuf',
                'refurbished' => 'Reconditionné',
                'used' => 'Occasion',
            ],
        ],
        'description' => [
            'label' => 'Description',
            'placeholder' => 'Décrivez votre annonce en détail...',
        ],
        'donation_goal' => [
            'label' => 'Objectif de don (€)',
            'placeholder' => 'ex. 5000',
        ],
        'ends_at' => [
            'label' => 'Fin de l\'enchère',
            'placeholder' => 'Sélectionnez une date de fin',
        ],
        'expires_at' => [
            'label' => 'Date d\'expiration (facultatif)',
            'placeholder' => 'Sélectionnez une date',
        ],
        'is_goal_flexible' => [
            'label' => 'Objectif flexible (conserver les fonds même s\'il n\'est pas atteint)',
        ],
        'location' => [
            'label' => 'Localisation',
            'placeholder' => 'ex. « Berlin, Allemagne »',
        ],
        'media' => [
            'description' => 'Télécharger ...',
            'documents' => 'Documents',
            'dropzone' => 'Déposez ou parcourez les fichiers ici',
            'images' => 'Photos',
            'label' => 'Fichier (photos, documents...)',
            'videos' => 'Vidéos',
        ],
        'price' => [
            'label' => 'Prix (€)',
            'placeholder' => 'ex. 499.99',
        ],
        'quantity' => [
            'label' => 'Quantité disponible',
        ],
        'reserve_price' => [
            'label' => 'Prix de réserve (€) (facultatif)',
            'placeholder' => 'Non affiché publiquement',
        ],
        'start_price' => [
            'label' => 'Enchère de départ (€)',
            'placeholder' => 'ex. 1.00',
        ],
        'starts_at' => [
            'label' => 'Début de l\'enchère (facultatif)',
            'placeholder' => 'Sélectionnez une date de début',
        ],
        'title' => [
            'label' => 'Titre',
            'placeholder' => 'ex. « Appartement moderne en ville »',
        ],
        'investment_goal' => 'Objectif d\'investissement',
        'minimum_investment' => 'Investissement minimum',
        'shares_offered' => 'Parts offertes',
        'share_price' => 'Prix par part',
        'images' => 'Images',
        'documents' => 'Documents',
        'videos' => 'Vidéos',
    ],
    'notifications' => [
        'error' => 'Erreur lors de la création de l\'annonce. Veuillez vérifier vos saisies.',
        'success' => 'Annonce créée avec succès ! Elle est maintenant en cours de révision.',
    ],
    'sections' => [
        'core' => 'Détails essentiels',
        'type' => 'Type d\'annonce',
        'common' => 'Détails communs',
        'details' => 'Détails de l\'annonce',
        'media' => 'Média (Images, Documents, Vidéos)',
    ],
    'title' => 'Créer une nouvelle annonce',
    'types' => [
        'auction' => [
            'description' => 'Vendre un article au plus offrant.',
            'title' => 'Enchère',
        ],
        'buy_now' => [
            'description' => 'Prix fixe pour des articles ou services.',
            'title' => 'Achat immédiat',
        ],
        'donation' => [
            'description' => 'Collecter des fonds pour un objectif spécifique.',
            'title' => 'Collecte de fonds',
        ],
        'investment' => 'Investissement',
    ],
    'placeholders' => [
        'title' => 'ex. « Veste en cuir vintage »',
        'description' => 'Décrivez votre article, projet ou objectif en détail...',
        'category' => 'Sélectionnez une catégorie',
        'location' => 'ex. « Berlin, Allemagne »',
        'price' => 'ex. 99.50',
        'start_price' => 'ex. 10.00',
        'reserve_price' => 'ex. 50.00',
        'buy_it_now_price' => 'ex. 150.00',
        'donation_goal' => 'ex. 5000',
        'investment_goal' => 'ex. 50000',
        'minimum_investment' => 'ex. 500',
        'shares_offered' => 'ex. 1000',
        'share_price' => 'ex. 50',
    ],
    'conditions' => [
        'new' => 'Neuf',
        'used' => 'Occasion',
        'refurbished' => 'Reconditionné',
    ],
    'tooltips' => [
        'is_goal_flexible' => 'Si coché, les dons peuvent continuer même après que l\'objectif soit atteint.',
    ],
    'media' => [
        'dropzone' => 'Déposez les fichiers ici ou cliquez pour parcourir',
        'remove' => 'Supprimer',
        'existing' => 'Média existant',
    ],
    'listings' => [
        'edit' => [
            'title' => 'Modifier « {title} »',
            'description' => 'Mettez à jour les détails de votre annonce, les médias et les paramètres spécifiques ci-dessous.',
            'actions' => [
                'save' => 'Enregistrer les modifications',
                'saving' => 'Enregistrement en cours...',
            ],
        ],
    ],
    'edit' => [
        'notifications' => [
            'success' => 'Annonce mise à jour avec succès.',
        ],
    ],
];
