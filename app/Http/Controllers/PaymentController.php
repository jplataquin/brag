<?php

namespace App\Http\Controllers;

use App\Models\DiamondPackage;
use App\Models\Payment;
use App\Models\ManualPaymentAgreement;
use App\Services\HitPayService;
use App\Mail\DiamondPurchaseReceipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected $hitPayService;

    public function __construct(HitPayService $hitPayService)
    {
        $this->hitPayService = $hitPayService;
    }

    /**
     * Initiate the checkout process for a Diamond package.
     */
    public function store(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:diamond_packages,id',
            'payment_method' => 'required|in:hitpay,manual',
        ]);

        $user = Auth::user();

        // Check if user is allowed to purchase diamonds
        if (!$user->can_purchase_diamonds) {
            return back()->with('error', 'Hey, Sorry! But this action was disabled by the system because your account is under review at the moment. Please contact us to expedite the process.');
        }

        $package = DiamondPackage::findOrFail($request->package_id);

        // Validate if payment method is allowed for this package
        if ($request->payment_method === 'hitpay' && !$package->allow_hitpay) {
            return back()->with('error', 'HitPay is not allowed for this package.');
        }

        if ($request->payment_method === 'manual' && !$package->allow_manual) {
            return back()->with('error', 'Manual payment is not allowed for this package.');
        }

        // Determine the final price
        $amount = $package->final_price;

        // Create a unique reference
        $reference = 'BRAG-' . strtoupper(uniqid()) . '-' . time();

        // Save pending payment record
        $payment = Payment::create([
            'user_id' => $user->id,
            'diamond_package_id' => $package->id,
            'reference' => $reference,
            'amount' => $amount,
            'currency' => $package->currency,
            'diamonds_amount' => $package->diamonds,
            'status' => 'pending',
            'payment_method' => $request->payment_method,
        ]);

        if ($request->payment_method === 'manual') {
            return redirect()->route('payments.manual', $package->id);
        }

        try {
            $allMethods = config('hitpay.payment_methods', []);
            $paymentMethods = array_keys(array_filter($allMethods));

            $hitPayResponse = $this->hitPayService->createPaymentRequest(
                $payment->amount,
                $payment->currency,
                $payment->reference,
                $user->email,
                $user->username,
                route('payments.callback'),
                route('payments.webhook'),
                $paymentMethods
            );

            if (isset($hitPayResponse['id'])) {
                $payment->update(['hitpay_id' => $hitPayResponse['id']]);
            }

            return redirect()->away($hitPayResponse['url']);

        } catch (\Exception $e) {
            Log::error('HitPay Checkout Error: ' . $e->getMessage());
            $payment->update(['status' => 'failed']);
            return back()->with('error', 'Unable to initiate payment at this time. Please try again later.');
        }
    }

    /**
     * Show manual checkout page with QR code.
     */
    public function manualCheckout(DiamondPackage $package)
    {
        $user = Auth::user();
        
        if (!$user->can_purchase_diamonds) {
            return redirect()->route('wallet.index')->with('error', 'Hey, Sorry! But this action was disabled by the system because your account is under review at the moment. Please contact us to expedite the process.');
        }

        if (!$package->allow_manual || !$package->is_active) {
            return redirect()->route('wallet.index')->with('error', 'Manual payment is not available for this package.');
        }

        $agreement = ManualPaymentAgreement::latest()->first();

        return view('wallet.manual_checkout', compact('package', 'agreement'));
    }

    /**
     * Submit proof of payment for manual transaction.
     */
    public function submitManualProof(Request $request, DiamondPackage $package)
    {
        $request->validate([
            'proof' => 'required_without:proof_temp_path|image|max:5120',
            'proof_temp_path' => 'nullable|string',
            'i_agree' => 'required|accepted',
        ]);

        $user = Auth::user();

        if (!$user->can_purchase_diamonds) {
            return redirect()->route('wallet.index')->with('error', 'Hey, Sorry! But this action was disabled by the system because your account is under review at the moment. Please contact us to expedite the process.');
        }

        // Fetch latest agreement to link it to the transaction
        $agreement = ManualPaymentAgreement::latest()->first();

        // Find the pending manual payment record for this user and package
        $payment = Payment::where('user_id', $user->id)
            ->where('diamond_package_id', $package->id)
            ->where('payment_method', 'manual')
            ->where('status', 'pending')
            ->whereNull('proof_path')
            ->latest()
            ->first();

        // If no record exists, create one
        if (!$payment) {
            $reference = 'BRAG-MAN-' . strtoupper(uniqid()) . '-' . time();
            $payment = Payment::create([
                'user_id' => $user->id,
                'diamond_package_id' => $package->id,
                'reference' => $reference,
                'amount' => $package->final_price,
                'currency' => $package->currency,
                'diamonds_amount' => $package->diamonds,
                'status' => 'pending',
                'payment_method' => 'manual',
            ]);
        }

        $finalPath = null;

        // Handle chunked upload
        if ($request->filled('proof_temp_path')) {
            $tempPath = $request->input('proof_temp_path');
            
            // Security: Ensure the file is actually in the tmp/uploads directory
            if (strpos($tempPath, 'tmp/uploads/') === 0 && Storage::disk('public')->exists($tempPath)) {
                $extension = pathinfo($tempPath, PATHINFO_EXTENSION);
                $filename = 'proof_' . time() . '_' . Str::random(10) . '.' . $extension;
                $finalPath = 'proofs/' . $filename;
                
                Storage::disk('public')->move($tempPath, $finalPath);
            } else {
                return back()->with('error', 'Invalid or expired upload. Please try again.');
            }
        } 
        // Fallback for direct upload
        elseif ($request->hasFile('proof')) {
            $finalPath = $request->file('proof')->store('proofs', 'public');
        }

        if (!$finalPath) {
            return back()->with('error', 'Proof of payment is required.');
        }

        $payment->update([
            'proof_path' => $finalPath,
            'auto_approve_at' => now()->addMinutes(10),
            'manual_payment_agreement_id' => $agreement?->id,
        ]);

        return redirect()->route('wallet.index')->with('success', 'Your proof of payment has been submitted! Our team will review it within 10 minutes, or it will be auto-approved.');
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
            return redirect()->route('payments.success', ['reference' => $reference]);
        }

        return redirect()->route('wallet.index')->with('error', 'Payment was cancelled or failed.');
    }

    /**
     * Display the success page after a successful payment.
     */
    public function success(Request $request)
    {
        $reference = $request->query('reference');
        
        $payment = Payment::where(function ($query) use ($reference) {
                $query->where('reference', $reference)
                      ->orWhere('hitpay_id', $reference);
            })
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('payments.success', compact('payment'));
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
        $paymentRequestId = $payload['payment_request_id'] ?? null;

        if (!$reference) {
            return response()->json(['error' => 'No reference provided'], 400);
        }

        // 2. Fetch payment details outside of DB transaction to avoid holding locks during HTTP call
        $paymentType = null;
        $fees = null;
        if ($paymentRequestId) {
            $paymentDetails = $this->hitPayService->getPaymentRequest($paymentRequestId);
            if (!empty($paymentDetails['payments'])) {
                $successfulPayment = collect($paymentDetails['payments'])->firstWhere('status', 'succeeded');
                $paymentType = $successfulPayment['payment_type'] ?? null;
                $fees = $successfulPayment['fees'] ?? null;
            }
        }

        try {
            // 3. Wrap fulfillment in a Database Transaction for integrity
            DB::transaction(function () use ($reference, $status, $paymentType, $fees) {
                // Lock the row to prevent race conditions (e.g. double webhook delivery)
                $payment = Payment::where('reference', $reference)->lockForUpdate()->firstOrFail();

                // If already completed, nothing to do
                if ($payment->status === 'completed') {
                    return;
                }

                if ($status === 'completed') {
                    $updateData = ['status' => 'completed'];
                    if ($paymentType) {
                        $updateData['payment_type'] = $paymentType;
                    }
                    if ($fees !== null) {
                        $updateData['fees'] = $fees;
                        $updateData['net_amount'] = max(0, $payment->amount - $fees);
                    }
                    $payment->update($updateData);

                    // Add Diamonds to user
                    $payment->user->addDiamonds(
                        $payment->diamonds_amount, 
                        'purchased', 
                        "Purchased {$payment->diamonds_amount} Diamonds via HitPay using {$paymentType} (Ref: {$payment->reference})"
                    );

                    // Send email receipt
                    try {
                        Mail::to($payment->user->email)->send(new DiamondPurchaseReceipt($payment));
                    } catch (\Exception $mailEx) {
                        Log::error('HitPay Webhook: Failed to send receipt email: ' . $mailEx->getMessage());
                        // We still consider the transaction successful even if email fails
                    }

                } elseif (in_array($status, ['failed', 'canceled', 'refunded'])) {
                    $updateData = ['status' => $status];
                    if ($paymentType) {
                        $updateData['payment_type'] = $paymentType;
                    }
                    $payment->update($updateData);
                }
            });

            return response()->json(['message' => 'Webhook processed successfully']);

        } catch (\Exception $e) {
            Log::error('HitPay Webhook Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
