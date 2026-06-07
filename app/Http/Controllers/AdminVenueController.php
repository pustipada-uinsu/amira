<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use Illuminate\Http\Request;

class AdminVenueController extends Controller
{
    public function index(Request $request)
    {
        $query = Venue::query();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('address', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->status !== null && $request->status !== '') {
            $query->where('status', $request->status);
        }
        $venues = $query->latest()->paginate(10)->withQueryString();
        return inertia('Admin/Venue/Index', [
            'venues' => $venues,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        Venue::create($request->all());
        return back()->with('success', 'Venue berhasil ditambahkan');
    }

    public function update(Request $request, Venue $venue)
    {
        $venue->update($request->all());
        return back()->with('success', 'Venue berhasil diupdate');
    }

    public function destroy(Venue $venue)
    {
        $venue->delete();
        return back()->with('success', 'Venue berhasil dihapus');
    }
}