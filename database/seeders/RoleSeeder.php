<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'products.view',
            'products.manage',
            'categories.manage',
            'orders.view',
            'orders.manage',
            'payments.verify',
            'customers.view',
            'reports.view',
            'users.manage',
            'suppliers.manage',
            'stock.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $admin = Role::findOrCreate('admin');
        $admin->syncPermissions($permissions);

        Role::findOrCreate('cliente');
    }
}
