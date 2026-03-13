<?php

namespace App\Http\Controllers;

use App\Models\ServiceReturn;
use App\Models\Service;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceReturnController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizePermission('view service-returns');
        $query = ServiceReturn::with(['service', 'creator'])->latest();

        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('return_number', 'like', "%{$request->search}%")
                  ->orWhereHas('service', function($sq) use ($request) {
                      $sq->where('service_number', 'like', "%{$request->search}%")
                        ->orWhere('customer_name', 'like', "%{$request->search}%");
                  });
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $returns = $query->paginate(15)->appends($request->query());
        
        return view('returns.service-returns.index', compact('returns'));
    }

    public function create(Request $request)
    {
        $this->authorizePermission('create service-returns');
        $serviceId = $request->get('service_id');
        $service = null;
        
        if ($serviceId) {
            $service = Service::findOrFail($serviceId);
        }
        
        // Fetch services that can be returned (completed or delivered, not already returned)
        // Exclude services that already have a completed return
        $services = Service::where(function($query) use ($service) {
                $query->whereIn('status', ['completed', 'delivered'])
                    ->whereDoesntHave('returns', function($q) {
                        $q->whereIn('status', ['approved', 'refunded']);
                    });
                
                // If a service is pre-selected, make sure it's included even if it has returns
                if ($service) {
                    $query->orWhere('id', $service->id);
                }
            })
            ->latest()
            ->get();
        
        return view('returns.service-returns.create', compact('service', 'services'));
    }

    public function store(Request $request)
    {
        $this->authorizePermission('create service-returns');
        $validated = $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'return_date' => ['required', 'date'],
            'reason' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
            'refund_amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $service = Service::findOrFail($validated['service_id']);

        $return = ServiceReturn::create([
            'service_id' => $validated['service_id'],
            'return_date' => $validated['return_date'],
            'reason' => $validated['reason'],
            'notes' => $validated['notes'] ?? null,
            'refund_amount' => $validated['refund_amount'] ?? 0,
            'status' => 'pending',
        ]);

        return redirect()->route('service-returns.show', $return)
            ->with('success', 'Service return created successfully.');
    }

    public function show(ServiceReturn $serviceReturn)
    {
        $this->authorizePermission('view service-returns');
        $serviceReturn->load(['service', 'creator', 'approver']);
        return view('returns.service-returns.show', compact('serviceReturn'));
    }

    public function edit(ServiceReturn $serviceReturn)
    {
        $this->authorizePermission('edit service-returns');
        if ($serviceReturn->status !== 'pending') {
            return redirect()->route('service-returns.show', $serviceReturn)
                ->with('error', 'Only pending returns can be edited.');
        }

        return view('returns.service-returns.edit', compact('serviceReturn'));
    }

    public function update(Request $request, ServiceReturn $serviceReturn)
    {
        $this->authorizePermission('edit service-returns');
        if ($serviceReturn->status !== 'pending') {
            return redirect()->route('service-returns.show', $serviceReturn)
                ->with('error', 'Only pending returns can be edited.');
        }

        $validated = $request->validate([
            'return_date' => ['required', 'date'],
            'reason' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
            'refund_amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $serviceReturn->update($validated);

        return redirect()->route('service-returns.show', $serviceReturn)
            ->with('success', 'Service return updated successfully.');
    }

    public function destroy(ServiceReturn $serviceReturn)
    {
        $this->authorizePermission('delete service-returns');
        if ($serviceReturn->status !== 'pending') {
            return redirect()->route('service-returns.index')
                ->with('error', 'Only pending returns can be deleted.');
        }

        $serviceReturn->delete();

        return redirect()->route('service-returns.index')
            ->with('success', 'Service return deleted successfully.');
    }

    public function approve(ServiceReturn $serviceReturn)
    {
        $this->authorizePermission('approve service-returns');
        if ($serviceReturn->status !== 'pending') {
            return back()->with('error', 'Only pending returns can be approved.');
        }

        $serviceReturn->approve();

        return back()->with('success', 'Service return approved successfully.');
    }

    public function complete(ServiceReturn $serviceReturn)
    {
        $this->authorizePermission('complete service-returns');
        if ($serviceReturn->status !== 'approved') {
            return back()->with('error', 'Return must be approved before completion.');
        }

        try {
            $serviceReturn->complete();
            // Create journal entry for completed return if refund amount > 0
            if ($serviceReturn->refund_amount > 0) {
                AccountingService::recordServiceReturn($serviceReturn);
            }
            return back()->with('success', 'Service return completed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function processRefund(ServiceReturn $serviceReturn)
    {
        $this->authorizePermission('process service-refunds');
        if ($serviceReturn->status !== 'completed') {
            return back()->with('error', 'Return must be completed before processing refund.');
        }

        try {
            $serviceReturn->processRefund();
            return back()->with('success', 'Refund processed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
