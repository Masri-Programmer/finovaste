<?php

return [
    'buttons' => [
        'submit' => 'Create advert',
        'submitting' => 'Creating...'
    ],
    'description' => 'Select a listing type and fill in the details to publish your offer.',
    'fields' => [
        'buy_it_now_price' => [
            'label' => 'Buy it now price (€) (optional)',
            'placeholder' => 'Allows immediate purchase'
        ],
        'category' => [
            'label' => 'Category',
            'placeholder' => 'Select category'
        ],
        'condition' => [
            'label' => 'state',
            'options' => [
                'new' => 'New',
                'refurbished' => 'overhauled',
                'used' => 'used'
            ]
        ],
        'description' => [
            'label' => 'Description',
            'placeholder' => 'Describe your ad in detail...'
        ],
        'donation_goal' => [
            'label' => 'Donation target (€)',
            'placeholder' => 'e.g. 5000'
        ],
        'ends_at' => [
            'label' => 'auction end',
            'placeholder' => 'Select end date'
        ],
        'expires_at' => [
            'label' => 'Expiry date (optional)',
            'placeholder' => 'Select date'
        ],
        'is_goal_flexible' => [
            'label' => 'Flexible goal (keep funds even if not achieved)'
        ],
        'location' => [
            'label' => 'Location',
            'placeholder' => 'e.g. “Berlin, Germany”'
        ],
        'media' => [
            'description' => 'Download ...',
            'documents' => 'Documents',
            'dropzone' => 'Drop or browse files here',
            'images' => 'Photos',
            'label' => 'File (photos, documents...)',
            'videos' => 'videos'
        ],
        'price' => [
            'label' => 'Price (€)',
            'placeholder' => 'e.g. 499.99'
        ],
        'quantity' => [
            'label' => 'Available quantity'
        ],
        'reserve_price' => [
            'label' => 'Minimum price (€) (optional)',
            'placeholder' => 'Not displayed publicly'
        ],
        'start_price' => [
            'label' => 'Starting bid (€)',
            'placeholder' => 'e.g. 1.00'
        ],
        'starts_at' => [
            'label' => 'Auction start (optional)',
            'placeholder' => 'Select start date'
        ],
        'title' => [
            'label' => 'Title',
            'placeholder' => 'e.g. “Modern city apartment”'
        ]
    ],
    'notifications' => [
        'error' => 'Error creating the ad. Please check your entries.',
        'success' => 'Advertisement successfully created! It is now being reviewed.'
    ],
    'sections' => [
        'core' => 'core details',
        'details' => 'Specific Details',
        'type' => 'Select ad type'
    ],
    'title' => 'Create new listing',
    'types' => [
        'auction' => [
            'description' => 'Sell item to highest bidder.',
            'title' => 'Auction'
        ],
        'buy_now' => [
            'description' => 'Fixed price for items or services.',
            'title' => 'Buy Now'
        ],
        'donation' => [
            'description' => 'Collect funds for a specific purpose.',
            'title' => 'Fundraiser'
        ]
    ]
];
