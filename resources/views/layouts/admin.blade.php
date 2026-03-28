<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>lannPark - @yield('title', 'Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { 
            --primary: #3b82f6; 
            --primary-dark: #2563eb;
            --sidebar-bg: #1e293b; 
            --bg-body: #f8fafc; 
            --text-main: #1e293b; 
            --text-muted: #64748b; 
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: var(--bg-body); color: var(--text-main); line-height: 1.6; }

        /* --- TOPBAR --- */
        .topbar { 
            height: 70px; background: white; 
            display: flex; align-items: center; 
            justify-content: space-between; padding: 0 30px; position: fixed; 
            width: 100%; z-index: 1000; border-bottom: 1px solid #e2e8f0; 
        }
        
        /* Gaya Logo lannPark */
        .topbar-left { 
            font-size: 22px; 
            font-weight: 800; 
            color: var(--primary); 
            display: flex; 
            align-items: center; 
            gap: 8px; 
            letter-spacing: -0.5px;
        }
        .topbar-left span { color: var(--sidebar-bg); }
        
        /* --- SIDEBAR --- */
        .sidebar { width: 260px; height: 100vh; background: var(--sidebar-bg); padding: 90px 15px 20px; position: fixed; left: 0; top: 0; }
        .sidebar a { 
            display: flex; align-items: center; gap: 12px; color: #94a3b8; 
            text-decoration: none; padding: 12px 15px; margin-bottom: 8px; 
            border-radius: 12px; font-size: 14px; font-weight: 500; transition: 0.3s; 
        }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.1); color: white; }
        
        /* --- CONTENT AREA --- */
        .content { margin-left: 260px; padding: 100px 30px 40px; min-height: 100vh; }
        
        /* --- CARD & TABLE --- */
        .card { background: white; padding: 25px; border-radius: 15px; border: 1px solid #e2e8f0; margin-bottom: 25px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
        .table-container { background: white; border-radius: 15px; border: 1px solid #e2e8f0; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8fafc; text-align: left; padding: 15px 20px; font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; border-bottom: 1px solid #e2e8f0; }
        td { padding: 15px 20px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }

        @media (max-width: 768px) { .sidebar { transform: translateX(-100%); } .content { margin-left: 0; } }
    </style>
</head>
<body>

    <div class="topbar">
        <div class="topbar-left">
            <i class="fas fa-bolt"></i> lann<span>Park</span>
        </div>
        <div class="topbar-right">
            <div style="display:flex; align-items:center; gap:15px;">
                <div style="font-size: 14px; color: var(--text-muted)">
                    Halo, <b>{{ auth()->user()->nama_lengkap }}</b> 
                    <span style="background: var(--primary); color: white; padding: 2px 8px; border-radius: 6px; font-size: 10px; font-weight: bold; text-transform: uppercase; margin-left: 5px;">
                        {{ auth()->user()->role }}
                    </span>
                </div>
                <form action="/logout" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" style="border:none; background:#fee2e2; color:#ef4444; padding: 8px 15px; border-radius: 8px; cursor:pointer; font-weight:700; font-size:12px; transition: 0.2s;" onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'">
                        <i class="fas fa-power-off"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="sidebar">
        @php $role = auth()->user()->role; @endphp
        
        <a href="/{{ $role }}/dashboard" class="{{ Request::is($role.'/dashboard') ? 'active' : '' }}">
            <i class="fas fa-th-large"></i> Dashboard
        </a>

        @if($role == 'admin')
            <a href="/admin/transaksi" class="{{ Request::is('admin/transaksi*') ? 'active' : '' }}"><i class="fas fa-car-side"></i> Data Kendaraan</a>
            <a href="/admin/tarif" class="{{ Request::is('admin/tarif*') ? 'active' : '' }}"><i class="fas fa-tags"></i> Kelola Tarif</a>
            <a href="/admin/area" class="{{ Request::is('admin/area*') ? 'active' : '' }}"><i class="fas fa-map-marker-alt"></i> Area Parkir</a>
            <a href="/admin/users" class="{{ Request::is('admin/users*') ? 'active' : '' }}"><i class="fas fa-user-shield"></i> Kelola User</a>
            <a href="/admin/logs" class="{{ Request::is('admin/logs*') ? 'active' : '' }}"><i class="fas fa-list-ul"></i> Log Aktivitas</a>
        @endif
    </div>

    <div class="content">
        @yield('content')
    </div>

</body>
</html>