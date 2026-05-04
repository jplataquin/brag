<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ManualPaymentAgreement;
use Illuminate\Http\Request;

class ManualPaymentAgreementController extends Controller
{
    public function index()
    {
        $agreements = ManualPaymentAgreement::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.manual_payment_agreements.index', compact('agreements'));
    }

    public function create()
    {
        return view('admin.manual_payment_agreements.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        ManualPaymentAgreement::create($request->only('content'));

        return redirect()->route('admin.manual-payment-agreements.index')->with('success', 'New manual payment agreement created and activated.');
    }

    public function edit(ManualPaymentAgreement $manualPaymentAgreement)
    {
        return view('admin.manual_payment_agreements.edit', compact('manualPaymentAgreement'));
    }

    public function update(Request $request, ManualPaymentAgreement $manualPaymentAgreement)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $manualPaymentAgreement->update($request->only('content'));

        return redirect()->route('admin.manual-payment-agreements.index')->with('success', 'Agreement updated.');
    }

    public function destroy(ManualPaymentAgreement $manualPaymentAgreement)
    {
        // Don't allow deletion if linked to payments (foreign key will handle but good to check)
        if ($manualPaymentAgreement->payments()->exists()) {
            return back()->with('error', 'Cannot delete an agreement that has already been signed by users.');
        }

        $manualPaymentAgreement->delete();
        return redirect()->route('admin.manual-payment-agreements.index')->with('success', 'Agreement deleted.');
    }
}
