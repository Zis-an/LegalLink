<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'permissions.list',
            'permissions.create',
            'permissions.show',
            'permissions.update',
            'permissions.delete',

            'roles.list',
            'roles.create',
            'roles.show',
            'roles.update',
            'roles.delete',

            'cases.list',
            'cases.create',
            'cases.show',
            'cases.update',
            'cases.delete',

            'bids.list',
            'bids.create',
            'bids.show',
            'bids.update',
            'bids.delete',

            'clients.list',
            'clients.create',
            'clients.show',
            'clients.update',
            'clients.delete',

            'consultations.list',
            'consultations.create',
            'consultations.show',
            'consultations.update',
            'consultations.delete',

            'lawyers.list',
            'lawyers.create',
            'lawyers.show',
            'lawyers.update',
            'lawyers.delete',

            'users.list',
            'users.create',
            'users.show',
            'users.update',
            'users.delete',

            'notifications.list',

        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
