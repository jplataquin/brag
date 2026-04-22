<?php

namespace App\Http\Controllers;

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
        
        // Paginate the user's shard transactions, newest first
        $transactions = $user->shardTransactions()
            ->with(['fromUser', 'transferUser'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        // Get the computed shards balance from the model accessor
        $balance = $user->shards_balance;

        return view('wallet.index', compact('transactions', 'balance'));
    }
}
