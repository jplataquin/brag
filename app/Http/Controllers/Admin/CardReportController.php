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

        return back()->with('success', "Report has been marked as {$request->status}.");
    }
}
