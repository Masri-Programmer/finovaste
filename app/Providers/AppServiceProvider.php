<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Providers\FakeMediaProvider;
use Faker\Factory as FakerFactory;

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
    }
}
