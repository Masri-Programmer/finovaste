<?php

namespace App\Providers;

use Faker\Provider\Base;

class FakeMediaProvider extends Base
{
    /**
     * Returns a random image URL suitable for a listing image.
     */
    public function listingImage(int $width = 800, int $height = 600): string
    {
        // Use a keyword set for relevant images (Unsplash/LoremFlickr are good sources)
        $keywords = ['realestate', 'business', 'tech', 'product', 'design'];
        $keyword = $this->generator->randomElement($keywords);

        // Using LoremFlickr for reliable random images
        return "https://loremflickr.com/{$width}/{$height}/{$keyword}?random=" . $this->generator->randomNumber();
    }

    /**
     * Returns a placeholder URL for a small video (e.g., MP4).
     */
    public function listingVideo(): string
    {
        return 'https://test-videos.co.uk/vids/bigbuckbunny/mp4/h264/1080/Big_Buck_Bunny_1080_10s_1MB.mp4';
    }

    /**
     * Returns a placeholder URL for a small PDF document.
     */
    public function listingDocument(): string
    {
        return 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf';
    }
}
