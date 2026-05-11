<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $newUser = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        \App\Models\ActivityLog::log('manajemen_admin', 'menambahkan admin baru: **' . $newUser->name . '**.');

        return redirect()->route('admin.users.index')->with('success', 'Admin berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ];

        // Jika password diisi, maka divalidasi
        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8|confirmed';
        }

        $request->validate($rules);

        $user->name = $request->name;
        $user->email = $request->email;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();

        \App\Models\ActivityLog::log('manajemen_admin', 'memperbarui data admin: **' . $user->name . '**.');

        return redirect()->route('admin.users.index')->with('success', 'Data Admin berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Proteksi agar tidak bisa menghapus diri sendiri
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $deletedName = $user->name;
        $user->delete();

        \App\Models\ActivityLog::log('manajemen_admin', 'menghapus akun admin: **' . $deletedName . '**.');

        return redirect()->route('admin.users.index')->with('success', 'Admin berhasil dihapus.');
    }
}
