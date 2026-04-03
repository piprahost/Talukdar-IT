<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Customer;
use App\Models\Supplier;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizePermission('view payments');
        $query = Payment::withStandardRelations()->latest();

        if ($request->has('type') && $request->type) {
            $query->where('payment_type', $request->type);
        }

        if ($request->filled('sale_id')) {
            $query->where('sale_id', $request->sale_id);
        }
        if ($request->filled('purchase_id')) {
            $query->where('purchase_id', $request->purchase_id);
        }

        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('payment_number', 'like', "%{$request->search}%")
                  ->orWhere('reference_number', 'like', "%{$request->search}%")
                  ->orWhereHas('customer', function($customerQuery) use ($request) {
                      $customerQuery->where('name', 'like', "%{$request->search}%")
                                    ->orWhere('phone', 'like', "%{$request->search}%");
                  })
                  ->orWhereHas('supplier', function($supplierQuery) use ($request) {
                      $supplierQuery->where('name', 'like', "%{$request->search}%")
                                    ->orWhere('phone', 'like', "%{$request->search}%");
                  });
            });
        }

        if ($request->has('payment_method') && $request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->where('payment_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('payment_date', '<=', $request->date_to);
        }

        $payments = $query->paginate(20)->appends($request->query());
        
        $totalCustomerPayments = Payment::where('payment_type', 'customer')->sum('amount');
        $totalSupplierPayments = Payment::where('payment_type', 'supplier')->sum('amount');
        
        return view('payments.index', compact('payments', 'totalCustomerPayments', 'totalSupplierPayments'));
    }

    public function create(Request $request)
    {
        $this->authorizePermission('create payments');
        $type = $request->get('type', 'customer'); // customer or supplier
        
        if ($type === 'customer') {
            $sales = Sale::where('status', 'completed')
                ->where('due_amount', '>', 0)
                ->with('customer')
                ->orderBy('sale_date', 'desc')
                ->get();
            
            $defaultPaymentMethod = function_exists('settings') ? (settings('payments.default_payment_method') ?: 'cash') : 'cash';
            return view('payments.create', compact('type', 'sales', 'defaultPaymentMethod'));
        } else {
            $purchases = Purchase::where('due_amount', '>', 0)
                ->with('supplier')
                ->orderBy('order_date', 'desc')
                ->get();
            $defaultPaymentMethod = function_exists('settings') ? (settings('payments.default_payment_method') ?: 'cash') : 'cash';
            return view('payments.create', compact('type', 'purchases', 'defaultPaymentMethod'));
        }
    }

    public function store(Request $request)
    {
        $this->authorizePermission('create payments');
        $validated = $request->validate([
            'payment_type' => ['required', 'in:customer,supplier'],
            'sale_id' => ['required_if:payment_type,customer', 'nullable', 'exists:sales,id'],
            'purchase_id' => ['required_if:payment_type,supplier', 'nullable', 'exists:purchase_orders,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:cash,card,mobile_banking,bank_transfer,cheque,other'],
            // When recording generic payments, we infer bank account from sale/purchase;
            // later we can expose an explicit bank account selector here.
            'reference_number' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        // Validate that the amount doesn't exceed due amount
        if ($validated['payment_type'] === 'customer' && $validated['sale_id']) {
            $sale = Sale::findOrFail($validated['sale_id']);
            if ($validated['amount'] > $sale->due_amount) {
                return back()->withErrors(['amount' => 'Payment amount cannot exceed due amount (৳' . number_format($sale->due_amount, 2) . ')'])->withInput();
            }
            
            $validated['customer_id'] = $sale->customer_id;
            $validated['bank_account_id'] = $sale->bank_account_id;
        } elseif ($validated['payment_type'] === 'supplier' && $validated['purchase_id']) {
            $purchase = Purchase::findOrFail($validated['purchase_id']);
            if ($validated['amount'] > $purchase->due_amount) {
                return back()->withErrors(['amount' => 'Payment amount cannot exceed due amount (৳' . number_format($purchase->due_amount, 2) . ')'])->withInput();
            }
            
            $validated['supplier_id'] = $purchase->supplier_id;
            $validated['bank_account_id'] = $purchase->bank_account_id;
        }

        $payment = DB::transaction(function () use ($validated) {
            $payment = Payment::create($validated);
            
            // Create journal entry for payment
            AccountingService::recordPayment($payment);
            
            return $payment;
        });

        // Optional SMS notifications
        if ($payment->payment_type === 'customer') {
            \App\Services\SmsNotificationService::customerPayment($payment);
        } elseif ($payment->payment_type === 'supplier') {
            \App\Services\SmsNotificationService::supplierPayment($payment);
        }

        $redirectRoute = $validated['payment_type'] === 'customer' 
            ? route('sales.show', $validated['sale_id'])
            : route('purchases.show', $validated['purchase_id']);

        return redirect($redirectRoute)
            ->with('success', 'Payment recorded successfully.');
    }

    public function show(Payment $payment)
    {
        $this->authorizePermission('view payments');
        $payment->load(array_merge(Payment::getStandardRelations(), ['sale.customer', 'purchase.supplier', 'service']));
        return view('payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        $this->authorizePermission('edit payments');
        $payment->load(['sale', 'purchase', 'service']);
        return view('payments.edit', compact('payment'));
    }

    public function update(Request $request, Payment $payment)
    {
        $this->authorizePermission('edit payments');
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:cash,card,mobile_banking,bank_transfer,cheque,other'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        // Validate amount doesn't exceed due + current payment amount
        if ($payment->payment_type === 'customer' && $payment->sale_id) {
            $sale = Sale::findOrFail($payment->sale_id);
            $otherPaymentsTotal = Payment::where('sale_id', $sale->id)
                ->where('id', '!=', $payment->id)
                ->sum('amount');
            $maxAllowed = $sale->total_amount - $otherPaymentsTotal;
            if ($validated['amount'] > $maxAllowed) {
                return back()->withErrors(['amount' => 'Payment amount cannot exceed due amount (৳' . number_format($maxAllowed, 2) . ')'])->withInput();
            }
        } elseif ($payment->payment_type === 'customer' && $payment->service_id) {
            $service = \App\Models\Service::findOrFail($payment->service_id);
            $otherPaymentsTotal = Payment::where('service_id', $service->id)
                ->where('id', '!=', $payment->id)
                ->sum('amount');
            $maxAllowed = (float) $service->service_cost - $otherPaymentsTotal;
            if ($validated['amount'] > $maxAllowed) {
                return back()->withErrors(['amount' => 'Payment amount cannot exceed due amount (৳' . number_format($maxAllowed, 2) . ')'])->withInput();
            }
        } elseif ($payment->payment_type === 'supplier' && $payment->purchase_id) {
            $purchase = Purchase::findOrFail($payment->purchase_id);
            $otherPaymentsTotal = Payment::where('purchase_id', $purchase->id)
                ->where('id', '!=', $payment->id)
                ->sum('amount');
            $maxAllowed = $purchase->total_amount - $otherPaymentsTotal;
            
            if ($validated['amount'] > $maxAllowed) {
                return back()->withErrors(['amount' => 'Payment amount cannot exceed due amount (৳' . number_format($maxAllowed, 2) . ')'])->withInput();
            }
        }

        DB::transaction(function () use ($payment, $validated) {
            $payment->update($validated);
            if ($payment->sale_id || $payment->purchase_id) {
                AccountingService::recordPayment($payment);
            }
            if ($payment->service_id) {
                AccountingService::recordService($payment->service->fresh());
            }
        });

        $redirectRoute = $payment->payment_type === 'customer' && $payment->sale_id
            ? route('sales.show', $payment->sale_id)
            : ($payment->payment_type === 'customer' && $payment->service_id
                ? route('services.show', $payment->service_id)
                : ($payment->purchase_id ? route('purchases.show', $payment->purchase_id) : route('payments.index')));

        return redirect($redirectRoute)
            ->with('success', 'Payment updated successfully.');
    }

    public function destroy(Payment $payment)
    {
        $this->authorizePermission('delete payments');
        $saleId = $payment->sale_id;
        $purchaseId = $payment->purchase_id;
        $serviceId = $payment->service_id;
        $type = $payment->payment_type;

        DB::transaction(function () use ($payment) {
            AccountingService::deleteJournalEntry('payment', $payment->id);
            $payment->forceDelete();
        });

        if ($type === 'customer' && $saleId) {
            return redirect()->route('sales.show', $saleId)
                ->with('success', 'Payment deleted successfully.');
        }
        if ($type === 'customer' && $serviceId) {
            return redirect()->route('services.show', $serviceId)
                ->with('success', 'Payment deleted successfully.');
        }
        if ($type === 'supplier' && $purchaseId) {
            return redirect()->route('purchases.show', $purchaseId)
                ->with('success', 'Payment deleted successfully.');
        }
        return redirect()->route('payments.index')
            ->with('success', 'Payment deleted successfully.');
    }

    public function quickPayment(Request $request)
    {
        $this->authorizePermission('create payments');
        // Quick payment from sale/purchase show page
        $validated = $request->validate([
            'type' => ['required', 'in:customer,supplier'],
            'reference_id' => ['required', 'integer'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'in:cash,card,mobile_banking,bank_transfer,cheque,other'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($validated['type'] === 'customer') {
            $sale = Sale::findOrFail($validated['reference_id']);
            if ($validated['amount'] > $sale->due_amount) {
                return back()->withErrors(['amount' => 'Payment amount cannot exceed due amount.'])->withInput();
            }
            
            $paymentData = [
                'payment_type' => 'customer',
                'sale_id' => $sale->id,
                'customer_id' => $sale->customer_id,
                'amount' => $validated['amount'],
                'payment_date' => now()->format('Y-m-d'),
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ];
        } else {
            $purchase = Purchase::findOrFail($validated['reference_id']);
            if ($validated['amount'] > $purchase->due_amount) {
                return back()->withErrors(['amount' => 'Payment amount cannot exceed due amount.'])->withInput();
            }
            
            $paymentData = [
                'payment_type' => 'supplier',
                'purchase_id' => $purchase->id,
                'supplier_id' => $purchase->supplier_id,
                'amount' => $validated['amount'],
                'payment_date' => now()->format('Y-m-d'),
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ];
        }

        $payment = DB::transaction(function () use ($paymentData) {
            $payment = Payment::create($paymentData);
            
            // Create journal entry for payment
            AccountingService::recordPayment($payment);
            
            return $payment;
        });

        return back()->with('success', 'Payment recorded successfully.');
    }
}
