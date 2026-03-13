<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizePermission('view customers');
        $query = Customer::latest();

        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%")
                  ->orWhere('mobile', 'like', "%{$request->search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $customers = $query->withCount('sales')->paginate(15)->appends($request->query());
        return view('sales.customers.index', compact('customers'));
    }

    public function create()
    {
        $this->authorizePermission('create customers');
        return view('sales.customers.create');
    }

    public function store(Request $request)
    {
        $this->authorizePermission('create customers');
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
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
        Customer::create($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer)
    {
        $this->authorizePermission('view customers');
        // Get summary statistics
        $summary = [
            'total_sales' => $customer->sales()->count(),
            'total_amount' => $customer->sales()->sum('total_amount') ?? 0,
            'total_paid' => $customer->sales()->sum('paid_amount') ?? 0,
            'total_due' => $customer->sales()->sum('due_amount') ?? 0,
            'unpaid_sales' => $customer->sales()->where('payment_status', 'unpaid')->count(),
            'partial_sales' => $customer->sales()->where('payment_status', 'partial')->count(),
            'paid_sales' => $customer->sales()->where('payment_status', 'paid')->count(),
        ];
        
        // Get recent sales
        $recentSales = $customer->sales()
            ->with('creator')
            ->latest()
            ->limit(10)
            ->get();
        
        // Get all sales for transaction table
        $allSales = $customer->sales()
            ->with('creator')
            ->latest()
            ->paginate(15);
        
        return view('sales.customers.show', compact('customer', 'summary', 'recentSales', 'allSales'));
    }

    public function edit(Customer $customer)
    {
        $this->authorizePermission('edit customers');
        return view('sales.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $this->authorizePermission('edit customers');
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
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
        $customer->update($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $this->authorizePermission('delete customers');
        if ($customer->sales()->count() > 0) {
            return redirect()->route('customers.index')
                ->with('error', 'Cannot delete customer with existing sales.');
        }

        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    }
}
