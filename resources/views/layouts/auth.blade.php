<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login') - ERP System</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6.5 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts - match app font -->
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --auth-bg: #f3f4f6;
            --auth-card-bg: #ffffff;
            --auth-border: rgba(0, 0, 0, 0.05);
            --auth-text-main: #111827;
            --auth-text-muted: #6b7280;
            --auth-accent: #0f766e;
            --auth-accent-strong: #0d9488;
            --auth-radius-lg: 12px;
            --auth-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DM Sans', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--auth-bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            color: var(--auth-text-main);
            -webkit-font-smoothing: antialiased;
        }
        
        .auth-container {
            background: var(--auth-card-bg);
            border-radius: var(--auth-radius-lg);
            box-shadow: var(--auth-shadow);
            overflow: hidden;
            max-width: 440px;
            width: 100%;
            border: 1px solid var(--auth-border);
        }
        
        .auth-header {
            padding: 24px 32px 20px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            background: #ffffff;
        }
        
        .auth-header-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        
        .auth-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .auth-logo {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #0f766e;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-weight: 700;
            font-size: 20px;
            box-shadow: 0 4px 6px -1px rgba(15, 118, 110, 0.2);
        }
        
        .auth-title h2 {
            font-weight: 700;
            margin-bottom: 2px;
            font-size: 18px;
            letter-spacing: -0.02em;
            color: #111827;
        }
        
        .auth-title p {
            font-size: 13px;
            color: var(--auth-text-muted);
            margin-bottom: 0;
        }
        
        .auth-badge {
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 999px;
            background: #f0fdfa;
            color: #0f766e;
            border: 1px solid #ccfbf1;
            font-weight: 600;
        }
        
        .auth-body {
            padding: 32px;
        }
        
        .form-label {
            font-weight: 500;
            color: #374151;
            margin-bottom: 6px;
            font-size: 14px;
        }
        
        .form-control {
            padding: 10px 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            background: #ffffff;
            color: var(--auth-text-main);
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        
        .form-control::placeholder {
            color: #9ca3af;
        }
        
        .form-control:focus {
            border-color: #0d9488;
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.1);
            outline: none;
        }
        
        .input-group-text {
            background: #f9fafb;
            border: 1px solid #d1d5db;
            border-right: none;
            border-radius: 8px 0 0 8px;
            color: #6b7280;
        }
        
        .input-group .form-control {
            border-left: none;
            border-radius: 0 8px 8px 0;
        }
        
        .btn-primary {
            background-color: #0f766e;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            color: #ffffff;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            width: 100%;
        }
        
        .btn-primary:hover {
            background-color: #0d9488;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(13, 148, 136, 0.2);
        }
        
        .btn-primary:active {
            transform: translateY(0);
            background-color: #0f766e;
        }
        
        .alert {
            border-radius: 8px;
            border: none;
            background: #fef2f2;
            color: #991b1b;
            font-size: 14px;
            padding: 12px 16px;
            margin-bottom: 24px;
            border-left: 4px solid #dc2626;
        }
        
        .text-muted {
            font-size: 13px;
            color: var(--auth-text-muted) !important;
        }
        
        @media (max-width: 576px) {
            .auth-container {
                border-radius: 12px;
            }
            
            .auth-header {
                padding: 20px 24px;
            }
            
            .auth-body {
                padding: 24px;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="auth-container">
        <div class="auth-header">
            <div class="auth-header-inner">
                <div class="auth-brand">
                    <div class="auth-logo">T</div>
                    <div class="auth-title">
                        <h2>Talukdar IT ERP</h2>
                        <p>@yield('title', 'Secure sign in to continue')</p>
                    </div>
                </div>
                <div class="auth-badge">
                    v1.0 · Production Ready
                </div>
            </div>
        </div>
        
        <div class="auth-body">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @yield('content')
        </div>
    </div>
    
    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>

