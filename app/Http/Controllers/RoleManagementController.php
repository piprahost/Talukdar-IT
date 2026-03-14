<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleManagementController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index(Request $request)
    {
        $this->authorizePermission('view roles');
        $query = Role::withCount('users')->with('permissions')->latest();
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        $roles = $query->paginate(10)->withQueryString();
        return view('role-management.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $this->authorizePermission('create roles');
        $permissions = Permission::all()->sortBy('name');
        
        // Group permissions by module
        $groupedPermissions = [];
        $moduleMap = [
            'dashboard' => 'Dashboard',
            'users' => 'User Management',
            'roles' => 'Role Management',
            'categories' => 'Categories',
            'brands' => 'Brands',
            'product-models' => 'Product Models',
            'products' => 'Products',
            'stock' => 'Stock Management',
            'stock-movements' => 'Stock Movements',
            'suppliers' => 'Suppliers',
            'purchases' => 'Purchases',
            'purchase-invoices' => 'Purchase Invoices',
            'customers' => 'Customers',
            'sales' => 'Sales',
            'invoices' => 'Invoices',
            'services' => 'Services',
            'service-status' => 'Service Status',
            'service-memos' => 'Service Memos',
            'warranties' => 'Warranties',
            'warranty-submissions' => 'Warranty Submissions',
            'warranty-memos' => 'Warranty Memos',
            'payments' => 'Payments',
            'purchase-returns' => 'Purchase Returns',
            'sale-returns' => 'Sale Returns',
            'service-returns' => 'Service Returns',
            'service-refunds' => 'Service Refunds',
            'bank-accounts' => 'Bank Accounts',
            'bank-balances' => 'Bank Balances',
            'accounts' => 'Chart of Accounts',
            'journal-entries' => 'Journal Entries',
            'expenses' => 'Expenses',
            'accounting-reports' => 'Accounting Reports',
            'sales-reports' => 'Sales Reports',
            'purchase-reports' => 'Purchase Reports',
            'financial-reports' => 'Financial Reports',
            'inventory-reports' => 'Inventory Reports',
            'reports' => 'General Reports',
            'settings' => 'Settings',
        ];
        
        foreach ($permissions as $permission) {
            $name = $permission->name;
            $parts = explode(' ', $name);
            $module = count($parts) > 1 ? end($parts) : $name;
            $groupName = $moduleMap[$module] ?? ucfirst(str_replace('-', ' ', $module));
            
            if (!isset($groupedPermissions[$groupName])) {
                $groupedPermissions[$groupName] = [];
            }
            $groupedPermissions[$groupName][] = $permission;
        }
        
        ksort($groupedPermissions); // Sort groups alphabetically
        return view('role-management.create', compact('groupedPermissions'));
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        $this->authorizePermission('create roles');
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
        ]);

        $role = Role::create(['name' => $validated['name']]);

        // Assign permissions
        if ($request->has('permissions') && is_array($request->permissions)) {
            $permissionIds = $request->permissions;
            $permissions = Permission::whereIn('id', $permissionIds)->pluck('name')->toArray();
            $role->givePermissionTo($permissions);
        }

        return redirect()->route('role-management.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        $this->authorizePermission('view roles');
        $role->load('permissions');
        $usersWithRole = User::role($role->name)->get();
        return view('role-management.show', compact('role', 'usersWithRole'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        $this->authorizePermission('edit roles');
        $permissions = Permission::all()->sortBy('name');
        
        // Group permissions by module
        $groupedPermissions = [];
        $moduleMap = [
            'dashboard' => 'Dashboard',
            'users' => 'User Management',
            'roles' => 'Role Management',
            'categories' => 'Categories',
            'brands' => 'Brands',
            'product-models' => 'Product Models',
            'products' => 'Products',
            'stock' => 'Stock Management',
            'stock-movements' => 'Stock Movements',
            'suppliers' => 'Suppliers',
            'purchases' => 'Purchases',
            'purchase-invoices' => 'Purchase Invoices',
            'customers' => 'Customers',
            'sales' => 'Sales',
            'invoices' => 'Invoices',
            'services' => 'Services',
            'service-status' => 'Service Status',
            'service-memos' => 'Service Memos',
            'warranties' => 'Warranties',
            'warranty-submissions' => 'Warranty Submissions',
            'warranty-memos' => 'Warranty Memos',
            'payments' => 'Payments',
            'purchase-returns' => 'Purchase Returns',
            'sale-returns' => 'Sale Returns',
            'service-returns' => 'Service Returns',
            'service-refunds' => 'Service Refunds',
            'bank-accounts' => 'Bank Accounts',
            'bank-balances' => 'Bank Balances',
            'accounts' => 'Chart of Accounts',
            'journal-entries' => 'Journal Entries',
            'expenses' => 'Expenses',
            'accounting-reports' => 'Accounting Reports',
            'sales-reports' => 'Sales Reports',
            'purchase-reports' => 'Purchase Reports',
            'financial-reports' => 'Financial Reports',
            'inventory-reports' => 'Inventory Reports',
            'reports' => 'General Reports',
            'settings' => 'Settings',
        ];
        
        foreach ($permissions as $permission) {
            $name = $permission->name;
            $parts = explode(' ', $name);
            $module = count($parts) > 1 ? end($parts) : $name;
            $groupName = $moduleMap[$module] ?? ucfirst(str_replace('-', ' ', $module));
            
            if (!isset($groupedPermissions[$groupName])) {
                $groupedPermissions[$groupName] = [];
            }
            $groupedPermissions[$groupName][] = $permission;
        }
        
        ksort($groupedPermissions); // Sort groups alphabetically
        $role->load('permissions');
        return view('role-management.edit', compact('role', 'groupedPermissions'));
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, Role $role)
    {
        $this->authorizePermission('edit roles');
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
            'permissions' => ['nullable', 'array'],
        ]);

        $role->update(['name' => $validated['name']]);

        // Sync permissions
        if ($request->has('permissions') && is_array($request->permissions)) {
            $permissionIds = $request->permissions;
            $permissions = Permission::whereIn('id', $permissionIds)->pluck('name')->toArray();
            $role->syncPermissions($permissions);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('role-management.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role)
    {
        $this->authorizePermission('delete roles');
        // Check if role is being used by any users
        $usersCount = User::role($role->name)->count();
        
        if ($usersCount > 0) {
            return redirect()->route('role-management.index')
                ->with('error', "Cannot delete role '{$role->name}' because it is assigned to {$usersCount} user(s). Please remove the role from all users first.");
        }

        $role->delete();

        return redirect()->route('role-management.index')
            ->with('success', 'Role deleted successfully.');
    }
}
