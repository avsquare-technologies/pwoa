<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EventAttendee;
use Illuminate\Support\Facades\Auth;

class TicketScannerController extends Controller
{
    public function index()
    {
        // For now, allow all authenticated users (or restrict to staff/admin later)
        return view('admin.scan-ticket');
    }

    public function validateTicket(Request $request)
    {
        $ticketId = $request->input('ticket_id');
        $token = $request->input('token');

        if (!$ticketId || !$token) {
            return response()->json([
                'success' => false,
                'message' => 'Missing ticket data. Please rescan.'
            ], 400);
        }

        $attendee = EventAttendee::where('ticket_id', $ticketId)->first();

        if (!$attendee) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Ticket: Not found in system.'
            ], 404);
        }

        if ($attendee->token !== $token) {
            return response()->json([
                'success' => false,
                'message' => 'Security Error: Token mismatch.'
            ], 403);
        }

        if ($attendee->status === 'used') {
            return response()->json([
                'success' => false,
                'message' => 'Already Used: This ticket was checked in at ' . $attendee->checked_in_at->format('h:i A')
            ], 400);
        }

        if ($attendee->status === 'expired' || ($attendee->expires_at && $attendee->expires_at->isPast())) {
            if ($attendee->status !== 'expired') {
                $attendee->update(['status' => 'expired']);
            }
            return response()->json([
                'success' => false,
                'message' => 'Ticket Expired: This ticket is no longer valid.'
            ], 400);
        }

        // Mark as used
        $attendee->update([
            'status' => 'used',
            'checked_in_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Valid Ticket! Access Granted.',
            'attendee' => [
                'name' => $attendee->user->name,
                'event' => $attendee->event->title,
                'time' => $attendee->checked_in_at->format('h:i A'),
            ]
        ]);
    }
}
