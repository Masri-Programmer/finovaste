<?php

return [
    'title' => 'Create New Listing',
    'description' => 'Choose a category and fill in the details to start your campaign.',

    'buttons' => [
        'submit' => 'Create Campaign',
        'submitting' => 'Creating...',
        'preview' => 'View Preview', 
        'save_draft' => 'Save as Draft',
    ],

    'types' => [
        'private' => [
            'title' => 'Private Occasions',
            'description' => 'Support birthdays or special events within the circle of family and friends – quite simply through private gifts.',
        ],
        'creative' => [
            'title' => 'Founders & Creatives',
            'description' => 'Realize your creative ideas: Present your project to our community and collect the support you need for implementation.',
        ],
        'charity' => [
            'title' => 'Donation Campaigns',
            'description' => 'For voluntary commitment: Present local initiatives or international aid projects and receive targeted support.',
        ],
        'charity_auction' => [ 
            'title' => 'Charity Actions',
            'description' => 'Offer products for auction or direct purchase and support a good cause with the proceeds.',
        ],
    ],

    'sections' => [
        'core' => 'Basic Details',
        'type' => 'Select Category',
        'details' => 'Campaign Details',
        'media' => 'Media (Images, Documents, Videos)',
        'design' => 'Design & Preview',
        'settings' => 'Settings & Privacy',
        'verification' => 'Verification & Legal',
    ],

    'fields' => [
        'title' => [
            'label' => 'Campaign Title',
            'placeholder' => 'e.g. "Help for the animal shelter" or "My 30th Birthday"',
        ],
        'description' => [
            'label' => 'Description & Design',
            'placeholder' => 'Describe your project. Use bold and different fonts for design...',
            'hint' => 'You can format text here.',
        ],
        'category' => [
            'label' => 'Category',
            'placeholder' => 'Select category',
        ],
        
        'is_private' => [
            'label' => 'Private (Invite link only)',
            'help' => 'If enabled, the page will not be listed publicly. It is only visible to people who receive the invite link (WhatsApp, SMS, Email). No registration necessary for donors.',
        ],

        'media' => [
            'label' => 'Files & Media',
            'images' => 'Upload Images',
            'videos' => 'Videos',
            'video_embed' => 'Embed Video (YouTube/Vimeo Link)',
            'documents' => 'Documents',
            'dropzone' => 'Drop files here or click',
        ],

        'association_check' => [
            'label' => 'I am acting on behalf of a registered association (e.V.)',
            'help' => 'Donation campaigns may only be created by associations registered in Germany.',
        ],
        'association_proof' => [
            'label' => 'Proof of Charitable Status / Association Register Extract',
            'placeholder' => 'Upload document',
        ],
        'tax_receipt_info' => [
            'label' => 'Note on Donation Receipts',
            'text' => 'We are obliged to issue a donation receipt or invoice for every donation over 300 euros. Please confirm that you can guarantee this.',
        ],

        'donation_goal' => [
            'label' => 'Donation Goal (€)',
            'placeholder' => 'e.g. 5000',
        ],
        'price' => [
            'label' => 'Fixed Price (€)',
            'placeholder' => 'e.g. 49.99',
        ],
        'start_price' => [
            'label' => 'Starting Bid (€)',
            'placeholder' => 'e.g. 1.00',
        ],
        'reserve_price' => [
            'label' => 'Reserve Price (€)',
            'placeholder' => 'Not visible to public',
        ],
        'buy_it_now_price' => [
            'label' => 'Buy It Now Price (€)',
            'placeholder' => 'Enables immediate purchase',
        ],
        'quantity' => [
            'label' => 'Available Quantity',
        ],
        'starts_at' => [
            'label' => 'Start Date',
            'placeholder' => 'Select start date',
        ],
        'ends_at' => [
            'label' => 'Runtime End',
            'placeholder' => 'Select end date',
        ],
        'expires_at' => [
            'label' => 'Expiration Date (Optional)',
            'placeholder' => 'Select a date',
        ],
        'is_goal_flexible' => [
            'label' => 'Flexible Goal (Keep funds even if not reached)',
        ],
        'investment_goal' => [
            'label' => 'Investment Goal',
        ],
        'minimum_investment' => [
            'label' => 'Minimum Investment',
        ],
        'shares_offered' => [
            'label' => 'Shares Offered',
        ],
        'share_price' => [
            'label' => 'Price per Share',
        ],
        'location' => [
            'label' => 'Location / Position',
            'placeholder' => 'e.g. Berlin, Germany',
        ],
    ],

    'notifications' => [
        'error' => 'Error creating. Please check the entries (e.g. proof of association).',
        'success' => 'Campaign created successfully! It will now be reviewed.',
        'preview_mode' => 'You are in preview mode. Click "Create Campaign" to publish.',
    ],

    'tooltips' => [
        'preview' => 'See here how the page will look for visitors.',
        'invitation_link' => 'You can copy this link after creation and send it via WhatsApp/SMS.',
    ],
    
    'placeholders' => [
        'video_embed' => 'https://www.youtube.com/watch?v=...',
        'investment_goal' => 'e.g. 50000',
        'minimum_investment' => 'e.g. 500',
        'shares_offered' => 'e.g. 1000',
        'share_price' => 'e.g. 50',
    ],
    
    'validation' => [
        'association_required' => 'Proof of registered association is required for donation campaigns.',
        'receipt_agreement' => 'You must agree to the issuance of donation receipts from 300€.',
    ],

    'terms' => [
        'title' => 'Terms and Conditions',
        'description' => 'By creating a listing, you agree to our terms and conditions.',
        'agree' => 'I agree to the',
        'link' => 'Terms of Service',
    ],
];