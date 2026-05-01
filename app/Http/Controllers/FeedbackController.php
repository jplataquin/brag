<?php

namespace App\Http\Controllers;

use App\Mail\UserFeedback;
use App\Rules\Turnstile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class FeedbackController extends Controller
{
    /**
     * Show the feedback form.
     */
    public function index()
    {
        return view('feedback');
    }

    /**
     * Send the feedback email.
     */
    public function send(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:100',
            'message' => 'required|string|max:2000',
            'cf-turnstile-response' => ['required', new Turnstile],
        ]);

        $user = Auth::user();
        $recipient = env('FEEDBACK_EMAIL', 'admin@brag.com');

        try {
            Mail::to($recipient)->send(new UserFeedback(
                $user,
                $request->subject,
                $request->message
            ));

            return redirect()->route('feedback.index')->with('success', 'Your feedback has been sent! Thank you for helping us improve the Arena.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send feedback. Please try again later.')->withInput();
        }
    }
}
