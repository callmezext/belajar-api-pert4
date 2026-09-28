<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Enchant AI Gateway')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-base: #f8fafc;
            --bg-surface: #ffffff;
            --bg-muted: #f1f5f9;
            --bg-hover: #e2e8f0;
            --border-light: #e2e8f0;
            --border-medium: #cbd5e1;
            
            --text-heading: #0f172a;
            --text-body: #334155;
            --text-muted: #64748b;
            --text-white: #ffffff;

            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --primary-light: #eef2ff;
            --primary-border: #c7d2fe;

            --tier-free-bg: #f8fafc;
            --tier-free-text: #475569;
            --tier-free-border: #cbd5e1;

            --tier-starter-bg: #eff6ff;
            --tier-starter-text: #1d4ed8;
            --tier-starter-border: #bfdbfe;

            --tier-super-bg: #faf5ff;
            --tier-super-text: #6b21a8;
            --tier-super-border: #e9d5ff;

            --success-bg: #ecfdf5;
            --success-text: #065f46;
            --success-border: #a7f3d0;

            --danger-bg: #fef2f2;
            --danger-text: #991b1b;
            --danger-border: #fecaca;

            --warning-bg: #fffbeb;
            --warning-text: #92400e;
            --warning-border: #fde68a;

            --shadow-xs: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -2px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -4px rgba(0, 0, 0, 0.05);

            --radius-xs: 4px;
            --radius-sm: 6px;
            --radius-md: 10px;
            --radius-lg: 16px;

            --font-sans: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            --font-mono: 'JetBrains Mono', ui-monospace, SFMono-Regular, Consolas, monospace;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-sans);
            background-color: var(--bg-base);
            color: var(--text-body);
            line-height: 1.55;
            font-size: 15px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            -webkit-font-smoothing: antialiased;
        }

        a {
            color: var(--primary);
            text-decoration: none;
            transition: all 0.15s ease;
        }

        a:hover {
            color: var(--primary-hover);
        }

        /* Top Header Navigation */
        .site-header {
            background-color: var(--bg-surface);
            border-bottom: 1px solid var(--border-light);
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: var(--shadow-xs);
        }

        .header-inner {
            max-width: 1240px;
            margin: 0 auto;
            padding: 0 1.5rem;
            height: 68px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--text-heading);
            font-weight: 800;
            font-size: 1.2rem;
            letter-spacing: -0.02em;
        }

        .brand-icon {
            width: 32px;
            height: 32px;
            background: var(--text-heading);
            color: #ffffff;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1rem;
            box-shadow: var(--shadow-xs);
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            list-style: none;
        }

        .nav-item-link {
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.925rem;
            padding: 0.5rem 0.85rem;
            border-radius: var(--radius-sm);
            transition: all 0.15s ease;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .nav-item-link:hover {
            color: var(--text-heading);
            background-color: var(--bg-muted);
        }

        .nav-item-link.active {
            color: var(--primary);
            background-color: var(--primary-light);
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-weight: 600;
            padding: 0.6rem 1.2rem;
            font-size: 0.925rem;
            border-radius: var(--radius-sm);
            cursor: pointer;
            border: 1px solid transparent;
            font-family: inherit;
            transition: all 0.15s ease;
            text-align: center;
            white-space: nowrap;
        }

        .btn:focus-visible {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }

        .btn-primary {
            background-color: var(--primary);
            color: #ffffff;
            box-shadow: 0 1px 2px rgba(79, 70, 229, 0.2);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            color: #ffffff;
            box-shadow: 0 3px 6px rgba(79, 70, 229, 0.3);
        }

        .btn-secondary {
            background-color: var(--bg-surface);
            color: var(--text-body);
            border-color: var(--border-medium);
            box-shadow: var(--shadow-xs);
        }

        .btn-secondary:hover {
            background-color: var(--bg-muted);
            color: var(--text-heading);
            border-color: var(--text-muted);
        }

        .btn-danger {
            background-color: #dc2626;
            color: #ffffff;
        }

        .btn-danger:hover {
            background-color: #b91c1c;
        }

        .btn-sm {
            padding: 0.35rem 0.75rem;
            font-size: 0.85rem;
        }

        .btn-lg {
            padding: 0.75rem 1.6rem;
            font-size: 1rem;
        }

        /* Layout Container */
        .main-container {
            max-width: 1240px;
            margin: 0 auto;
            padding: 2rem 1.5rem 4rem;
            width: 100%;
            flex: 1;
        }

        /* Cards */
        .card {
            background: var(--bg-surface);
            border: 1px solid var(--border-light);
            border-radius: var(--radius-md);
            padding: 1.5rem;
            box-shadow: var(--shadow-xs);
            margin-bottom: 1.5rem;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
            padding-bottom: 0.85rem;
            border-bottom: 1px solid var(--border-light);
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .card-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text-heading);
            letter-spacing: -0.01em;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.65rem;
            font-size: 0.75rem;
            font-weight: 700;
            border-radius: var(--radius-sm);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .badge-free {
            background-color: var(--tier-free-bg);
            color: var(--tier-free-text);
            border: 1px solid var(--tier-free-border);
        }

        .badge-starter {
            background-color: var(--tier-starter-bg);
            color: var(--tier-starter-text);
            border: 1px solid var(--tier-starter-border);
        }

        .badge-super {
            background-color: var(--tier-super-bg);
            color: var(--tier-super-text);
            border: 1px solid var(--tier-super-border);
        }

        .badge-admin {
            background-color: var(--warning-bg);
            color: var(--warning-text);
            border: 1px solid var(--warning-border);
        }

        .badge-success {
            background-color: var(--success-bg);
            color: var(--success-text);
            border: 1px solid var(--success-border);
        }

        .badge-danger {
            background-color: var(--danger-bg);
            color: var(--danger-text);
            border: 1px solid var(--danger-border);
        }

        /* Flash Alerts */
        .alert-box {
            padding: 1rem 1.25rem;
            border-radius: var(--radius-sm);
            margin-bottom: 1.5rem;
            font-size: 0.925rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .alert-box.success {
            background-color: var(--success-bg);
            color: var(--success-text);
            border: 1px solid var(--success-border);
        }

        .alert-box.error {
            background-color: var(--danger-bg);
            color: var(--danger-text);
            border: 1px solid var(--danger-border);
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.4rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-heading);
        }

        .form-control {
            width: 100%;
            padding: 0.65rem 0.9rem;
            font-size: 0.925rem;
            border: 1px solid var(--border-medium);
            border-radius: var(--radius-sm);
            background-color: var(--bg-surface);
            color: var(--text-heading);
            font-family: inherit;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
        }

        .form-hint {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 0.35rem;
        }

        /* Tables */
        .table-wrap {
            overflow-x: auto;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border-light);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.925rem;
        }

        th {
            background-color: var(--bg-muted);
            color: var(--text-muted);
            font-weight: 700;
            padding: 0.85rem 1.1rem;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--border-light);
        }

        td {
            padding: 0.95rem 1.1rem;
            border-bottom: 1px solid var(--border-light);
            color: var(--text-body);
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background-color: #fafbfc;
        }

        /* Code display */
        .code-snippet {
            background-color: #0f172a;
            color: #e2e8f0;
            font-family: var(--font-mono);
            font-size: 0.85rem;
            padding: 1.1rem;
            border-radius: var(--radius-sm);
            overflow-x: auto;
            line-height: 1.6;
        }

        /* Progress Bar */
        .progress-track {
            background-color: var(--bg-muted);
            height: 10px;
            border-radius: 999px;
            overflow: hidden;
            margin: 0.5rem 0;
            border: 1px solid var(--border-light);
        }

        .progress-fill {
            height: 100%;
            background-color: var(--primary);
            transition: width 0.3s ease;
        }

        /* Modals */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 100;
            padding: 1rem;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-card {
            background-color: var(--bg-surface);
            border-radius: var(--radius-md);
            max-width: 580px;
            width: 100%;
            border: 1px solid var(--border-light);
            box-shadow: var(--shadow-lg);
            padding: 1.75rem;
            max-height: 90vh;
            overflow-y: auto;
            animation: modalIn 0.15s ease-out;
        }

        @keyframes modalIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--border-light);
        }

        .modal-close-btn {
            background: none;
            border: none;
            font-size: 1.35rem;
            cursor: pointer;
            color: var(--text-muted);
            padding: 0.25rem;
            line-height: 1;
            border-radius: var(--radius-xs);
        }

        .modal-close-btn:hover {
            color: var(--text-heading);
            background-color: var(--bg-muted);
        }

        /* Footer */
        .site-footer {
            margin-top: auto;
            background-color: var(--bg-surface);
            border-top: 1px solid var(--border-light);
            padding: 2rem 1.5rem;
            text-align: center;
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-inner {
                padding: 0 1rem;
                height: auto;
                min-height: 64px;
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            .nav-menu {
                flex-wrap: wrap;
            }
            .main-container {
                padding: 1.25rem 1rem 3rem;
            }
        }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="header-inner">
            <a href="{{ route('home') }}" class="brand-link">
                <div class="brand-icon">E</div>
                <div>Enchant <span style="color:var(--primary); font-weight:700;">Gateway</span></div>
            </a>

            <nav>
                <ul class="nav-menu">
                    <li><a href="{{ route('home') }}" class="nav-item-link {{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a></li>

                    @if(session()->has('jwt_token'))
                        @php $sessUser = session('user'); @endphp
                        <li><a href="{{ route('dashboard') }}" class="nav-item-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard Member</a></li>

                        @if(($sessUser['role'] ?? '') === 'admin')
                            <li><a href="{{ route('admin.index') }}" class="nav-item-link {{ request()->routeIs('admin.*') ? 'active' : '' }}" style="color:#b45309;">Panel Admin</a></li>
                        @endif

                        <li style="margin-left: 0.5rem;">
                            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-secondary btn-sm">
                                    Keluar ({{ $sessUser['name'] ?? 'User' }})
                                </button>
                            </form>
                        </li>
                    @else
                        <li><a href="{{ route('login') }}" class="nav-item-link {{ request()->routeIs('login') ? 'active' : '' }}">Masuk</a></li>
                        <li style="margin-left: 0.5rem;">
                            <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Daftar Akun</a>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
    </header>

    <main class="main-container">
        @if(session('success'))
            <div class="alert-box success" role="alert">
                <span style="font-weight:700;">Sukses:</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="alert-box error" role="alert">
                <span style="font-weight:700;">Pemberitahuan:</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="site-footer">
        <div style="max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <strong>Enchant AI Gateway</strong> &bull; Multi-Tenant AI Model Routing &amp; Token Limiter
            </div>
            <div>
                Backend Express: <code style="font-family:var(--font-mono); font-size:0.8rem; background:var(--bg-muted); padding:0.15rem 0.4rem;">Port 5000</code> &bull; Frontend Laravel: <code style="font-family:var(--font-mono); font-size:0.8rem; background:var(--bg-muted); padding:0.15rem 0.4rem;">Port 8001</code>
            </div>
        </div>
    </footer>

    @yield('scripts')
</body>
</html>
