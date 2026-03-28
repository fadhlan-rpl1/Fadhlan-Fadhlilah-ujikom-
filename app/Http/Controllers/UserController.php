<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index() {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request) {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username' => 'required|string|unique:users|max:50',
            'password' => 'required|min:3',
            'role' => 'required|in:admin,petugas,owner'
        ]);

        User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        ActivityLog::create([
            'user_id' => auth()->user()->id,
            'aktivitas' => 'Tambah User',
        ]);

        return back()->with('success', 'User baru berhasil ditambahkan!');
    }

    // --- FUNGSI UPDATE (Untuk Fitur Edit) ---
    public function update(Request $request, $id) {
        $user = User::findOrFail($id);

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username' => "required|string|max:50|unique:users,username,{$id},id", // Abaikan username milik user ini sendiri saat cek unik
            'role' => 'required|in:admin,petugas,owner'
        ]);

        $data = [
            'nama_lengkap' => $request->nama_lengkap,
            'username' => $request->username,
            'role' => $request->role,
        ];

        // Jika password diisi, maka update passwordnya
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        ActivityLog::create([
            'user_id' => auth()->user()->id,
            'aktivitas' => 'Update User',
        ]);

        return back()->with('success', 'Data user berhasil diperbarui!');
    }

    public function destroy($id) {
        // Jangan biarkan admin menghapus dirinya sendiri
        if(auth()->user()->id == $id) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri!');
        }

        $user = User::findOrFail($id);
        $nama = $user->username;

        ActivityLog::create([
            'user_id' => auth()->user()->id,
            'aktivitas' => 'Hapus User',
        ]);

        $user->delete();
        return back()->with('success', 'User berhasil dihapus!');
    }
}