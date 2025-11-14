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
        ],
        'investment_goal' => 'Investment Goal',
        'minimum_investment' => 'Minimum Investment',
        'shares_offered' => 'Shares Offered',
        'share_price' => 'Price per Share',

        'images' => 'Images',
        'documents' => 'Documents',
        'videos' => 'Videos',
    ],
    'notifications' => [
        'error' => 'Error creating the ad. Please check your entries.',
        'success' => 'Advertisement successfully created! It is now being reviewed.'
    ],
    'sections' => [
        'core' => 'core details',
        'type' => 'Listing Type',
        'common' => 'Common Details',
        'details' => 'Listing Details',
        'media' => 'Media (Images, Documents, Videos)',
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
        ],
        'investment' => 'Investment',
    ],


    'placeholders' => [
        // Common
        'title' => 'e.g., "Vintage Leather Jacket"',
        'description' => 'Describe your item, project, or goal in detail...',
        'category' => 'Select a category',
        'location' => 'e.g., "Berlin, Germany"',

        // Buy Now
        'price' => 'e.g., 99.50',

        // Auction
        'start_price' => 'e.g., 10.00',
        'reserve_price' => 'e.g., 50.00',
        'buy_it_now_price' => 'e.g., 150.00',

        // Donation
        'donation_goal' => 'e.g., 5000',

        // Investment
        'investment_goal' => 'e.g., 50000',
        'minimum_investment' => 'e.g., 500',
        'shares_offered' => 'e.g., 1000',
        'share_price' => 'e.g., 50',
    ],

    'conditions' => [
        'new' => 'New',
        'used' => 'Used',
        'refurbished' => 'Refurbished',
    ],

    'tooltips' => [
        'is_goal_flexible' => 'If checked, donations can continue even after the goal is met.',
    ],

    'media' => [
        'dropzone' => 'Drop files here or click to browse',
        'remove' => 'Remove',
        'existing' => 'Existing Media',
    ],

    'listings' => [
        'edit' => [
            'title' => 'Edit "{title}"',
            'description' => 'Update your listing details, media, and specific settings below.',
            'actions' => [
                'save' => 'Save Changes',
                'saving' => 'Saving...',
            ],
        ],
    ],

    'edit' => [
        'notifications' => [
            'success' => 'Listing updated successfully.',
        ],
    ],
];
