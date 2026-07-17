<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function index()
    {
        return view('complaints.track');
    }

    public function track(Request $request)
    {
        $validated = $request->validate([
            'ticket_id' => 'required|string',
            'email' => 'required|email',
        ]);

        $complaint = Complaint::where('ticket_id', $validated['ticket_id'])
            ->whereHas('user', function ($query) use ($validated) {
                $query->where('email', $validated['email']);
            })
            ->first();

        if (!$complaint) {
            return back()->withErrors(['ticket_id' => 'No complaint found with these details.']);
        }

        return view('complaints.track_result', compact('complaint'));
    }
}
