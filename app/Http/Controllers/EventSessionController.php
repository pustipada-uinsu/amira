<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventSession;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class EventSessionController extends Controller
{
    public function index(Event $event)
    {
        if (!$event->canAccessBy(auth()->user())) {
            abort(403);
        }

        $days = collect(CarbonPeriod::create(
            $event->start_date->startOfDay(),
            $event->end_date->startOfDay()
        ))->map(function (Carbon $date) {
            return [
                'date' => $date->toDateString(),
                'label' => $date->locale('id')->translatedFormat('l, d F Y'),
            ];
        });

        $sessions = $event->sessions()
            ->orderBy('start_at')
            ->get()
            ->map(function ($s) {

                return [
                    'id' => $s->id,
                    'title' => $s->title,
                    'session_date' => $s->session_date->toDateString(),

                    'start_at' => $s->start_at->toIso8601String(),
                    'end_at' => $s->end_at->toIso8601String(),
                    'location' => $s->location
                        ?? $s->event?->venue?->name
                        ?? $s->event?->custom_location,

                    'description' => $s->description,

                    'materials' => $s->materials ?? [],
                ];
            });

        return inertia('Event/Session/Index', [
            'event' => [
                ...$event->toArray(),
                'start_date_formatted' => $event->start_date
                    ? $event->start_date->format('d M Y')
                    : '-',
                'end_date_formatted' => $event->end_date
                    ? $event->end_date->format('d M Y')
                    : '-',
                'location_final' => $event->venue?->name
                    ?: $event->custom_location
                    ?: 'Lokasi belum diatur',
            ],

            'days' => $days,
            'sessions' => $sessions,
        ]);
    }

    public function store(Request $request, Event $event)
    {
        if (!$event->canAccessBy(auth()->user())) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'session_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'location' => 'nullable|string',
            'description' => 'nullable|string',
            'materials' => 'nullable|array',
            'materials.*' => 'file|mimes:pdf,doc,docx,ppt,pptx|max:5120',
        ]);

        $start = Carbon::parse($request->session_date . ' ' . $request->start_time);
        $end = Carbon::parse($request->session_date . ' ' . $request->end_time);

        $files = [];

        if ($request->hasFile('materials')) {
            foreach ($request->file('materials') as $file) {
                $files[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $file->store('session-materials', 'public'),
                ];
            }
        }

        EventSession::create([
            'event_id' => $event->id,
            'title' => $request->title,
            'session_date' => $request->session_date,
            'start_at' => $start,
            'end_at' => $end,
            'location' => $request->location,
            'description' => $request->description,
            'materials' => $files,
        ]);

        return back()->with('success', 'Sesi berhasil ditambahkan');
    }

    public function destroy(Event $event, EventSession $session)
    {
        if ($session->event_id !== $event->id) {
            abort(404);
        }

        if (!$event->canAccessBy(auth()->user())) {
            abort(403);
        }

        $session->delete();

        return back()->with('success', 'Sesi dihapus');
    }
}
