<?php

namespace App\Policies;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ListingPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Listing  $listing
     * @return bool
     */
    public function update(User $user, Listing $listing): bool
    {
        // Only the user who owns the listing can update it.
        return $user->id === $listing->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Listing  $listing
     * @return bool
     */
    public function delete(User $user, Listing $listing): bool
    {
        // Only the user who owns the listing can delete it.
        return $user->id === $listing->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     * (Useful if you implement a "trash" feature)
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Listing  $listing
     * @return bool
     */
    public function restore(User $user, Listing $listing): bool
    {
        return $user->id === $listing->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Listing  $listing
     * @return bool
     */
    public function forceDelete(User $user, Listing $listing): bool
    {
        // You might restrict this to just admins, 
        // or allow users to permanently delete their own.
        return $user->id === $listing->user_id;
    }
}
