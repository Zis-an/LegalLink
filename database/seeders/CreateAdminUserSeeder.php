<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateAdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles if not exists
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $clientRole = Role::firstOrCreate(['name' => 'client']);
        $lawyerRole = Role::firstOrCreate(['name' => 'lawyer']);

        // Give all permissions to admin
        $permissions = Permission::pluck('name')->toArray();
        $adminRole->syncPermissions($permissions);

        // 1. Super Admin
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@gmail.com',
            'password' => bcrypt('123456'),
            'role_id' => $adminRole->id, // Assign the role_id
        ]);
        $superAdmin->assignRole('admin');
    }
}
