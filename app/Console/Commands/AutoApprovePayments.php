<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Mail\DiamondPurchaseReceipt;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AutoApprovePayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:auto-approve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-approve manual payments that have passed the 10-minute review window.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $payments = Payment::where('status', 'pending')
            ->where('payment_method', 'manual')
            ->whereNotNull('proof_path')
            ->where('auto_approve_at', '<=', now())
            ->get();

        if ($payments->isEmpty()) {
            $this->info('No manual payments found for auto-approval.');
            return;
        }

        $this->info("Found {$payments->count()} manual payments to process.");

        foreach ($payments as $payment) {
            try {
                DB::transaction(function () use ($payment) {
                    $payment->lockForUpdate();

                    // Double check status in case it was processed manually just now
                    if ($payment->status !== 'pending') {
                        return;
                    }

                    $payment->update([
                        'status' => 'completed',
                        'payment_type' => 'manual_auto_approve',
                        'collected_at' => now(),
                        'collected_by' => null, // Auto-approved
                    ]);

                    // Add Diamonds to user
                    $payment->user->addDiamonds(
                        $payment->diamonds_amount, 
                        'purchased', 
                        "Purchased {$payment->diamonds_amount} Diamonds via Manual Payment (Auto-Approved) (Ref: {$payment->reference})"
                    );

                    // Send email receipt
                    try {
                        Mail::to($payment->user->email)->send(new DiamondPurchaseReceipt($payment));
                    } catch (\Exception $mailEx) {
                        Log::error("AutoApprove: Failed to send receipt email for {$payment->reference}: " . $mailEx->getMessage());
                    }
                });

                $this->info("Successfully auto-approved payment: {$payment->reference}");

            } catch (\Exception $e) {
                Log::error("AutoApprove Error for {$payment->reference}: " . $e->getMessage());
                $this->error("Failed to auto-approve payment: {$payment->reference}");
            }
        }

        $this->info('Auto-approval process completed.');
    }
}
