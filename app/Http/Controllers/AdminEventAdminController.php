<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;

class AdminEventAdminController extends Controller
{
    public function index(Event $event)
    {
        $assignedIds = $event->admins()->pluck('users.id');

        $users = User::where('role', 'admin')
            ->whereNotIn('id', $assignedIds)
            ->get();

        return inertia('Admin/EventAdmin/Index', [
            'event' => $event,
            'users' => $users,
            'admins' => $event->admins()
                ->withPivot('role')
                ->get(),
        ]);
    }

    public function store(Request $request, Event $event)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:owner,admin'
        ]);

        $exists = $event->admins()
            ->where('user_id', $request->user_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'User sudah menjadi admin');
        }

        $event->admins()->syncWithoutDetaching([
            $request->user_id => ['role' => $request->role]
        ]);

        return back()->with('success', 'Admin ditambahkan');
    }

    public function update(Request $request, Event $event, User $user)
    {
        $request->validate([
            'role' => 'required|in:owner,admin'
        ]);

        $event->admins()->updateExistingPivot($user->id, [
            'role' => $request->role
        ]);

        return back()->with('success', 'Role diupdate');
    }

    public function destroy(Event $event, User $user)
    {
        $event->admins()->detach($user->id);

        return back()->with('success', 'Admin dihapus');
    }
}