<?php

namespace App\Http\Controllers;

use App\Models\Warranty;
use App\Models\Product;
use Illuminate\Http\Request;

class WarrantyController extends Controller
{
    /**
     * Show warranty verification page
     */
    public function verify()
    {
        $this->authorizePermission('verify warranties');
        return view('warranties.verify');
    }

    /**
     * Verify warranty by barcode
     */
    public function verifyByBarcode(Request $request)
    {
        $this->authorizePermission('verify warranties');
        $request->validate([
            'barcode' => ['required', 'string'],
        ]);

        $barcode = $request->input('barcode');
        
        // Find warranty by barcode
        $warranty = Warranty::byBarcode($barcode)
            ->with(['product', 'sale.customer', 'saleItem.sale'])
            ->first();

        if (!$warranty) {
            return back()->with('error', 'No warranty found for this barcode. Please verify the barcode.');
        }

        // Update status if expired
        if ($warranty->end_date->isPast() && $warranty->status === 'active') {
            $warranty->status = 'expired';
            $warranty->save();
        }

        return view('warranties.result', compact('warranty'));
    }

    /**
     * Get warranty details via AJAX (for API-like responses)
     */
    public function getWarrantyByBarcode(Request $request)
    {
        $this->authorizePermission('verify warranties');
        $request->validate([
            'barcode' => ['required', 'string'],
        ]);

        $barcode = $request->input('barcode');
        
        $warranty = Warranty::byBarcode($barcode)
            ->with(['product', 'sale.customer'])
            ->first();

        if (!$warranty) {
            return response()->json([
                'success' => false,
                'message' => 'No warranty found for this barcode.',
            ], 404);
        }

        // Update status if expired
        if ($warranty->end_date->isPast() && $warranty->status === 'active') {
            $warranty->status = 'expired';
            $warranty->save();
        }

        return response()->json([
            'success' => true,
            'warranty' => [
                'id' => $warranty->id,
                'barcode' => $warranty->barcode,
                'product_name' => $warranty->product->name,
                'customer_name' => $warranty->customer ? $warranty->customer->name : ($warranty->sale->customer_name ?? 'Walk-in Customer'),
                'start_date' => $warranty->start_date->format('Y-m-d'),
                'end_date' => $warranty->end_date->format('Y-m-d'),
                'warranty_period_days' => $warranty->warranty_period_days,
                'status' => $warranty->status,
                'is_active' => $warranty->isActive(),
                'days_remaining' => $warranty->daysRemaining(),
                'days_expired' => $warranty->daysExpired(),
                'progress_percentage' => $warranty->getProgressPercentage(),
            ],
        ]);
    }

    /**
     * List all warranties with filters
     */
    public function index(Request $request)
    {
        $this->authorizePermission('view warranties');
        $query = Warranty::with(['product', 'sale.customer', 'customer']);

        if ($request->has('status') && $request->status) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'expired') {
                $query->expired();
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->has('barcode') && $request->barcode) {
            $query->byBarcode($request->barcode);
        }

        if ($request->has('customer_id') && $request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        $warranties = $query->latest('created_at')->paginate(20);

        $stats = [
            'total'    => \App\Models\Warranty::count(),
            'active'   => \App\Models\Warranty::where('status', 'active')->whereDate('end_date', '>=', now())->count(),
            'expired'  => \App\Models\Warranty::where('status', 'expired')->orWhereDate('end_date', '<', now())->count(),
            'expiring' => \App\Models\Warranty::where('status', 'active')
                            ->whereDate('end_date', '>=', now())
                            ->whereDate('end_date', '<=', now()->addDays(30))
                            ->count(),
        ];

        return view('warranties.index', compact('warranties', 'stats'));
    }

    /**
     * Show warranty details
     */
    public function show(Warranty $warranty)
    {
        $this->authorizePermission('view warranties');
        $warranty->load(['product', 'sale.customer', 'saleItem', 'customer']);
        return view('warranties.show', compact('warranty'));
    }
}
