<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\SessionAttendance;
use Inertia\Inertia;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CheckinController extends Controller
{


    public function generateEventQR(Event $event)
    {
        if (!$event->canAccessBy(auth()->user())) {
            abort(403);
        }

        $cacheKey = "event_checkin_{$event->id}";
        $ttl = 12;
        $token = (string) \Str::uuid();

        \Cache::put($cacheKey, $token, now()->addMinutes($ttl));

        return response()->json([
            'type' => 'event_checkin',
            'token' => $token,
            'event_id' => $event->id,
            'hashid' => $event->hashid,
            'expired_in' => $ttl
        ]);
    }

    public function eventQRPage(Event $event)
    {
        if (!$event->canAccessBy(auth()->user())) {
            abort(403);
        }

        return Inertia::render('Attendance/EventQR', [
            'event' => $event
        ]);
    }

    public function scanEventCheckin(Request $request)
    {
        $data = $request->validate([
            'event_id' => 'required|exists:events,id',
            'token' => 'required'
        ]);

        $event = Event::findOrFail($data['event_id']);

        $participant = $event->participants()
            ->where('user_id', auth()->id())
            ->where('status', 'approved')
            ->first();

        if (!$participant) {
            return response()->json([
                'message' => 'Kamu tidak terdaftar'
            ], 403);
        }

        $cacheKey = "event_checkin_{$event->id}";
        $validToken = (string) \Cache::get($cacheKey);

        if (!$validToken || !hash_equals($validToken, (string) $data['token'])) {
            return response()->json([
                'message' => 'QR tidak valid / expired'
            ], 403);
        }

        if ($participant->checked_in_at) {
            return response()->json([
                'message' => 'Kamu sudah check-in event ini'
            ], 409);
        }

        $participant->update([
            'checked_in_at' => now()
        ]);

        return response()->json([
            'message' => "Berhasil check-in di event {$event->title}"
        ]);
    }

    public function participants(Event $event)
    {
        if (!$event->canAccessBy(auth()->user())) {
            abort(403);
        }

        $participants = $event->participants()
            ->with('user')
            ->where('status', 'approved')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'user' => $p->user,
                    'is_checked_in' => $p->checked_in_at ? true : false,
                    'checked_in_at' => $p->checked_in_at,
                ];
            });

        return Inertia::render('Attendance/EventParticipants', [
            'event' => $event,
            'participants' => $participants
        ]);
    }

    public function manualCheckin(Event $event, Request $request)
    {
        if (!$event->canAccessBy(auth()->user())) {
            abort(403);
        }

        $data = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $participant = $event->participants()
            ->where('user_id', $data['user_id'])
            ->firstOrFail();

        if ($participant->checked_in_at) {
            return back()->with('error', 'Peserta sudah check-in');
        }

        $participant->update([
            'checked_in_at' => now()
        ]);

        return back()->with('success', 'Check-in manual berhasil');
    }

    public function exportEventCheckin(Event $event)
    {
        if (!$event->canAccessBy(auth()->user())) {
            abort(403);
        }

        $participants = $event->participants()
            ->with('user')
            ->whereNotNull('checked_in_at')
            ->orderBy('checked_in_at', 'asc')
            ->get();

        $filename = 'checkin-event-'.$event->title.'.csv';

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
        ];

        $callback = function () use ($participants) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['No', 'Nama', 'Email', 'Check-in']);

            foreach ($participants as $i => $p) {
                fputcsv($file, [
                    $i + 1,
                    $p->user->name,
                    $p->user->email,
                    $p->checked_in_at
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
