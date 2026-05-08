<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CardReport;
use Illuminate\Http\Request;

class CardReportController extends Controller
{
    /**
     * Display a listing of card reports.
     */
    public function index()
    {
        $reports = CardReport::with(['user', 'digitalCard.template'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.card_reports.index', compact('reports'));
    }

    /**
     * Resolve or dismiss a report.
     */
    public function resolve(Request $request, CardReport $report)
    {
        $request->validate([
            'status' => 'required|in:resolved,dismissed',
        ]);

        $report->update(['status' => $request->status]);

        if ($request->status === 'resolved') {
            $report->digitalCard->update(['is_censored' => true]);
        } elseif ($request->status === 'dismissed') {
            // Auto-un-censor if no more pending reports exist for this card
            $pendingCount = CardReport::where('digital_card_id', $report->digital_card_id)
                ->where('status', 'pending')
                ->count();
                
            if ($pendingCount === 0) {
                $report->digitalCard->update(['is_censored' => false]);
            }
        }

        return back()->with('success', "Report has been marked as {$request->status}.");
    }
}
