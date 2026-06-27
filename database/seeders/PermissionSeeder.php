<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            'view_stock',
            'edit_stock',
            'manage_products',
            'view_pos',
            'perform_transaction',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Get Role
        $petugasRole = Role::firstOrCreate(['name' => 'petugas']);

        // Assign Permissions to Petugas
        $petugasRole->givePermissionTo([
            'view_stock',
            'edit_stock',
            'manage_products',
            'view_pos',
            'perform_transaction',
        ]);
        
        // Admin gets all
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());
    }
}
