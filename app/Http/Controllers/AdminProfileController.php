<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;

class AdminProfileController extends Controller
{
    public function index(Request $request)
    {
        $query = UserProfile::with(['user', 'institusi'])
            ->whereHas('user', function ($q) {
                $q->where('is_active', true);
            });

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->search . '%')
                    ->orWhereHas('user', function ($q2) use ($request) {
                        $q2->where('email', 'like', '%' . $request->search . '%');
                    });
            });
        }

        if ($request->zona) {
            $query->whereHas('institusi', function ($q) use ($request) {
                $q->where('id_zona', $request->zona);
            });
        }

        if ($request->institusi) {
            $query->where('institusi_id', $request->institusi);
        }

        $profiles = $query->latest()->paginate(10)->withQueryString();

        $institusi = \App\Models\Institusi::select('id_institusi', 'nama_institusi', 'id_zona')->get();

        $zona = $institusi->unique('id_zona')->map(function ($item) {
            return [
                'id' => $item->id_zona,
                'nama' => $item->nama_zona ?? 'Zona ' . $item->id_zona
            ];
        })->values();

        return inertia('Admin/Profiles/Index', [
            'profiles' => $profiles,
            'filters' => $request->only(['search', 'zona', 'institusi']),
            'institusi' => $institusi,
            'zona' => $zona,
        ]);
    }

    public function export(Request $request)
    {
        $query = UserProfile::with(['user', 'institusi'])
            ->whereHas('user', function ($q) {
                $q->where('is_active', true);
            });

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->search . '%')
                    ->orWhereHas('user', function ($q2) use ($request) {
                        $q2->where('email', 'like', '%' . $request->search . '%');
                    });
            });
        }

        if ($request->zona) {
            $query->whereHas('institusi', function ($q) use ($request) {
                $q->where('id_zona', $request->zona);
            });
        }

        if ($request->institusi) {
            $query->where('institusi_id', $request->institusi);
        }

        $profiles = $query->latest()->get();

        $filename = 'user_profiles.csv';

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
        ];

        $callback = function () use ($profiles) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Nama',
                'Email',
                'Institusi',
                'Zona',
                'No Telp',
                'No WA',
                'Jabatan',
                'Ukuran Baju',
                'Smoking',
                'Avatar',
            ]);

            foreach ($profiles as $p) {
                fputcsv($file, [
                    $p->nama_lengkap,
                    $p->user?->email,
                    $p->institusi?->nama_institusi,
                    $p->institusi?->nama_zona,
                    $p->no_telp,
                    $p->no_wa,
                    $p->jabatan,
                    $p->ukuran_baju,
                    $p->is_smoking ? 'Ya' : 'Tidak',
                    $p->avatar ? asset('storage/' . $p->avatar) : '-',
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
