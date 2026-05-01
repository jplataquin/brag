<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of published announcements.
     */
    public function index()
    {
        $announcements = Announcement::where('is_published', true)
            ->latest()
            ->paginate(10);

        return view('announcements.index', compact('announcements'));
    }

    /**
     * Display the specified announcement.
     */
    public function show(Announcement $announcement)
    {
        if (!$announcement->is_published) {
            abort(404);
        }

        return view('announcements.show', compact('announcement'));
    }
}
