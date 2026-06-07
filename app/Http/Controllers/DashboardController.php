<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventParticipant;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $events = collect();
        $myEvents = collect();
        $stats = [];

        // ================= SUPERADMIN =================
        if ($user->role === 'superadmin') {

            $events = Event::with(['venue'])
                ->latest()
                ->take(6)
                ->get();

            $stats = [
                'total_events' => Event::count(),
                'total_participants' => EventParticipant::count(),
            ];
        }

        // ================= ADMIN =================
        elseif ($user->role === 'admin') {

            $events = Event::with(['venue'])
                ->where(function ($q) use ($user) {
                    $q->where('created_by', $user->id)
                        ->orWhereHas('admins', function ($q2) use ($user) {
                            $q2->where('user_id', $user->id);
                        });
                })
                ->latest()
                ->get();

            $eventIds = $events->pluck('id');

            $stats = [
                'my_events' => $events->count(),
                'total_participants' => EventParticipant::whereIn('event_id', $eventIds)->count(),
            ];
        }

        // ================= USER =================
        else {

            $myEvents = EventParticipant::with(['event.venue'])
                ->where('user_id', $user->id)
                ->latest()
                ->get();

            $events = Event::with(['venue'])
                ->where('status', 'published')
                ->where('public', true)
                ->latest()
                ->take(6)
                ->get();

            $stats = [
                'joined_events' => $myEvents->count(),
            ];
        }

        return inertia('Dashboard', [
            'user' => $user,
            'events' => $events,
            'my_events' => $myEvents,
            'stats' => $stats,
            'role' => $user->role,
        ]);
    }
}
