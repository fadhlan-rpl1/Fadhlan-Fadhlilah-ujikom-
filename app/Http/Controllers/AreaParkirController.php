<?php

namespace App\Http\Controllers;

use App\Models\AreaParkir;
use App\Models\ActivityLog; // <-- Pastikan ini sesuai nama model log kamu
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AreaParkirController extends Controller
{
    public function index()
    {
        // 1. Menggunakan paginate(5) agar tombol halaman di view lannPark muncul
        $areas = AreaParkir::latest()->paginate(5);
        
        return view('admin.area.index', compact('areas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_area' => 'required|string|max:100',
            'kapasitas' => 'required|numeric|min:1',
        ]);

        $area = AreaParkir::create([
            'nama_area' => $request->nama_area,
            'kapasitas' => $request->kapasitas,
            'terisi' => 0 
        ]);

        // 📝 CATAT LOG: Tambah Area
        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => 'Tambah Area',
            'description' => 'Admin menambah area baru: ' . $request->nama_area . ' dengan kapasitas ' . $request->kapasitas
        ]);

        return back()->with('success', '🏢 Area parkir berhasil ditambahkan!');
    }

    // --- FITUR BARU: UPDATE AREA & KAPASITAS ---
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_area' => 'required|string|max:100',
            'kapasitas' => 'required|numeric|min:0',
        ]);

        $area = AreaParkir::findOrFail($id);
        $namaLama = $area->nama_area;
        $kapasitasLama = $area->kapasitas;

        $area->update([
            'nama_area' => $request->nama_area,
            'kapasitas' => $request->kapasitas,
        ]);

        // 📝 CATAT LOG: Edit Area
        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => 'Edit Area',
            'description' => "Admin mengubah area $namaLama (Kap: $kapasitasLama) menjadi $request->nama_area (Kap: $request->kapasitas)"
        ]);

        return back()->with('success', '✏️ Data area parkir berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $area = AreaParkir::findOrFail($id);
        $namaArea = $area->nama_area;

        // 📝 CATAT LOG: Hapus Area
        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => 'Hapus Area',
            'description' => 'Admin menghapus area parkir: ' . $namaArea
        ]);

        $area->delete();

        return back()->with('success', '🗑️ Area parkir berhasil dihapus!');
    }
}