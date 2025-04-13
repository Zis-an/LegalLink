<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'test',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('123456')
        ]);

        // Create admin role (if not already exists)
        $role = Role::firstOrCreate(['name' => 'admin']);

        // Give all permissions to admin
        $permissions = Permission::pluck('name')->toArray();
        $role->syncPermissions($permissions);

        // Assign role to user by name (preferred)
        $user->assignRole('admin');
    }
}
