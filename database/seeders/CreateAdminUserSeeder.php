<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Client;
use App\Models\Lawyer;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

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

        // 2. Moderator / Sub Admin
        $moderator = User::create([
            'name' => 'Moderator',
            'email' => 'moderator@gmail.com',
            'password' => bcrypt('123456'),
            'role_id' => $adminRole->id, // Assign the role_id
        ]);
        $moderator->assignRole('admin');

        // 3. Four Clients
        for ($i = 1; $i <= 4; $i++) {
            $user = User::create([
                'name' => "Client $i",
                'email' => "client$i@gmail.com",
                'password' => bcrypt('123456'),
                'role_id' => $clientRole->id, // Assign the role_id
            ]);
            $user->assignRole('client');

            // Create related client model
            Client::create([
                'user_id' => $user->id,
                'address' => 'Client Address ' . $i,
                'dob' => now()->subYears(20 + $i)->format('Y-m-d'),
                'photo' => 'images/user.jpg',
            ]);
        }

        // 4. Four Lawyers
        for ($i = 1; $i <= 4; $i++) {
            $user = User::create([
                'name' => "Lawyer $i",
                'email' => "lawyer$i@gmail.com",
                'password' => bcrypt('123456'),
                'role_id' => $lawyerRole->id, // Assign the role_id
            ]);
            $user->assignRole('lawyer');

            // Create related lawyer model
            Lawyer::create([
                'user_id' => $user->id,
                'bar_id' =>  $i,
                'practice_area' => 'General Practice',
                'chamber_name' => 'Chamber ' . $i,
                'chamber_address' => 'Address ' . $i,
                'photo' => 'images/user.jpg',
            ]);
        }
    }
}
