<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Venue;
use App\Models\SessionAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EventController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'adminsuper') {
            $events = Event::with('venue')->latest()->get();
        } else {
            $events = Event::with('venue')
                ->where(function ($q) use ($user) {
                    $q->where('created_by', $user->id)
                    ->orWhereHas('admins', fn($q2) => $q2->where('user_id', $user->id)); })
                ->latest()
                ->get();
        }

        return inertia('Event/Index', [
            'events' => $events,
            'auth' => [
                'user' => auth()->user()
            ]
        ]);
    }

    public function create()
    {
        return inertia('Event/Create', [
            'venues' => Venue::where('status', true)->get(),
        ]);
    }

    public function show(Event $event)
    {
        if (!$event->canAccessBy(auth()->user())) {
            abort(403);
        }

        return inertia('Event/Show', [
            'event' => $event->load(['venue', 'sessions'])
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'public' => 'required|boolean',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'registration_start' => 'required|date',
            'registration_end' => 'required|date|after_or_equal:registration_start',
            'venue_id' => 'nullable|exists:venues,id',
            'custom_location' => 'nullable|string',
            'banner' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'flyer' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $bannerPath = null;
        if ($request->hasFile('banner')) {
            $bannerPath = $request->file('banner')->store('events', 'public');
        }

        $flyerPath = null;
        if ($request->hasFile('flyer')) {
            $flyerPath = $request->file('flyer')->store('flyers', 'public');
        }

        $event = Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'public' => $request->public,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'registration_start' => $request->registration_start,
            'registration_end' => $request->registration_end,
            'venue_id' => $request->venue_id,
            'custom_location' => $request->custom_location,
            'created_by' => Auth::id(),
            'status' => 'draft',
            'banner' => $bannerPath,
            'flyer' => $flyerPath,
        ]);

        $event->admins()->attach(Auth::id(), ['role' => 'owner']);

        return redirect()->route('events.index')->with('success', 'Event berhasil dibuat');
    }

    public function edit(Event $event)
    {
        if (!$event->canEditBy(auth()->user())) {
            abort(403);
        }

        return inertia('Event/Edit', [
            'event' => $event->load('venue'),
            'venues' => \App\Models\Venue::where('status', true)->get()
        ]);
    }

    public function update(Request $request, Event $event)
    {
        if (!$event->canEditBy(auth()->user())) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'public' => 'required|boolean',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'registration_start' => 'required|date',
            'registration_end' => 'required|date|after_or_equal:registration_start',
            'venue_id' => 'nullable|exists:venues,id',
            'custom_location' => 'nullable|string',
            'banner' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'flyer' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'required|in:draft,published,finished',
        ]);

        $bannerPath = $event->banner;
        if ($request->hasFile('banner')) {
            $bannerPath = $request->file('banner')->store('events', 'public');
        }

        $flyerPath = $event->flyer;
        if ($request->hasFile('flyer')) {
            $flyerPath = $request->file('flyer')->store('flyers', 'public');
        }

        $event->update([
            'title' => $request->title,
            'description' => $request->description,
            'public' => $request->public,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'registration_start' => $request->registration_start,
            'registration_end' => $request->registration_end,
            'venue_id' => $request->venue_id,
            'custom_location' => $request->custom_location,
            'banner' => $bannerPath,
            'flyer' => $flyerPath,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('events.index', $event->hashid)
            ->with('success', 'Event berhasil diupdate');
    }

    public function destroy(Event $event)
    {
        if (!$event->canDeleteBy(auth()->user())) {
            abort(403);
        }

        $event->delete();

        return redirect()
            ->route('events.index')
            ->with('success', 'Event berhasil dihapus');
    }

    public function detail(Event $event)
    {
        $user = auth()->user();

        $participant = $event->participants()
            ->where('user_id', $user->id)
            ->first();

        $sessions = [];

        if ($participant && in_array($participant->status, ['approved', 'checked_in'])) {
            $sessions = $event->sessions()
                ->orderBy('session_date')
                ->orderBy('start_at')
                ->get();
        }

        $attendances = SessionAttendance::where('event_id', $event->id)
        ->where('user_id', $user->id)
        ->get()
        ->keyBy(function ($item) {
            return Carbon::parse($item->attendance_date)->format('Y-m-d');
        });

        return inertia('Event/Detail', [
            'event' => $event->load('venue'),
            'isRegistered' => !!$participant,
            'myStatus' => $participant?->status,
            'myParticipant' => $participant,
            'sessions' => $sessions,
            'attendances' => $attendances,
            'is_checked_in_event' => $participant?->checked_in_at ? true : false,
        ]);
    }
}
