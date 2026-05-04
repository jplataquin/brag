<?php

namespace App\Http\Controllers;

use App\Models\DiamondPackage;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    /**
     * Display the user's wallet with balance and transaction history.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Paginate the user's diamond transactions, newest first
        $transactions = $user->diamondTransactions()
            ->with(['fromUser', 'transferUser'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        // Get the computed diamonds balance from the model accessor
        $balance = $user->diamonds_balance;
        
        $packages = DiamondPackage::active()->orderBy('diamonds', 'asc')->get();

        // Check manual payment limit (max 3 per day)
        $manualPaymentsToday = Payment::where('user_id', $user->id)
            ->where('payment_method', 'manual')
            ->whereDate('created_at', now()->today())
            ->count();
        
        $reachedManualLimit = $manualPaymentsToday >= 3;

        return view('wallet.index', compact('transactions', 'balance', 'packages', 'reachedManualLimit'));
    }
}
