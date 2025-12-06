<?php

return [
    'title' => 'Transaction History',
    'description' => 'View your purchases, investments, donations, and auction activity.',
    'search_placeholder' => 'Search transactions...',
    'item_unavailable' => 'Item unavailable',
    'empty_state' => 'No transactions found.',
    
    'filters' => [
        'all' => 'All',
        'purchases' => 'Purchases',
        'investments' => 'Investments',
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
        'purchase' => 'Purchase',
        'auction_buy_now' => 'Auction Buy Now',
        'investment' => 'Investment',
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
    ],
];