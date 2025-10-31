<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Database\Factories\AddressFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::firstWhere('slug', 'admin');
        $userRole = Role::firstWhere('slug', 'user');

        // Use updateOrCreate to find the user by email, or create them if they don't exist
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'], // Attributes to find by
            [                                 // Attributes to update or create with
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Only create an address if the user doesn't have one
        if ($admin->addresses()->count() === 0) {
            $admin->addresses()->create(
                AddressFactory::new()->make()->toArray()
            );
        }

        // Only attach the role if the user doesn't already have it
        if ($adminRole && !$admin->roles->contains($adminRole)) {
            $admin->roles()->attach($adminRole);
        }

        // Create 20 new users and attach the user role
        User::factory(20)->create()->each(function ($user) use ($userRole) {
            $user->addresses()->create(
                AddressFactory::new()->make()->toArray()
            );

            // Also add a check here to be safe
            if ($userRole && !$user->roles->contains($userRole)) {
                $user->roles()->attach($userRole);
            }
        });
    }
}
