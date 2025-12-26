<?php

return [
    'welcome' => 'Welcome to our application',
    'user' => 'User',
    'listing_created' => 'Listing created successfully!',
    'listing_create_failed' => 'Your listing could not be saved. Please try again.',
    'listing_updated' => 'Listing updated successfully.',
    'listing_update_failed' => 'Failed to update listing: ',
    'listing_deleted' => 'Entry successfully deleted.',
    'listing_delete_failed' => 'Entry could not be deleted: ',
    'listing_liked' => 'I like this entry!',
    'listing_unliked' => 'I don\'t like this listing.',
    'titles' => [
        'success' => 'success',
        'error' => 'Error',
        'warning' => 'Caution',
        'info' => 'Information',
    ],

    'success' => [
        'created' => ' :model was created successfully.',
        'updated' => ' :model has been updated successfully.',
        'deleted' => ' :model was successfully deleted.',
        'restored' => ' :model has been restored successfully.',
        'generic' => 'Action completed successfully.',
        'liked' => ':model liked successfully.',
        'unliked' => ':model unliked successful.',
        'bid_placed' => 'Bid placed successfully!',
    ],

    'errors' => [
        'generic_user' => 'An error has occurred. Please try again later.',
        'not_found' => 'The requested :model could not be found.',
        'unauthorized' => 'You are not authorized to perform this action.',
        'own_listing' => 'You cannot perform this action on your own listing.',
        'own_bid' => 'You cannot bid on your own listing.',
        'own_review' => 'You cannot review your own listing.',
        'already_reviewed' => 'You have already reviewed this listing.',
        'listing_expired' => 'This listing has already ended.',
        'not_an_auction' => 'This listing is not an auction.',
        'auction_not_started' => 'The auction has not started yet.',
        'auction_ended' => 'The auction has ended.',
        'bid_too_low' => 'Your bid must be at least :amount.',
        'buy_now_not_available' => 'Buy Now not available for this auction.',
        'invalid_listing_type' => 'Invalid listing type.',
        'payment_init_failed' => 'Payment initialization failed.',
        'invalid_session' => 'Invalid Session.',
        'payment_not_completed' => 'Payment not completed or is processing.',
        'model_not_found' => 'Model :model not found.',
        'no_items_selected' => 'No items selected for deletion.',
        'not_implemented' => ':feature not implemented yet.',
        'delete_own_account' => 'You cannot delete your own account.',
        'category_has_children' => 'Cannot delete: This category has subcategories. Please reassign them first.',
        'delete_failed' => 'An error occurred while deleting the :model.',
    ],


    'auth' => [
        'login_required' => 'You must be logged in to perform this action.',
        'registration_success' => 'Welcome, :name! Your account has been created successfully.',
        'registration_mail_failed' => 'Account created! However, we couldn\'t send the verification email. You can still login, but some features may be limited.',
    ],
];