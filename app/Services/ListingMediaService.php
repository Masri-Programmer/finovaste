<?php

namespace App\Services;

use App\Models\Listing;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ListingMediaService
{
    /**
     * Handle all *new* file uploads from a request.
     * This method loops through known collections and adds
     * any new files found in the request.
     *
     * @param Request $request The incoming request.
     * @param Listing $listing The listing to attach media to.
     * @return void
     */
    public function handleUploads(Request $request, Listing $listing)
    {
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $listing->addMedia($file)
                    ->toMediaCollection('images');
            }
        }

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $listing->addMedia($file)
                    ->toMediaCollection('documents');
            }
        }

        if ($request->hasFile('videos')) {
            foreach ($request->file('videos') as $file) {
                $listing->addMedia($file)
                    ->toMediaCollection('videos');
            }
        }
    }

    /**
     * Private helper to process a single file collection.
     *
     * @param Request $request
     * @param Listing $listing
     * @param string $collectionName (e.g., 'images', 'documents')
     * @return void
     */
    private function uploadCollection(Request $request, Listing $listing, string $collectionName): void
    {
        if ($request->hasFile($collectionName)) {

            foreach ($request->file($collectionName) as $file) {
                $listing->addMedia($file)->toMediaCollection($collectionName);
            }
        }
    }
    /**
     * Handle the deletion of media items.
     * Assumes $mediaIds have already been validated by the Form Request.
     *
     * @param array|null $mediaIds An array of media IDs to delete.
     * @return void
     */
    public function handleDeletions(array $mediaIds = null): void
    {
        if (empty($mediaIds)) {
            return;
        }

        $mediaItems = Media::whereIn('id', $mediaIds)->get();

        foreach ($mediaItems as $mediaItem) {
            $mediaItem->delete();
        }
    }
}
