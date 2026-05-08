<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CardReport;
use App\Mail\CardReportActionNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
     * Display the specific card report.
     */
    public function show(CardReport $report)
    {
        $report->load(['user', 'digitalCard.template', 'digitalCard.owner', 'resolvedBy']);
        return view('admin.card_reports.show', compact('report'));
    }

    /**
     * Resolve or dismiss a report.
     */
    public function resolve(Request $request, CardReport $report)
    {
        $request->validate([
            'status' => 'required|in:resolved,dismissed',
            'admin_notes' => 'required|string|min:5|max:1000',
        ]);

        $report->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);

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

        // Send email notification to reporter
        try {
            Mail::to($report->user->email)->send(new CardReportActionNotification($report));
        } catch (\Exception $e) {
            // Log error or ignore if mail fails
            \Log::error("Failed to send CardReportActionNotification: " . $e->getMessage());
        }

        $statusLabel = $request->status === 'resolved' ? 'confirmed' : $request->status;
        return redirect()->route('admin.card_reports.index')
            ->with('success', "Report has been marked as {$statusLabel} and the reporter has been notified.");
    }
}
