<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('user');

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by Payment Type
        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }

        // Filter by Date Range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by User (Username or Email)
        if ($request->filled('user_search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('username', 'like', '%' . $request->user_search . '%')
                  ->orWhere('email', 'like', '%' . $request->user_search . '%');
            });
        }

        // Sorting
        $sortField = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_dir', 'desc');
        
        $allowedSorts = ['created_at', 'amount', 'shards_amount', 'status', 'payment_type'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection);
        }

        // Calculate totals for the filtered results
        $totalsData = (clone $query)->selectRaw('
            SUM(amount) as total_gross,
            SUM(fees) as total_fees,
            SUM(net_amount) as total_net
        ')->first();

        $payments = $query->paginate(20)->withQueryString();

        // Get unique payment types for the dropdown
        $paymentTypes = Payment::whereNotNull('payment_type')
            ->distinct()
            ->pluck('payment_type');

        return view('admin.payments.index', compact('payments', 'paymentTypes', 'totalsData'));
    }

    public function show(Payment $payment)
    {
        $payment->load('user');
        return view('admin.payments.show', compact('payment'));
    }
}
