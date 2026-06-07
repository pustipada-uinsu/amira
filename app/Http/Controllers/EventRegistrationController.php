<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EventRegistrationController extends Controller
{

    public function index(Request $request, Event $event)
    {
        $user = auth()->user();
        if (!$event->canAccessBy($user)) {
            abort(403, 'Tidak punya akses ke event ini');
        }

        $query = $event->participants()->with(['user.profile.institusi']);

        if ($request->search) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        return inertia('Event/Participants', [
            'event' => $event,
            'participants' => $query->latest()->paginate(10)->withQueryString(),
            'filters' => $request->only('search', 'status')
        ]);
    }

    public function approve($id)
    {
        $participant = EventParticipant::with('event')->findOrFail($id);

        $event = $participant->event;

        if (!$event->canAccessBy(auth()->user())) {
            abort(403);
        }

        $participant->update([
            'status' => 'approved'
        ]);

        return back()->with('success', 'Peserta disetujui');
    }

    public function unapprove($id)
    {
        $participant = EventParticipant::with('event')->findOrFail($id);
        $event = $participant->event;
        if (!$event->canAccessBy(auth()->user())) {
            abort(403);
        }
        $participant->update([
            'status' => 'pending'
        ]);
        return back()->with('success', 'Status dikembalikan ke pending');
    }

    public function reject($id)
    {
        $participant = EventParticipant::with('event')->findOrFail($id);

        $event = $participant->event;

        if (!$event->canAccessBy(auth()->user())) {
            abort(403);
        }

        $participant->update([
            'status' => 'rejected'
        ]);

        return back()->with('success', 'Peserta ditolak');
    }

    public function store(Event $event, Request $request)
    {
        $user = auth()->user();

        if (!$event->canAccessBy($user)) {
            abort(403, 'Tidak punya akses ke event ini');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $exists = $event->participants()
            ->where('user_id', $request->user_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'User sudah terdaftar');
        }

        $event->participants()->create([
            'user_id' => $request->user_id,
            'status' => 'approved',
            'registered_at' => now(),
        ]);

        return back()->with('success', 'Peserta berhasil ditambahkan');
    }

    public function apply(Event $event, Request $request)
    {
        $user = auth()->user();

        $exists = $event->participants()
            ->where('user_id', $user->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Kamu sudah terdaftar');
        }

        if (
            !$event->registration_start ||
            !$event->registration_end ||
            now()->lt($event->registration_start) ||
            now()->gt($event->registration_end)
        ) {
            return back()->with('error', 'Pendaftaran belum dibuka / sudah ditutup');
        }

        $event->participants()->create([
            'user_id' => $user->id,
            'status' => 'pending',
            'registered_at' => now(),
        ]);

        return back()->with('success', 'Berhasil mendaftar, menunggu approval');
    }

    public function export(Request $request, Event $event)
    {
        $user = auth()->user();

        if (!$event->canAccessBy($user)) {
            abort(403, 'Tidak punya akses ke event ini');
        }

        $status = $request->status;

        $participants = $event->participants()
            ->with(['user.profile.institusi'])
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->latest()
            ->get();

        $fileName = "participants-{$event->title}-{$status}.csv";

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
        ];

        $columns = [
            'Nama',
            'Email',
            'Institusi',
            'No HP',
            'WhatsApp',
            'Jabatan',
            'Status',
            'Tanggal Daftar',
            'Avatar',
        ];

        $callback = function () use ($participants, $columns) {
            $file = fopen('php://output', 'w');

            // header CSV
            fputcsv($file, $columns);

            foreach ($participants as $p) {
                $user = $p->user;
                $profile = $user?->profile;

                fputcsv($file, [
                    $user?->name ?? '-',
                    $user?->email ?? '-',
                    $profile?->institusi?->nama_institusi ?? '-',
                    '="' . ($profile?->no_telp ?? '-') . '"',
                    '="' . ($profile?->no_wa ?? '-') . '"',
                    $profile?->jabatan ?? '-',
                    $p->status,
                    $p->registered_at,

                    $profile?->avatar
                        ? asset('storage/' . $profile->avatar)
                        : '-',
                ]);
            }

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    public function availableUsers(Request $request, Event $event)
    {
        $search = $request->search;

        $users = User::query()
            ->where('role', 'user')
            ->whereDoesntHave('participants', function ($q) use ($event) {
                $q->where('event_id', $event->id);
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
                });
            })
            ->latest()
            ->limit(10)
            ->get(['id', 'name', 'email']);

        return response()->json($users);
    }

    
}