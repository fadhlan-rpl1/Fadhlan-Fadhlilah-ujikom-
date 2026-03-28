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
            'role' => $request->role,
            'status_aktif' => 1
        ]);

        ActivityLog::create([
            'user_id' => auth()->user()->id_user,
            'activity' => 'Tambah User',
            'description' => "Menambahkan user baru: {$request->username} sebagai {$request->role}"
        ]);

        return back()->with('success', 'User baru berhasil ditambahkan!');
    }

    // --- FUNGSI UPDATE (Untuk Fitur Edit) ---
    public function update(Request $request, $id) {
        $user = User::findOrFail($id);

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username' => "required|string|max:50|unique:users,username,{$id},id_user", // Abaikan username milik user ini sendiri saat cek unik
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
            'user_id' => auth()->user()->id_user,
            'activity' => 'Update User',
            'description' => "Memperbarui data user: {$request->username}"
        ]);

        return back()->with('success', 'Data user berhasil diperbarui!');
    }

    public function destroy($id) {
        // Jangan biarkan admin menghapus dirinya sendiri
        if(auth()->user()->id_user == $id) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri!');
        }

        $user = User::findOrFail($id);
        $nama = $user->username;

        ActivityLog::create([
            'user_id' => auth()->user()->id_user,
            'activity' => 'Hapus User',
            'description' => "Menghapus akun user: {$nama}"
        ]);

        $user->delete();
        return back()->with('success', 'User berhasil dihapus!');
    }
}