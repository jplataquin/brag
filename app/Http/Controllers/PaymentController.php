<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\HitPayService;
use App\Mail\ShardPurchaseReceipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    protected $hitPayService;

    public function __construct(HitPayService $hitPayService)
    {
        $this->hitPayService = $hitPayService;
    }

    /**
     * Initiate the checkout process for a Shard package.
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'package_id' => 'required|string',
        ]);

        $packageId = $request->input('package_id');
        $packages = config('shards.packages');

        if (!array_key_exists($packageId, $packages)) {
            return back()->with('error', 'Invalid Shard package selected.');
        }

        $package = $packages[$packageId];
        $user = Auth::user();

        // Create a unique reference for our system
        $reference = 'BRAG-' . strtoupper(uniqid()) . '-' . time();

        // Save pending payment record in DB
        $payment = Payment::create([
            'user_id' => $user->id,
            'reference' => $reference,
            'amount' => $package['price'],
            'currency' => $package['currency'],
            'shards_amount' => $package['shards'],
            'status' => 'pending',
        ]);

        try {
            // Generate HitPay Request
            $hitPayResponse = $this->hitPayService->createPaymentRequest(
                $payment->amount,
                $payment->currency,
                $payment->reference,
                $user->email,
                $user->username,
                route('payments.callback'), // User redirected here after
                route('payments.webhook')   // Server-to-server webhook
            );

            // Update with HitPay ID if we want
            if (isset($hitPayResponse['id'])) {
                $payment->update(['hitpay_id' => $hitPayResponse['id']]);
            }

            // Redirect to HitPay Checkout URL
            return redirect()->away($hitPayResponse['url']);

        } catch (\Exception $e) {
            Log::error('HitPay Checkout Error: ' . $e->getMessage());
            $payment->update(['status' => 'failed']);
            return back()->with('error', 'Unable to initiate payment at this time. Please try again later.');
        }
    }

    /**
     * Handle the user returning from the HitPay checkout page.
     * Note: This is purely for UI feedback. Actual fulfillment is done via Webhook.
     */
    public function callback(Request $request)
    {
        $reference = $request->query('reference');
        $status = $request->query('status'); // 'completed', 'canceled', etc.

        if ($status === 'completed') {
            return redirect()->route('wallet.index')->with('success', 'Payment successful! Your shards will be added to your wallet shortly.');
        }

        return redirect()->route('wallet.index')->with('error', 'Payment was cancelled or failed.');
    }

    /**
     * Server-to-server webhook from HitPay.
     * This is where we MUST fulfill the order.
     */
    public function webhook(Request $request)
    {
        $payload = $request->all();

        // 1. Verify Signature to prevent spoofing
        if (!$this->hitPayService->verifySignature($payload)) {
            Log::warning('HitPay Webhook: Invalid Signature', $payload);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $reference = $payload['reference_number'] ?? null;
        $status = $payload['status'] ?? null; // e.g., 'completed'

        if (!$reference) {
            return response()->json(['error' => 'No reference provided'], 400);
        }

        try {
            // 2. Wrap fulfillment in a Database Transaction for integrity
            DB::transaction(function () use ($reference, $status) {
                // Lock the row to prevent race conditions (e.g. double webhook delivery)
                $payment = Payment::where('reference', $reference)->lockForUpdate()->firstOrFail();

                // If already completed, nothing to do
                if ($payment->status === 'completed') {
                    return;
                }

                if ($status === 'completed') {
                    $payment->update(['status' => 'completed']);

                    // Add Shards to user
                    $payment->user->addShards(
                        $payment->shards_amount, 
                        'purchased', 
                        "Purchased {$payment->shards_amount} Shards via HitPay (Ref: {$payment->reference})"
                    );

                    // Send email receipt
                    try {
                        Mail::to($payment->user->email)->send(new ShardPurchaseReceipt($payment));
                    } catch (\Exception $mailEx) {
                        Log::error('HitPay Webhook: Failed to send receipt email: ' . $mailEx->getMessage());
                        // We still consider the transaction successful even if email fails
                    }

                } elseif (in_array($status, ['failed', 'canceled', 'refunded'])) {
                    $payment->update(['status' => $status]);
                }
            });

            return response()->json(['message' => 'Webhook processed successfully']);

        } catch (\Exception $e) {
            Log::error('HitPay Webhook Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
