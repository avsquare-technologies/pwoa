<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\ComplaintCategory;
use App\Models\ComplaintReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    public function index()
    {
        $complaints = Auth::user()->complaints()->latest()->paginate(10);
        return view('complaints.index', compact('complaints'));
    }

    public function create()
    {
        $categories = ComplaintCategory::all();
        return view('complaints.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:complaint_categories,id',
            'description' => 'required|string',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ]);

        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('complaint-attachments', 'public');
            $validated['attachment_path'] = $path;
        }

        $complaint = Auth::user()->complaints()->create($validated);

        return redirect()->route('complaints.show', $complaint)
            ->with('success', 'Complaint submitted successfully. Ticket ID: ' . $complaint->ticket_id);
    }

    public function show(Complaint $complaint)
    {
        if ($complaint->user_id !== Auth::id()) {
            abort(403);
        }

        $complaint->load(['replies.user', 'replies.admin', 'category']);
        return view('complaints.show', compact('complaint'));
    }

    public function reply(Request $request, Complaint $complaint)
    {
        if ($complaint->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        $complaint->replies()->create([
            'user_id' => Auth::id(),
            'message' => $validated['message'],
        ]);

        return back()->with('success', 'Reply added successfully.');
    }
}
