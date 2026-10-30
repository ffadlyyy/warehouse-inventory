<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->get();

        return Inertia::render('users/Index', [
            'users' => $users,
        ]);
    }

    public function updateRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|in:admin,staff',
        ]);

        // Cegah Admin mengubah role dirinya sendiri menjadi Staff agar tidak terkunci
        if ($user->id === $request->user()->id && $validated['role'] !== 'admin') {
            return redirect()->back()->withErrors([
                'role' => 'Anda tidak dapat mengubah role Anda sendiri menjadi Staff!',
            ]);
        }

        $user->update([
            'role' => $validated['role'],
        ]);

        return redirect()->back();
    }
}