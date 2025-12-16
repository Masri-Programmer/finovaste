<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Kdabrow\SeederOnce\SeederOnce;

class RolesAndPermissionsSeeder extends SeederOnce
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // === Create Category Permissions ===
        Permission::create(['name' => 'edit categories']);
        Permission::create(['name' => 'delete categories']);
        Permission::create(['name' => 'create categories']);
        Permission::create(['name' => 'view categories']);

        // === Create User Permissions ===
        Permission::create(['name' => 'edit users']);
        Permission::create(['name' => 'delete users']);
        Permission::create(['name' => 'create users']);
        Permission::create(['name' => 'view users']);

        // === Create Listing Permissions ===
        Permission::create(['name' => 'edit listings']);
        Permission::create(['name' => 'delete listings']);
        Permission::create(['name' => 'create listings']);
        Permission::create(['name' => 'view listings']);



        $roleUser = Role::create(['name' => 'user'])
            ->givePermissionTo(['view categories']);

        $roleAdmin = Role::create(['name' => 'admin']);
        $roleAdmin->givePermissionTo(Permission::all());

        $user = User::find(1);
        if ($user) {
            $user->assignRole($roleAdmin);
        }
    }
}
