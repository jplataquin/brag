@extends('layouts.app')

@section('title', 'Payment Success')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="mb-4">
                <i class="bi bi-check-circle" style="font-size: 5rem; color: #39ff14; text-shadow: 0 0 20px rgba(57, 255, 20, 0.5);"></i>
            </div>
            
            <h1 class="mb-2" style="font-family: 'Orbitron', sans-serif; color: #fff; letter-spacing: 2px;">
                PAYMENT SUCCESSFUL!
            </h1>
            <p class="lead text-muted mb-5">
                Thank you for your purchase. Your diamonds are being prepared and will be added to your wallet shortly.
            </p>

            <div class="neon-card p-4 mb-5 text-start" style="border-color: #00f0ff; background: rgba(17, 17, 34, 0.8); backdrop-filter: blur(10px);">
                <h4 class="mb-4 text-center" style="font-family: 'Orbitron', sans-serif; color: #00f0ff; letter-spacing: 1px; border-bottom: 1px solid rgba(0, 240, 255, 0.2); padding-bottom: 15px;">
                    TRANSACTION RECEIPT
                </h4>
                
                <div class="row mb-3">
                    <div class="col-sm-5 text-muted small" style="font-family: 'Orbitron', sans-serif;">REFERENCE NO.</div>
                    <div class="col-sm-7 fw-bold" style="font-family: monospace; color: #fff;">{{ $payment->reference }}</div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-5 text-muted small" style="font-family: 'Orbitron', sans-serif;">PURCHASED</div>
                    <div class="col-sm-7 fw-bold" style="color: #00f0ff;">
                        <i class="bi bi-gem"></i> {{ number_format($payment->diamonds_amount) }} DIAMONDS
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-5 text-muted small" style="font-family: 'Orbitron', sans-serif;">AMOUNT PAID</div>
                    <div class="col-sm-7 fw-bold" style="color: #fff;">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-5 text-muted small" style="font-family: 'Orbitron', sans-serif;">DATE</div>
                    <div class="col-sm-7 text-muted">{{ $payment->created_at->format('M d, Y h:i A') }}</div>
                </div>

                <div class="row">
                    <div class="col-sm-5 text-muted small" style="font-family: 'Orbitron', sans-serif;">STATUS</div>
                    <div class="col-sm-7">
                        @if($payment->status === 'completed')
                            <span class="badge bg-success">COMPLETED</span>
                        @else
                            <span class="badge bg-warning text-dark">PROCESSING</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
                <a href="{{ route('wallet.index') }}" class="btn btn-outline-neon btn-lg px-5" style="border-color: #00f0ff; color: #00f0ff;">
                    <i class="bi bi-wallet2 me-2"></i> RETURN TO WALLET
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-lg px-5">
                    <i class="bi bi-speedometer2 me-2"></i> DASHBOARD
                </a>
            </div>
            
            <div class="mt-5 pt-4">
                <p class="small text-muted">
                    If your diamonds do not appear within 5 minutes, please contact support with your Reference No.
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-outline-neon:hover {
        background-color: rgba(0, 240, 255, 0.1);
        box-shadow: 0 0 15px rgba(0, 240, 255, 0.4);
        color: #fff !important;
    }
</style>
@endsection
