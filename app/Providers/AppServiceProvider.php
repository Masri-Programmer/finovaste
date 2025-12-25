<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Providers\FakeMediaProvider;
use Faker\Factory as FakerFactory;
use App\Models\Listing;
use App\Observers\ListingObserver;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register custom Faker Provider for media URLs only when seeding
        if ($this->app->environment('local', 'staging') && class_exists(FakerFactory::class)) {
            $this->app->singleton(\Faker\Generator::class, function () {
                $faker = FakerFactory::create();
                $faker->addProvider(new FakeMediaProvider($faker));
                return $faker;
            });
        }
        Listing::observe(ListingObserver::class);
        \App\Models\Review::observe(\App\Observers\ReviewObserver::class);
        \App\Models\ListingFaq::observe(\App\Observers\ListingFaqObserver::class);
        \App\Models\AuctionListing::observe(\App\Observers\AuctionListingObserver::class);
        \App\Models\DonationListing::observe(\App\Observers\DonationListingObserver::class);

        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Login::class,
            \App\Listeners\SetLocaleOnLogin::class
        );
    }
}
