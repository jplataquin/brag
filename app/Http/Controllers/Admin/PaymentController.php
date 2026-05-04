<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use App\Mail\ManualPaymentApproved;
use App\Mail\ManualPaymentRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['user', 'collector']);

        // Default to current day if no date range is provided
        if (!$request->filled('date_from') && !$request->filled('date_to')) {
            $request->merge([
                'date_from' => Carbon::today()->toDateString(),
                'date_to' => Carbon::today()->toDateString(),
            ]);
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by Payment Method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by Date Range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by User (Username)
        if ($request->filled('username')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('username', $request->username);
            });
        }

        // Calculate Grand Total for the filtered results (excluding collected)
        $grandTotal = (clone $query)->whereNull('collected_at')->sum('amount');

        // Sorting
        $sortField = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_dir', 'desc');
        $allowedSorts = ['created_at', 'amount', 'diamonds_amount', 'status', 'payment_method'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection);
        }

        $payments = $query->paginate(20)->withQueryString();

        return view('admin.payments.index', compact('payments', 'grandTotal'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['user', 'collector', 'package']);
        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Approve a manual payment.
     */
    public function approve(Request $request, Payment $payment)
    {
        if ($payment->status !== 'pending' || $payment->payment_method !== 'manual') {
            return back()->with('error', 'This payment cannot be approved.');
        }

        try {
            DB::transaction(function () use ($payment) {
                $payment->lockForUpdate();

                $payment->update([
                    'status' => 'completed',
                    'payment_type' => 'manual_approval',
                    'collected_at' => now(),
                    'collected_by' => auth()->id(),
                ]);

                // Add Diamonds to user
                $payment->user->addDiamonds(
                    $payment->diamonds_amount, 
                    'purchased', 
                    "Purchased {$payment->diamonds_amount} Diamonds via Manual Payment (Ref: {$payment->reference})"
                );

                // Send approval email
                try {
                    Mail::to($payment->user->email)->send(new ManualPaymentApproved($payment));
                } catch (\Exception $mailEx) {
                    Log::error("Manual Approval: Failed to send email for {$payment->reference}: " . $mailEx->getMessage());
                }
            });

            return back()->with('success', "Payment {$payment->reference} has been approved.");

        } catch (\Exception $e) {
            Log::error("Manual Approval Error: " . $e->getMessage());
            return back()->with('error', 'An error occurred while approving the payment.');
        }
    }

    /**
     * Reject a manual payment.
     */
    public function reject(Request $request, Payment $payment)
    {
        $request->validate(['reason' => 'nullable|string|max:255']);

        if ($payment->status !== 'pending' || $payment->payment_method !== 'manual') {
            return back()->with('error', 'This payment cannot be rejected.');
        }

        $payment->update(['status' => 'failed']);

        // Send rejection email
        try {
            Mail::to($payment->user->email)->send(new ManualPaymentRejected($payment, $request->reason));
        } catch (\Exception $mailEx) {
            Log::error("Manual Rejection: Failed to send email for {$payment->reference}: " . $mailEx->getMessage());
        }

        return back()->with('success', "Payment {$payment->reference} has been rejected.");
    }

    /**
     * Mass approve manual payments.
     */
    public function massApprove(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) return back()->with('error', 'No payments selected.');

        $payments = Payment::whereIn('id', $ids)
            ->where('status', 'pending')
            ->where('payment_method', 'manual')
            ->get();

        $count = 0;
        foreach ($payments as $payment) {
            // Reusing the same logic (ideally refactor to a service)
            DB::transaction(function () use ($payment, &$count) {
                $payment->lockForUpdate();
                if ($payment->status !== 'pending') return;

                $payment->update([
                    'status' => 'completed',
                    'payment_type' => 'manual_approval',
                    'collected_at' => now(),
                    'collected_by' => auth()->id(),
                ]);

                $payment->user->addDiamonds($payment->diamonds_amount, 'purchased', "Purchased {$payment->diamonds_amount} Diamonds via Manual Payment (Mass Approved) (Ref: {$payment->reference})");
                
                try {
                    Mail::to($payment->user->email)->send(new ManualPaymentApproved($payment));
                } catch (\Exception $e) {}
                
                $count++;
            });
        }

        return back()->with('success', "Successfully approved {$count} payments.");
    }

    /**
     * Mass reject manual payments.
     */
    public function massReject(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) return back()->with('error', 'No payments selected.');

        $count = Payment::whereIn('id', $ids)
            ->where('status', 'pending')
            ->where('payment_method', 'manual')
            ->update(['status' => 'failed']);

        // Note: For simplicity, not sending emails in mass reject here to avoid timeout, 
        // but ideally should be queued.

        return back()->with('success', "Successfully rejected {$count} payments.");
    }

    /**
     * User auto-suggest for filtering.
     */
    public function usersSuggest(Request $request)
    {
        $term = $request->query('term');
        if (strlen($term) < 2) return response()->json([]);

        $usernames = User::where('username', 'like', $term . '%')
            ->limit(10)
            ->pluck('username');

        return response()->json($usernames);
    }
}
