<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create comprehensive permissions for all modules
        $permissions = [
            // Dashboard
            'view dashboard',
            
            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Role Management
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            
            // Product Management
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',
            
            'view brands',
            'create brands',
            'edit brands',
            'delete brands',
            
            'view product-models',
            'create product-models',
            'edit product-models',
            'delete product-models',
            
            'view products',
            'create products',
            'edit products',
            'delete products',
            
            // Stock Management
            'view stock',
            'create stock',
            'edit stock',
            'delete stock',
            'view stock-movements',
            'adjust stock',
            
            // Supplier Management
            'view suppliers',
            'create suppliers',
            'edit suppliers',
            'delete suppliers',
            
            // Purchase Management
            'view purchases',
            'create purchases',
            'edit purchases',
            'delete purchases',
            'receive purchases',
            'print purchase-invoices',
            
            // Customer Management
            'view customers',
            'create customers',
            'edit customers',
            'delete customers',
            
            // Sales Management
            'view sales',
            'create sales',
            'edit sales',
            'delete sales',
            'complete sales',
            'print invoices',
            
            // Service Management
            'view services',
            'create services',
            'edit services',
            'delete services',
            'update service-status',
            'print service-memos',
            
            // Warranty Management
            'view warranties',
            'verify warranties',
            
            // Warranty Submissions
            'view warranty-submissions',
            'create warranty-submissions',
            'edit warranty-submissions',
            'delete warranty-submissions',
            'print warranty-memos',
            
            // Payment Management
            'view payments',
            'create payments',
            'edit payments',
            'delete payments',
            
            // Return Management
            'view purchase-returns',
            'create purchase-returns',
            'edit purchase-returns',
            'delete purchase-returns',
            'approve purchase-returns',
            'complete purchase-returns',
            
            'view sale-returns',
            'create sale-returns',
            'edit sale-returns',
            'delete sale-returns',
            'approve sale-returns',
            'complete sale-returns',
            
            'view service-returns',
            'create service-returns',
            'edit service-returns',
            'delete service-returns',
            'approve service-returns',
            'complete service-returns',
            'process service-refunds',
            
            // Accounting Management
            'view bank-accounts',
            'create bank-accounts',
            'edit bank-accounts',
            'delete bank-accounts',
            'update bank-balances',
            
            'view accounts',
            'create accounts',
            'edit accounts',
            'delete accounts',
            
            'view journal-entries',
            'create journal-entries',
            'edit journal-entries',
            'delete journal-entries',
            'post journal-entries',
            'unpost journal-entries',
            
            // Expense Management
            'view expenses',
            'create expenses',
            'edit expenses',
            'delete expenses',
            'approve expenses',
            'mark-expenses-paid',
            'cancel expenses',
            
            // Accounting Reports
            'view accounting-reports',
            'export accounting-reports',
            
            // Reports & Analytics
            'view sales-reports',
            'view purchase-reports',
            'view financial-reports',
            'view inventory-reports',
            'export reports',
            
            // Settings
            'view settings',
            'edit settings',
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdminRole->syncPermissions(Permission::all()); // All permissions

        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->syncPermissions([
            'view dashboard',
            'view users',
            'create users',
            'edit users',
            'view roles',
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',
            'view brands',
            'create brands',
            'edit brands',
            'delete brands',
            'view product-models',
            'create product-models',
            'edit product-models',
            'delete product-models',
            'view products',
            'create products',
            'edit products',
            'delete products',
            'view stock',
            'create stock',
            'edit stock',
            'adjust stock',
            'view stock-movements',
            'view suppliers',
            'create suppliers',
            'edit suppliers',
            'delete suppliers',
            'view purchases',
            'create purchases',
            'edit purchases',
            'delete purchases',
            'receive purchases',
            'print purchase-invoices',
            'view customers',
            'create customers',
            'edit customers',
            'delete customers',
            'view sales',
            'create sales',
            'edit sales',
            'delete sales',
            'complete sales',
            'print invoices',
            'view services',
            'create services',
            'edit services',
            'delete services',
            'update service-status',
            'print service-memos',
            'view warranties',
            'verify warranties',
            'view warranty-submissions',
            'create warranty-submissions',
            'edit warranty-submissions',
            'delete warranty-submissions',
            'print warranty-memos',
            'view payments',
            'create payments',
            'edit payments',
            'delete payments',
            'view purchase-returns',
            'create purchase-returns',
            'edit purchase-returns',
            'delete purchase-returns',
            'approve purchase-returns',
            'complete purchase-returns',
            'view sale-returns',
            'create sale-returns',
            'edit sale-returns',
            'delete sale-returns',
            'approve sale-returns',
            'complete sale-returns',
            'view service-returns',
            'create service-returns',
            'edit service-returns',
            'delete service-returns',
            'approve service-returns',
            'complete service-returns',
            'process service-refunds',
            'view bank-accounts',
            'create bank-accounts',
            'edit bank-accounts',
            'delete bank-accounts',
            'update bank-balances',
            'view accounts',
            'create accounts',
            'edit accounts',
            'delete accounts',
            'view journal-entries',
            'create journal-entries',
            'edit journal-entries',
            'delete journal-entries',
            'post journal-entries',
            'unpost journal-entries',
            'view expenses',
            'create expenses',
            'edit expenses',
            'delete expenses',
            'approve expenses',
            'mark-expenses-paid',
            'cancel expenses',
            'view accounting-reports',
            'export accounting-reports',
            'view sales-reports',
            'view purchase-reports',
            'view financial-reports',
            'view inventory-reports',
            'export reports',
            'view settings',
            'edit settings',
        ]);

        $managerRole = Role::firstOrCreate(['name' => 'Manager']);
        $managerRole->syncPermissions([
            'view dashboard',
            'view products',
            'view stock',
            'view stock-movements',
            'adjust stock',
            'view suppliers',
            'view purchases',
            'create purchases',
            'view customers',
            'view sales',
            'create sales',
            'edit sales',
            'complete sales',
            'print invoices',
            'view services',
            'create services',
            'edit services',
            'update service-status',
            'print service-memos',
            'view warranties',
            'verify warranties',
            'view warranty-submissions',
            'create warranty-submissions',
            'view payments',
            'create payments',
            'view purchase-returns',
            'create purchase-returns',
            'approve purchase-returns',
            'view sale-returns',
            'create sale-returns',
            'approve sale-returns',
            'view service-returns',
            'create service-returns',
            'approve service-returns',
            'view bank-accounts',
            'view accounts',
            'view journal-entries',
            'create journal-entries',
            'view expenses',
            'create expenses',
            'approve expenses',
            'view accounting-reports',
            'view sales-reports',
            'view purchase-reports',
            'view financial-reports',
            'view inventory-reports',
            'export reports',
        ]);

        $staffRole = Role::firstOrCreate(['name' => 'Staff']);
        $staffRole->syncPermissions([
            'view dashboard',
            'view products',
            'view stock',
            'view stock-movements',
            'view customers',
            'view sales',
            'create sales',
            'complete sales',
            'print invoices',
            'view services',
            'create services',
            'print service-memos',
            'view warranties',
            'verify warranties',
            'view warranty-submissions',
            'create warranty-submissions',
            'view payments',
            'create payments',
            'view sales-reports',
            'view inventory-reports',
        ]);

        $this->command->info('Roles and Permissions created successfully!');
    }
}
