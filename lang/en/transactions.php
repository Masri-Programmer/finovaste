<?php

return [
    'title' => 'Transaction history',
    'description' => 'View your purchases, donations, and auction activities.',
    'search_placeholder' => 'Search transactions...',
    'item_unavailable' => 'Item not available',
    'empty_state' => 'No transactions found.',

    'filters' => [
        'all' => 'All',
        'purchases' => 'Purchases',
        'donations' => 'Donations',
        'auctions' => 'Auctions',
    ],

    'columns' => [
        'date' => 'Date',
        'type' => 'Type',
        'item' => 'Item',
        'amount' => 'Amount',
        'status' => 'Status',
        'action' => 'Action',
    ],

    'types' => [
        'auction_purchase' => 'Auction (Buy Now)',
        'donation' => 'Donation',
        'auction' => 'Auction',
    ],

    'status' => [
        'completed' => 'Completed',
        'pending' => 'Pending',
        'failed' => 'Failed',
        'cancelled' => 'Cancelled',
    ],

    'actions' => [
        'view' => 'View',
        'download' => 'Download',
    ],
];