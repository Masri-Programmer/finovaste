<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
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

        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $admin->addresses()->create(
            \Database\Factories\AddressFactory::new()->make()->toArray()
        );

        if ($adminRole) {
            $admin->roles()->attach($adminRole);
        }

        User::factory(20)->create()->each(function ($user) use ($userRole) {
            $user->addresses()->create(
                \Database\Factories\AddressFactory::new()->make()->toArray()
            );

            if ($userRole) {
                $user->roles()->attach($userRole);
            }
        });
    }
}
