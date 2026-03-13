<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\BankAccount;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of services.
     */
    public function index(Request $request)
    {
        $this->authorizePermission('view services');
        $query = Service::with('creator')->latest();
        
        // Search by barcode/serial number or customer phone
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('serial_number', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('service_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('product_name', 'like', "%{$search}%");
            });
        }
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Filter by payment status
        if ($request->has('payment_status') && $request->payment_status) {
            if ($request->payment_status === 'paid') {
                $query->where('due_amount', 0)->where('paid_amount', '>', 0);
            } elseif ($request->payment_status === 'unpaid') {
                $query->where('paid_amount', 0);
            } elseif ($request->payment_status === 'partial') {
                $query->where('paid_amount', '>', 0)->where('due_amount', '>', 0);
            }
        }
        
        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('receive_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('receive_date', '<=', $request->date_to);
        }
        
        $services = $query->paginate(15)->appends($request->query());
        return view('services.index', compact('services'));
    }

    /**
     * Show the form for creating a new service.
     */
    public function create()
    {
        $this->authorizePermission('create services');
        $bankAccounts = BankAccount::active()->orderBy('account_name')->get();
        return view('services.create', compact('bankAccounts'));
    }

    /**
     * Store a newly created service.
     */
    public function store(Request $request)
    {
        $this->authorizePermission('create services');
        $validated = $request->validate([
            'product_name' => ['required', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'problem_notes' => ['nullable', 'string'],
            'service_notes' => ['nullable', 'string'],
            'service_cost' => ['required', 'numeric', 'min:0'],
            'receive_date' => ['required', 'date'],
            'delivery_date' => ['nullable', 'date', 'after_or_equal:receive_date'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'customer_address' => ['nullable', 'string'],
            'paid_amount' => ['required', 'numeric', 'min:0'],
            'due_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:cash,card,mobile_banking,bank_transfer,cheque,other'],
            'bank_account_id' => ['nullable', 'exists:bank_accounts,id', 'required_if:payment_method,card,mobile_banking,bank_transfer,cheque'],
            'status' => ['required', 'in:pending,in_progress,completed,delivered,cancelled'],
            'internal_notes' => ['nullable', 'string'],
        ]);

        // Calculate due amount if not provided
        if (!isset($validated['due_amount'])) {
            $validated['due_amount'] = max(0, $validated['service_cost'] - $validated['paid_amount']);
        }

        $validated['created_by'] = auth()->id();

        Service::create($validated);

        return redirect()->route('services.index')
            ->with('success', 'Service order created successfully.');
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service)
    {
        $this->authorizePermission('view services');
        $service->load('creator', 'returns', 'bankAccount');
        return view('services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit(Service $service)
    {
        $this->authorizePermission('edit services');
        $bankAccounts = BankAccount::active()->orderBy('account_name')->get();
        return view('services.edit', compact('service', 'bankAccounts'));
    }

    /**
     * Update the specified service.
     */
    public function update(Request $request, Service $service)
    {
        $this->authorizePermission('edit services');
        $validated = $request->validate([
            'product_name' => ['required', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'problem_notes' => ['nullable', 'string'],
            'service_notes' => ['nullable', 'string'],
            'service_cost' => ['required', 'numeric', 'min:0'],
            'receive_date' => ['required', 'date'],
            'delivery_date' => ['nullable', 'date', 'after_or_equal:receive_date'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'customer_address' => ['nullable', 'string'],
            'paid_amount' => ['required', 'numeric', 'min:0'],
            'due_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:cash,card,mobile_banking,bank_transfer,cheque,other'],
            'bank_account_id' => ['nullable', 'exists:bank_accounts,id', 'required_if:payment_method,card,mobile_banking,bank_transfer,cheque'],
            'status' => ['required', 'in:pending,in_progress,completed,delivered,cancelled'],
            'internal_notes' => ['nullable', 'string'],
        ]);

        // Calculate due amount if not provided
        if (!isset($validated['due_amount'])) {
            $validated['due_amount'] = max(0, $validated['service_cost'] - $validated['paid_amount']);
        }

        $service->update($validated);

        return redirect()->route('services.index')
            ->with('success', 'Service order updated successfully.');
    }

    /**
     * Remove the specified service.
     */
    public function destroy(Service $service)
    {
        $this->authorizePermission('delete services');
        $service->delete();

        return redirect()->route('services.index')
            ->with('success', 'Service order deleted successfully.');
    }

    /**
     * Print service memo/order
     */
    public function print(Service $service)
    {
        $this->authorizePermission('print service-memos');
        $service->load('creator');
        return view('services.print', compact('service'));
    }

    /**
     * Quick update service status
     */
    public function updateStatus(Request $request, Service $service)
    {
        $this->authorizePermission('update service-status');
        $validated = $request->validate([
            'status' => 'required|string|in:pending,in_progress,completed,delivered,cancelled',
        ]);

        $service->update(['status' => $validated['status']]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'status' => $service->status,
                'status_label' => ucfirst(str_replace('_', ' ', $service->status))
            ]);
        }

        return redirect()->back()->with('success', 'Service status updated successfully!');
    }
}
