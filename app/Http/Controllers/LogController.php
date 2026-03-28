<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index()
    {
        // Mengambil data log terbaru, 10 data per halaman
        $logs = ActivityLog::with('user')->latest()->paginate(10);
        
        return view('admin.logs.index', compact('logs'));
    }
}