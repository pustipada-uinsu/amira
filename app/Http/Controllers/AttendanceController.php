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

class AttendanceController extends Controller
{


    public function index(Event $event)
    {
        if (!$event->canAccessBy(auth()->user())) {
            abort(403);
        }

        $dates = $event->sessions()
            ->select('session_date')
            ->distinct()
            ->orderBy('session_date')
            ->pluck('session_date');

        $attendancePerDay = $dates->map(function ($date) use ($event) {

            $count = SessionAttendance::where('event_id', $event->id)
                ->whereDate('attendance_date', $date)
                ->whereNotNull('user_id')
                ->count();

            $carbon = Carbon::parse($date);

            return [
                'date' => $carbon->format('Y-m-d'),
                'label' => $carbon->translatedFormat('l, d F Y'),
                'attendance_count' => $count
            ];
        });

        $eventCheckinCount = $event->participants()
            ->whereNotNull('checked_in_at')
            ->count();

        return Inertia::render('Attendance/Index', [
            'event' => $event,
            'days' => $attendancePerDay,
            'event_checkin_count' => $eventCheckinCount
        ]);
    }

    public function generateQR(Event $event)
    {
        if (!$event->canAccessBy(auth()->user())) {
            abort(403);
        }

        $date = request('date');

        if (!$date) {
            return response()->json([
                'message' => 'Tanggal wajib diisi'
            ], 422);
        }

        $date = Carbon::parse($date)
            ->timezone('Asia/Jakarta')
            ->format('Y-m-d');

        $cacheKey = "qr_{$event->id}_{$date}";
        $ttl = 12;
        $token = (string) Str::uuid();

        Cache::put($cacheKey, $token, now()->addMinutes($ttl));

        return response()->json([
            'type' => 'daily',
            'token' => $token,
            'event_id' => $event->id,
            'hashid' => $event->hashid,
            'date' => $date,
            'expired_in' => $ttl
        ]);
    }

    public function qrPage(Event $event)
    {
        if (!$event->canAccessBy(auth()->user())) {
            abort(403);
        }

        $date = request('date');

        if (!$date) {
            return redirect()->back()->with('error', 'Tanggal wajib diisi');
        }

        return Inertia::render('Attendance/QR', [
            'event' => $event,
            'date' => $date
        ]);
    }



    public function scanPage()
    {
        return Inertia::render('Attendance/Scan');
    }

    public function scanProcess(Request $request)
    {
        $data = $request->validate([
            'event_id' => 'required|exists:events,id',
            'date' => 'required',
            'token' => 'required'
        ]);

        $event = Event::findOrFail($data['event_id']);

        $participant = $event->participants()
            ->where('user_id', auth()->id())
            ->where('status', 'approved')
            ->first();

        if (!$participant) {
            return response()->json([
                'message' => 'Kamu tidak terdaftar atau belum disetujui'
            ], 403);
        }

        $date = Carbon::parse($data['date'])
            ->timezone('Asia/Jakarta')
            ->format('Y-m-d');

        $cacheKey = "qr_{$event->id}_{$date}";
        $validToken = Cache::get($cacheKey);
        $validToken = (string) $validToken;

        if (!$validToken) {
            return response()->json([
                'message' => 'QR sudah expired (cache kosong)',
            ], 403);
        }

        if (!hash_equals($validToken, (string) $data['token'])) {
            return response()->json([
                'message' => 'QR tidak valid',
                'debug' => [
                    'cache' => $validToken,
                    'incoming' => $data['token']
                ]
            ], 403);
        }

        $exists = SessionAttendance::where('user_id', auth()->id())
            ->where('event_id', $event->id)
            ->where('attendance_date', $date)
            // ->whereHas('session', fn($q) => $q->where('event_id', $event->id))
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Kamu sudah check-in hari ini'
            ], 409);
        }

        try {
            SessionAttendance::create([
                'event_id' => $event->id,
                'user_id' => auth()->id(),
                'attendance_date' => $date,
                'checked_in_at' => now(),
                'method' => 'qr'
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'message' => 'Kamu sudah check-in hari ini'
            ], 409);
        }

        return response()->json([
            'message' => "Check-in berhasil di {$event->title}"
        ]);
    }

    public function participants(Event $event)
    {
        if (!$event->canAccessBy(auth()->user())) {
            abort(403);
        }

        $date = request('date');

        if (!$date) {
            return redirect()->back()->with('error', 'Tanggal wajib diisi');
        }

        $date = Carbon::parse($date)->format('Y-m-d');

        $participants = $event->participants()
            ->with('user')
            ->where('status', 'approved')
            ->get()
            ->map(function ($p) use ($event, $date) {

                $attendance = SessionAttendance::where('event_id', $event->id)
                    ->where('user_id', $p->user_id)
                    ->whereDate('attendance_date', $date)
                    ->first();

                return [
                    'id' => $p->id,
                    'user' => $p->user,
                    'is_present' => $attendance ? true : false,
                    'checked_in_at' => $attendance?->checked_in_at,
                ];
            });

        return Inertia::render('Attendance/Participants', [
            'event' => $event,
            'date' => $date,
            'participants' => $participants
        ]);
    }

    public function export(Event $event)
    {
        if (!$event->canAccessBy(auth()->user())) {
            abort(403);
        }

        $date = request('date');

        if (!$date) {
            abort(404);
        }

        $participants = SessionAttendance::with('user')
            ->where('event_id', $event->id)
            ->whereDate('attendance_date', $date)
            ->whereNotNull('user_id')
            ->orderBy('checked_in_at', 'asc')
            ->get();

        $filename = 'presensi-' . $event->title . '-' . $date . '.csv';

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

    public function manualCheckin(Request $request, Event $event)
    {
        if (!$event->canAccessBy(auth()->user())) {
            return response()->json([
                'message' => 'Tidak diizinkan'
            ], 403);
        }
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required'
        ]);
        $date = Carbon::parse($data['date'])->format('Y-m-d');
        $exists = SessionAttendance::where('event_id', $event->id)
            ->where('user_id', $data['user_id'])
            ->whereDate('attendance_date', $date)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Peserta sudah presensi');
        }
        SessionAttendance::create([
            'event_id' => $event->id,
            'user_id' => $data['user_id'],
            'attendance_date' => $date,
            'checked_in_at' => now(),
            'method' => 'manual'
        ]);
        return back()->with('success', 'Presensi manual berhasil');
    }
}
