<?php

namespace App\Http\Controllers;

use App\Models\Warranty;
use App\Models\WarrantySubmission;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;

class WarrantySubmissionController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizePermission('view warranty-submissions');
        $query = WarrantySubmission::with(['warranty', 'product', 'sale', 'customer', 'creator', 'assignedTo'])->latest();

        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('memo_number', 'like', "%{$request->search}%")
                  ->orWhere('customer_name', 'like', "%{$request->search}%")
                  ->orWhere('customer_phone', 'like', "%{$request->search}%")
                  ->orWhere('barcode', 'like', "%{$request->search}%");
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('warranty_status')) {
            if ($request->warranty_status === 'active') {
                $query->whereHas('warranty', function($q) {
                    $q->where('status', 'active')->where('end_date', '>=', now());
                });
            } elseif ($request->warranty_status === 'expired') {
                $query->whereHas('warranty', function($q) {
                    $q->where('status', 'expired')->orWhere('end_date', '<', now());
                });
            }
        }

        $submissions = $query->paginate(15)->appends($request->query());
        
        return view('warranties.submissions.index', compact('submissions'));
    }

    public function create(Request $request)
    {
        $this->authorizePermission('create warranty-submissions');
        $warranty = null;
        
        // If barcode is provided, find warranty
        if ($request->has('barcode') && $request->barcode) {
            $warranty = Warranty::byBarcode($request->barcode)
                ->with(['product', 'sale.customer', 'customer'])
                ->first();
        }
        
        $customers = Customer::active()->latest()->get();
        
        return view('warranties.submissions.create', compact('warranty', 'customers'));
    }

    public function store(Request $request)
    {
        $this->authorizePermission('create warranty-submissions');
        $validated = $request->validate([
            'warranty_id' => ['required', 'exists:warranties,id'],
            'barcode' => ['nullable', 'string'],
            'submission_date' => ['required', 'date'],
            'problem_description' => ['required', 'string'],
            'customer_complaint' => ['required', 'string'],
            'condition' => ['required', 'in:excellent,good,fair,poor,damaged'],
            'physical_condition_notes' => ['nullable', 'string'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'customer_address' => ['nullable', 'string'],
            'expected_completion_date' => ['nullable', 'date', 'after_or_equal:submission_date'],
            'internal_notes' => ['nullable', 'string'],
        ]);

        // Get warranty to get sale and product info
        $warranty = Warranty::findOrFail($validated['warranty_id']);
        
        $submission = WarrantySubmission::create([
            'warranty_id' => $validated['warranty_id'],
            'sale_id' => $warranty->sale_id,
            'product_id' => $warranty->product_id,
            'customer_id' => $warranty->customer_id,
            'barcode' => $validated['barcode'] ?? $warranty->barcode,
            'submission_date' => $validated['submission_date'],
            'problem_description' => $validated['problem_description'],
            'customer_complaint' => $validated['customer_complaint'],
            'condition' => $validated['condition'],
            'physical_condition_notes' => $validated['physical_condition_notes'] ?? null,
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'customer_address' => $validated['customer_address'] ?? null,
            'expected_completion_date' => $validated['expected_completion_date'] ?? null,
            'internal_notes' => $validated['internal_notes'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('warranty-submissions.show', $submission)
            ->with('success', 'Warranty submission created successfully.');
    }

    public function show(WarrantySubmission $warrantySubmission)
    {
        $this->authorizePermission('view warranty-submissions');
        $warrantySubmission->load(['warranty.product', 'warranty.sale', 'warranty.customer', 'product', 'sale', 'customer', 'creator', 'assignedTo']);
        return view('warranties.submissions.show', compact('warrantySubmission'));
    }

    public function edit(WarrantySubmission $warrantySubmission)
    {
        $this->authorizePermission('edit warranty-submissions');
        $warrantySubmission->load(['warranty', 'product', 'sale', 'customer']);
        return view('warranties.submissions.edit', compact('warrantySubmission'));
    }

    public function update(Request $request, WarrantySubmission $warrantySubmission)
    {
        $this->authorizePermission('edit warranty-submissions');
        $validated = $request->validate([
            'status' => ['required', 'in:pending,received,in_progress,completed,returned,cancelled'],
            'service_notes' => ['nullable', 'string'],
            'internal_notes' => ['nullable', 'string'],
            'service_charge' => ['nullable', 'numeric', 'min:0'],
            'expected_completion_date' => ['nullable', 'date'],
            'completion_date' => ['nullable', 'date'],
            'return_date' => ['nullable', 'date'],
            'assigned_to' => ['nullable', 'exists:users,id'],
        ]);
        
        // Convert empty string to null for assigned_to
        if (empty($validated['assigned_to'])) {
            $validated['assigned_to'] = null;
        }

        $warrantySubmission->update($validated);

        return redirect()->route('warranty-submissions.show', $warrantySubmission)
            ->with('success', 'Warranty submission updated successfully.');
    }

    public function destroy(WarrantySubmission $warrantySubmission)
    {
        $this->authorizePermission('delete warranty-submissions');
        if ($warrantySubmission->status !== 'pending') {
            return redirect()->route('warranty-submissions.index')
                ->with('error', 'Cannot delete submission that is not pending.');
        }

        $warrantySubmission->delete();

        return redirect()->route('warranty-submissions.index')
            ->with('success', 'Warranty submission deleted successfully.');
    }

    public function printMemo(WarrantySubmission $warrantySubmission)
    {
        $this->authorizePermission('print warranty-memos');
        $warrantySubmission->load(['warranty.product', 'warranty.sale', 'product', 'sale', 'customer', 'creator']);
        return view('warranties.submissions.memo', compact('warrantySubmission'));
    }

    public function getWarrantyByBarcode(Request $request)
    {
        // Check permission - user needs to be able to create warranty submissions to search warranties
        if (!auth()->user()->can('create warranty-submissions')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $barcode = $request->get('barcode');
        
        if (!$barcode) {
            return response()->json(['error' => 'Barcode is required'], 400);
        }

        $warranty = Warranty::byBarcode($barcode)
            ->with(['product', 'sale.customer', 'customer'])
            ->first();

        if (!$warranty) {
            return response()->json(['error' => 'No warranty found for this barcode'], 404);
        }

        return response()->json([
            'warranty' => [
                'id' => $warranty->id,
                'barcode' => $warranty->barcode,
                'product_name' => $warranty->product->name,
                'product_id' => $warranty->product_id,
                'sale_id' => $warranty->sale_id,
                'customer_id' => $warranty->customer_id,
                'customer_name' => $warranty->customer ? $warranty->customer->name : ($warranty->sale->customer_name ?? 'Walk-in Customer'),
                'customer_phone' => $warranty->customer ? $warranty->customer->phone : ($warranty->sale->customer_phone ?? ''),
                'customer_address' => $warranty->customer ? $warranty->customer->address : ($warranty->sale->customer_address ?? ''),
                'invoice_number' => $warranty->sale->invoice_number,
                'start_date' => $warranty->start_date->format('Y-m-d'),
                'end_date' => $warranty->end_date->format('Y-m-d'),
                'is_active' => $warranty->isActive(),
                'days_remaining' => $warranty->daysRemaining(),
            ],
        ]);
    }
}
