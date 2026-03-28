<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - lannPark</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { 
            --primary: #3b82f6; 
            --primary-dark: #2563eb;
            --sidebar-bg: #1e293b; 
            --bg-body: #f8fafc; 
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        
        body { 
            background: var(--bg-body); 
            height: 100vh; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            background-image: radial-gradient(circle at 2px 2px, #e2e8f0 1px, transparent 0);
            background-size: 40px 40px; /* Memberikan efek titik-titik halus pada background */
        }

        .login-card { 
            background: #fff; 
            padding: 40px; 
            border-radius: 20px; 
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); 
            width: 100%;
            max-width: 400px; 
            border: 1px solid #e2e8f0;
        }

        .brand-section { text-align: center; margin-bottom: 30px; }
        
        /* Logo lannPark Style */
        .brand-logo { 
            font-size: 28px; 
            font-weight: 800; 
            color: var(--primary); 
            letter-spacing: -1px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 5px;
        }
        .brand-logo span { color: var(--sidebar-bg); }
        .brand-section p { color: #64748b; font-size: 14px; }

        .form-group { margin-bottom: 20px; }
        .form-group label { 
            display: block; 
            font-size: 13px; 
            font-weight: 600; 
            color: #475569; 
            margin-bottom: 8px; 
        }

        .input-wrapper { position: relative; }
        .input-wrapper i { 
            position: absolute; 
            left: 15px; 
            top: 50%; 
            transform: translateY(-50%); 
            color: #94a3b8; 
        }

        input { 
            width: 100%; 
            padding: 12px 15px 12px 45px; 
            border: 1px solid #e2e8f0; 
            border-radius: 12px; 
            font-size: 14px; 
            transition: 0.3s;
            outline: none;
            background: #fcfcfd;
        }

        input:focus { 
            border-color: var(--primary); 
            background: white;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); 
        }

        button { 
            width: 100%; 
            padding: 14px; 
            background-color: var(--sidebar-bg); 
            color: white; 
            border: none; 
            border-radius: 12px; 
            font-weight: 700; 
            font-size: 15px;
            cursor: pointer; 
            transition: 0.3s;
            margin-top: 10px;
        }

        button:hover { 
            background-color: #0f172a; 
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .error-message {
            background: #fee2e2;
            color: #ef4444;
            padding: 12px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 20px;
            border: 1px solid #fecaca;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand-section">
            <div class="brand-logo">
                <i class="fas fa-bolt"></i> lann<span>Park</span>
            </div>
            <p>Silakan masuk ke akun Anda</p>
        </div>
        
        @if(session()->has('loginError'))
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> {{ session('loginError') }}
            </div>
        @endif

        <form action="/login" method="post">
            @csrf
            <div class="form-group">
                <label>Username</label>
                <div class="input-wrapper">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" placeholder="Masukkan username" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Masukkan password" required>
                </div>
            </div>

            <button type="submit">
                Masuk<i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
            </button>
        </form>
    </div>
</body>
</html>