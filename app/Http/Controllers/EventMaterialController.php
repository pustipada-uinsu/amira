<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\EventSession;

class EventMaterialController extends Controller
{


    public function download($id, $index)
    {
        $session = EventSession::findOrFail($id);

        $user = auth()->user();

        $participant = $session->event
            ->participants()
            ->where('user_id', $user->id)
            ->first();

        if (!$participant || $participant->status !== 'checked_in') {
            abort(403, 'Tidak bisa buka Materi, Kamu belum check-in');
        }

        $file = $session->materials[$index] ?? null;

        if (!$file || !Storage::disk('public')->exists($file['path'])) {
            abort(404, 'File tidak ditemukan di storage');
        }

        return Storage::disk('public')->response(
            $file['path'],
            $file['name']
        );
    }
}
