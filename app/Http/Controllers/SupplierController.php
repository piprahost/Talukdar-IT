<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizePermission('view suppliers');
        $query = Supplier::latest();

        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('company_name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $suppliers = $query
            ->withCount('purchaseOrders')
            ->withSum('purchaseOrders', 'total_amount')
            ->withSum('purchaseOrders', 'due_amount')
            ->paginate(15)->appends($request->query());

        $stats = [
            'total'      => \App\Models\Supplier::count(),
            'active'     => \App\Models\Supplier::where('is_active', true)->count(),
            'total_due'  => \App\Models\Purchase::sum('due_amount'),
            'total_spent'=> \App\Models\Purchase::sum('total_amount'),
        ];

        return view('purchases.suppliers.index', compact('suppliers', 'stats'));
    }

    public function create()
    {
        $this->authorizePermission('create suppliers');
        return view('purchases.suppliers.create');
    }

    public function store(Request $request)
    {
        $this->authorizePermission('create suppliers');
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'tax_id' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->has('is_active');
        Supplier::create($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    public function show(Supplier $supplier)
    {
        $this->authorizePermission('view suppliers');
        // Load purchase orders with financial data
        $purchaseOrders = $supplier->purchaseOrders()
            ->selectRaw('
                SUM(total_amount) as total_purchases,
                SUM(paid_amount) as total_paid,
                SUM(due_amount) as total_due,
                COUNT(*) as total_orders
            ')
            ->first();
        
        // Get summary statistics
        $summary = [
            'total_orders' => $supplier->purchaseOrders()->count(),
            'total_purchases' => $supplier->purchaseOrders()->sum('total_amount') ?? 0,
            'total_paid' => $supplier->purchaseOrders()->sum('paid_amount') ?? 0,
            'total_due' => $supplier->purchaseOrders()->sum('due_amount') ?? 0,
            'unpaid_orders' => $supplier->purchaseOrders()->where('payment_status', 'unpaid')->count(),
            'partial_orders' => $supplier->purchaseOrders()->where('payment_status', 'partial')->count(),
            'paid_orders' => $supplier->purchaseOrders()->where('payment_status', 'paid')->count(),
        ];
        
        // Get recent purchase orders
        $recentPurchases = $supplier->purchaseOrders()
            ->with('creator')
            ->latest()
            ->limit(10)
            ->get();
        
        // Get all purchase orders for transaction table
        $allPurchases = $supplier->purchaseOrders()
            ->with('creator')
            ->latest()
            ->paginate(15);
        
        return view('purchases.suppliers.show', compact('supplier', 'summary', 'recentPurchases', 'allPurchases'));
    }

    public function edit(Supplier $supplier)
    {
        $this->authorizePermission('edit suppliers');
        return view('purchases.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $this->authorizePermission('edit suppliers');
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'tax_id' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->has('is_active');
        $supplier->update($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->purchaseOrders()->count() > 0) {
            return redirect()->route('suppliers.index')
                ->with('error', 'Cannot delete supplier with existing purchase orders.');
        }

        $supplier->forceDelete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }
}
