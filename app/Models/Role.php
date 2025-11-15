<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasFactory;

    // You can add any custom logic, relationships,
    // or properties specific to your application's Role model here.

    // For example, if you want to ensure the guard name is always 'web'
    // protected $guard_name = 'web';

    // By default, it will use the 'id' as the route key.
    // If you want to use 'name' (e.g., /roles/admin) in your routes:
    // public function getRouteKeyName()
    // {
    //     return 'name';
    // }
}
