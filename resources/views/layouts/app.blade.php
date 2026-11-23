{{-- resources/views/layouts/app.blade.php --}}
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'TREND FUTSAL') }}</title>
    
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Poppins:400,500,600,700,800|Inter:400,500,600,700" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    @viteReactRefresh
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
    @stack('styles')
    
    <style>
        :root {
            --sidebar-width: 280px;
            --bg-dark: #1a1d23;
            --bg-darker: #13151a;
            --bg-light: #f8f9fa;
            --text-dark: #2d3436;
            --text-muted: #636e72;
            --border-color: #e8eaed;
            --accent-primary: #4f46e5;
            --accent-hover: #4338ca;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.06);
            --shadow-md: 0 4px 6px rgba(0,0,0,0.07);
            --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            font-size: 14px;
        }
        
        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: var(--bg-dark);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .sidebar-header {
            padding: 1.75rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .sidebar-header-content {
            display: flex;
            flex-direction: column;
        }
        
        /* Sidebar Toggle inside header */
        .sidebar-toggle {
            background: rgba(255,255,255,0.1);
            color: white;
            border: 1px solid rgba(255,255,255,0.15);
            width: 36px;
            height: 36px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .sidebar-toggle:hover {
            background: rgba(255,255,255,0.15);
            border-color: rgba(255,255,255,0.25);
            transform: scale(1.05);
        }
        
        .sidebar-toggle:active {
            transform: scale(0.95);
        }
        
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 8px;
        }
        
        .sidebar-logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--accent-primary), #7c3aed);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }
        
        .sidebar-header h4 {
            color: white;
            margin: 0;
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: -0.02em;
        }
        
        .sidebar-header small {
            color: #9ca3af;
            font-size: 0.8rem;
            padding-left: 52px;
            display: block;
            font-weight: 500;
        }
        
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 1.25rem 0;
        }
        
        .sidebar-nav::-webkit-scrollbar {
            width: 5px;
        }
        
        .sidebar-nav::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
        }
        
        .sidebar-nav::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.15);
        }
        
        .sidebar .nav-link {
            color: #9ca3af;
            padding: 0.75rem 1.25rem;
            margin: 3px 12px;
            border-radius: 10px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            position: relative;
        }
        
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 12px;
            text-align: center;
            font-size: 1.1rem;
        }
        
        .sidebar .nav-link:hover {
            background-color: rgba(255,255,255,0.05);
            color: #ffffff;
        }
        
        .sidebar .nav-link.active {
            background: rgba(79, 70, 229, 0.15);
            color: #ffffff;
            font-weight: 600;
        }
        
        .sidebar .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 24px;
            background: var(--accent-primary);
            border-radius: 0 4px 4px 0;
        }
        
        .sidebar-divider {
            border-top: 1px solid rgba(255,255,255,0.08);
            margin: 1rem 1.25rem;
        }
        
        .sidebar-title {
            padding: 1rem 1.5rem 0.5rem;
            color: #6b7280;
            font-size: 0.7rem;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.8px;
        }
        
        .sidebar-footer {
            flex-shrink: 0;
            padding: 1.25rem;
            border-top: 1px solid rgba(255,255,255,0.08);
        }
        
        .sidebar-footer .btn-logout {
            width: 100%;
            padding: 0.75rem;
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.2s;
        }
        
        .sidebar-footer .btn-logout:hover {
            background: rgba(239, 68, 68, 0.15);
            border-color: rgba(239, 68, 68, 0.3);
            transform: translateY(-1px);
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            background: var(--bg-light);
        }
        
        .top-navbar {
            background: white;
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 2rem;
            margin-bottom: 0;
            position: sticky;
            top: 0;
            z-index: 100;
            transition: padding-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .sidebar.collapsed ~ .main-content .top-navbar {
            padding-left: 75px;
        }
        
        .top-navbar h5 {
            margin: 0;
            font-weight: 700;
            color: var(--text-dark);
            font-size: 1.35rem;
            letter-spacing: -0.03em;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .user-info-text {
            text-align: right;
        }
        
        .user-info-text .fw-bold {
            font-size: 0.9rem;
            color: var(--text-dark);
            font-weight: 600;
        }
        
        .user-info-text small {
            color: var(--text-muted);
            font-size: 0.8rem;
        }
        
        .user-avatar {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--accent-primary), #7c3aed);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1rem;
            box-shadow: var(--shadow-sm);
        }
        
        .badge {
            padding: 0.4em 0.75em;
            font-weight: 600;
            font-size: 0.75rem;
            border-radius: 6px;
        }
        
        .badge.bg-primary {
            background: rgba(79, 70, 229, 0.1) !important;
            color: var(--accent-primary);
        }
        
        /* Content Area */
        .container-fluid {
            padding: 2rem 2rem 3rem;
        }
        
        /* Card Styles */
        .card {
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 1.5rem;
            background: white;
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid var(--border-color);
            border-radius: 12px 12px 0 0 !important;
            font-weight: 700;
            padding: 1.25rem 1.5rem;
            color: var(--text-dark);
            font-size: 1.05rem;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        /* Buttons */
        .btn-primary {
            background: var(--accent-primary);
            border: none;
            padding: 0.625rem 1.25rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s;
        }
        
        .btn-primary:hover {
            background: var(--accent-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }
        
        /* Alerts */
        .alert {
            border-radius: 10px;
            border: none;
            padding: 1rem 1.25rem;
            box-shadow: var(--shadow-sm);
        }
        
        .alert-success {
            background: #f0fdf4;
            color: #166534;
            border-left: 4px solid #22c55e;
        }
        
        .alert-danger {
            background: #fef2f2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }
        
        /* Sidebar collapsed state */
        .sidebar.collapsed {
            transform: translateX(-100%);
        }
        
        .sidebar.collapsed ~ .main-content {
            margin-left: 0;
        }
        
        /* Toggle button when sidebar collapsed */
        .sidebar-toggle-fixed {
            position: fixed;
            top: 24px;
            left: 16px;
            z-index: 1001;
            background: var(--bg-dark);
            color: white;
            border: 1px solid rgba(255,255,255,0.15);
            width: 40px;
            height: 40px;
            border-radius: 10px;
            box-shadow: var(--shadow-md);
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            align-items: center;
            justify-content: center;
            display: none;
        }
        
        .sidebar.collapsed ~ .sidebar-toggle-fixed {
            display: flex;
        }
        
        .sidebar-toggle-fixed:hover {
            background: var(--bg-darker);
            transform: scale(1.05);
        }
        
        .sidebar-toggle-fixed:active {
            transform: scale(0.95);
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
                box-shadow: 4px 0 20px rgba(0,0,0,0.2);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .top-navbar {
                padding-left: 2rem;
            }
            
            .container-fluid {
                padding: 1.5rem 1rem;
            }
            
            .sidebar-toggle-fixed {
                display: flex !important;
            }
        }
        
        /* Utility Classes */
        .text-muted {
            color: var(--text-muted) !important;
        }
        
        /* Table Improvements */
        .table {
            font-size: 0.9rem;
        }
        
        .table thead th {
            border-bottom: 2px solid var(--border-color);
            color: var(--text-muted);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        /* Shared POS design system */
        :root {
            --bs-primary: #16805c;
            --bs-primary-rgb: 22, 128, 92;
            --bs-success: #16805c;
            --bs-success-rgb: 22, 128, 92;
            --sidebar-width: 264px;
            --bg-dark: #102a22;
            --bg-darker: #0a2019;
            --bg-light: #f5f8f6;
            --text-dark: #172a21;
            --text-muted: #718077;
            --border-color: #e4ebe6;
            --accent-primary: #16805c;
            --accent-hover: #106b4b;
            --shadow-sm: 0 2px 8px rgba(22, 50, 36, .045);
            --shadow-md: 0 8px 22px rgba(22, 50, 36, .08);
            --shadow-lg: 0 18px 45px rgba(22, 50, 36, .12);
        }
        body { background:var(--bg-light); color:var(--text-dark); letter-spacing:-.005em; }
        .sidebar { background:linear-gradient(180deg,#14392d 0%,#102a22 58%,#0c241c 100%); box-shadow:8px 0 28px rgba(10,32,25,.08); }
        .sidebar-logo-icon { background:linear-gradient(135deg,#27a879,#147653); box-shadow:0 8px 18px rgba(21,137,94,.25); }
        .sidebar .nav-link { margin:3px 10px; padding:.72rem 1rem; border-radius:10px; }
        .sidebar .nav-link.active { background:rgba(54,178,128,.17); color:#fff; }
        .sidebar .nav-link.active::before { background:#54d39d; }
        .sidebar .nav-link i { font-size:1rem; }
        .sidebar-title { letter-spacing:1.2px; }
        .main-content { background:#f5f8f6; }
        .top-navbar { min-height:76px; padding:1rem 2rem; box-shadow:0 1px 0 rgba(24,47,34,.025); }
        .top-navbar h5 { font-size:1.08rem; letter-spacing:-.02em; }
        .user-avatar { background:linear-gradient(135deg,#20a475,#126e50); border-radius:50%; }
        .container-fluid { max-width:1680px; padding:1.65rem 2rem 3rem; }
        .card { border:1px solid #e5ece7; border-radius:15px; box-shadow:0 4px 18px rgba(26,54,38,.035); overflow:hidden; }
        .card-header { padding:1.05rem 1.3rem; font-size:.96rem; background:#fff; }
        .card-body { padding:1.3rem; }
        .card-footer { background:#fbfdfb; border-color:#e8eee9; }
        .btn { border-radius:9px; font-weight:650; letter-spacing:-.01em; transition:transform .16s ease,box-shadow .16s ease,background .16s ease; }
        .btn:hover { transform:translateY(-1px); }
        .btn-primary { background:#16805c; box-shadow:0 4px 10px rgba(22,128,92,.14); }
        .btn-primary:hover { background:#106b4b; box-shadow:0 7px 16px rgba(22,128,92,.2); }
        .btn-success { background:#16805c; border-color:#16805c; }
        .btn-info { color:#126b53; background:#e8f5ef; border-color:#e8f5ef; }
        .btn-info:hover { color:#0c553e; background:#d8eee3; border-color:#d8eee3; }
        .btn-warning { color:#715112; background:#fff6df; border-color:#fff0c7; }
        .btn-danger { background:#c94e4e; border-color:#c94e4e; }
        .form-control,.form-select { border-color:#dce6df; border-radius:9px; min-height:40px; }
        .form-control:focus,.form-select:focus { border-color:#74b99a; box-shadow:0 0 0 .2rem rgba(22,128,92,.12); }
        .form-label { color:#33483b; font-weight:650; font-size:.86rem; }
        .table { color:#314239; vertical-align:middle; margin-bottom:0; }
        .table thead th { background:#f7faf8; color:#738078; border-bottom:1px solid #e4ebe6; padding:.85rem .8rem; white-space:nowrap; }
        .table td { padding:.8rem; border-color:#edf1ee; }
        .table-hover tbody tr:hover { --bs-table-accent-bg:#f7fbf8; }
        .table-responsive { border-radius:10px; }
        .badge { border-radius:999px; padding:.42em .78em; letter-spacing:.01em; }
        .badge.bg-success { background:#e4f5eb!important; color:#187349; }
        .badge.bg-primary { background:#e4f5eb!important; color:#187349!important; }
        .badge.bg-warning { background:#fff2d2!important; color:#805b10!important; }
        .badge.bg-danger { background:#fdeaea!important; color:#a83d3d; }
        .badge.bg-info { background:#e4f3f2!important; color:#196d69; }
        .badge.bg-secondary { background:#edf1ee!important; color:#5f6d64; }
        .alert { border:1px solid transparent; }
        .modal-content { border:1px solid #e2eae4; border-radius:15px; box-shadow:var(--shadow-lg); }
        .modal-header,.modal-footer { border-color:#edf1ee; }
        .accordion-item { border-color:#e5ece7; overflow:hidden; }
        .accordion-button { color:#263b2e; font-weight:700; }
        .accordion-button:not(.collapsed) { background:#edf7f1; color:#176d4d; box-shadow:none; }
        .pagination { gap:.25rem; }
        .page-link { border-radius:8px!important; border-color:#e3ebe5; color:#247553; }
        .page-item.active .page-link { background:#16805c; border-color:#16805c; }
        .sidebar-footer .btn-logout { color:#ffc0bb; border-color:rgba(255,160,150,.18); background:rgba(225,91,77,.1); }
        .guest-shell { min-height:100vh; display:grid; place-items:center; padding:2rem 1rem; background:radial-gradient(ellipse at 75% 5%,#e2f4e9 0,transparent 38%),linear-gradient(145deg,#f8fbf8,#eef5f0); }
        .guest-card { width:min(100%,460px); border:1px solid #e1ebe4; border-radius:20px; background:#fff; box-shadow:0 24px 70px rgba(18,58,38,.12); overflow:hidden; }
        .guest-card-head { padding:2rem 2rem 1.2rem; text-align:center; }
        .guest-brand { width:58px;height:58px;margin:0 auto 1rem;display:grid;place-items:center;border-radius:17px;background:linear-gradient(135deg,#26a578,#126e50);color:#fff;font-size:1.5rem;box-shadow:0 8px 18px rgba(22,128,92,.2); }
        .guest-card .card-body { padding:1.5rem 2rem 2rem; }
        @media(max-width:768px){.container-fluid{padding:1.25rem 1rem 2rem}.top-navbar{min-height:68px;padding:.85rem 1rem .85rem 4rem}.top-navbar h5{font-size:1rem}.user-info{gap:8px}.user-avatar{width:36px;height:36px}.user-info .badge{display:none}.card-body{padding:1rem}.table{font-size:.84rem}}
        /* React application shell */
        #react-sidebar-root { display:flex; width:100%; height:100%; min-height:0; }
        .react-sidebar { display:flex; flex-direction:column; width:100%; min-height:0; color:#fff; }
        .react-sidebar-header { display:flex; align-items:center; justify-content:space-between; padding:1.45rem 1.25rem 1.1rem; }
        .react-brand { display:flex; align-items:center; gap:.75rem; min-width:0; color:#fff; text-decoration:none; }
        .react-brand:hover { color:#fff; }
        .react-brand-mark { display:grid; place-items:center; width:42px; height:42px; flex:0 0 42px; border-radius:13px; background:linear-gradient(135deg,#32bc88,#137552); box-shadow:0 8px 20px rgba(18,125,84,.3); font-size:1.1rem; }
        .react-brand-copy { display:grid; gap:.12rem; min-width:0; }
        .react-brand-copy strong { overflow:hidden; font-size:.95rem; font-weight:750; text-overflow:ellipsis; white-space:nowrap; }
        .react-brand-copy small { color:#91b0a1; font-size:.62rem; font-weight:700; letter-spacing:1.25px; }
        .react-sidebar-header .sidebar-toggle { display:none; width:34px; height:34px; border:0; border-radius:9px; color:#cfe4d8; background:rgba(255,255,255,.08); }
        .react-workspace-pill { display:flex; align-items:center; gap:.5rem; margin:0 1.25rem 1.15rem; padding:.65rem .75rem; border:1px solid rgba(195,230,210,.12); border-radius:10px; background:rgba(255,255,255,.045); color:#cfdfd5; font-size:.75rem; }
        .react-online-dot { width:7px; height:7px; flex:0 0 7px; border-radius:50%; background:#46d899; box-shadow:0 0 0 3px rgba(70,216,153,.13); }
        .react-role { margin-left:auto; padding:.22rem .45rem; border-radius:999px; background:rgba(255,255,255,.08); color:#b9d0c4; font-size:.63rem; font-weight:700; }
        .react-nav-search { display:flex; align-items:center; gap:.6rem; margin:0 1.25rem 1rem; padding:.55rem .7rem; border:1px solid rgba(195,230,210,.13); border-radius:9px; color:#9fb8aa; background:rgba(3,20,14,.2); }
        .react-nav-search input { width:100%; min-width:0; padding:0; border:0; outline:0; color:#fff; background:transparent; font:inherit; font-size:.78rem; }
        .react-nav-search input::placeholder { color:#8aa497; }
        .react-nav-search kbd,.react-command-trigger kbd,.react-command-input kbd,.react-command-footer kbd { padding:.15rem .35rem; border:1px solid rgba(255,255,255,.16); border-radius:5px; color:#9eb7a8; background:rgba(255,255,255,.06); font-size:.65rem; white-space:nowrap; }
        .react-nav { flex:1; min-height:0; overflow-y:auto; padding:0 .65rem .75rem; }
        .react-nav-group { margin-bottom:1.05rem; }
        .react-nav-label { padding:.35rem .72rem .45rem; color:#779587; font-size:.64rem; font-weight:750; letter-spacing:1.25px; text-transform:uppercase; }
        .react-nav .nav-link { position:relative; display:flex; align-items:center; min-height:42px; gap:.7rem; margin:.15rem 0; padding:.62rem .75rem; color:#a9bdb2; font-size:.82rem; font-weight:550; }
        .react-nav .nav-link i { width:19px; margin:0; color:#829d8e; font-size:.94rem; }
        .react-nav .nav-link:hover { color:#fff; background:rgba(255,255,255,.07); }
        .react-nav .nav-link.active { color:#fff; background:linear-gradient(90deg,rgba(36,159,108,.28),rgba(36,159,108,.12)); box-shadow:inset 0 0 0 1px rgba(87,202,147,.1); }
        .react-nav .nav-link.active::before { width:3px; height:20px; background:#57d59a; }
        .react-nav .nav-link.active i { color:#71dbaa; }
        .react-nav-plus { display:grid; place-items:center; width:19px; height:19px; margin-left:auto; border-radius:6px; background:rgba(99,220,160,.15); color:#8ce6b7; font-size:.95rem; }
        .react-nav-empty { padding:.5rem .75rem; color:#9db3a7; font-size:.78rem; }
        .react-sidebar-footer { padding:.8rem 1rem 1rem; border-top:1px solid rgba(255,255,255,.08); }
        .react-collapse-button,.react-logout { display:flex; align-items:center; width:100%; gap:.65rem; padding:.65rem .7rem; border:0; border-radius:9px; color:#a6bdb0; background:transparent; font:inherit; font-size:.79rem; text-align:left; }
        .react-collapse-button:hover { color:#fff; background:rgba(255,255,255,.06); }
        .react-collapse-button i,.react-logout i { width:18px; text-align:center; }
        .react-logout { margin-top:.3rem; color:#e5a9a3; background:rgba(217,91,78,.08); }
        .react-logout:hover { color:#ffd1cc; background:rgba(217,91,78,.15); }
        .react-topbar { display:flex; justify-content:space-between; gap:1rem; }
        .react-topbar-title,.react-topbar-actions,.react-user { display:flex; align-items:center; }
        .react-topbar-title { gap:.85rem; min-width:0; }
        .react-topbar-title h1 { margin:0; color:#1d3327; font-size:1.04rem; font-weight:750; letter-spacing:-.025em; }
        .react-topbar-title p { margin:.22rem 0 0; color:#7b8a80; font-size:.73rem; font-weight:500; }
        .react-topbar-title p span { margin:0 .18rem; color:#c0cbc3; }
        .react-topbar-actions { gap:.8rem; }
        .react-command-trigger { display:flex; align-items:center; gap:.55rem; min-height:38px; padding:.4rem .65rem; border:1px solid #e5ebe6; border-radius:9px; color:#718078; background:#fff; font:inherit; font-size:.77rem; }
        .react-command-trigger:hover { border-color:#bbd9c8; color:#246f4d; background:#fbfefc; }
        .react-command-trigger kbd { margin-left:.45rem; border-color:#e5ebe6; color:#89968d; background:#f7faf8; }
        .react-topbar-divider { width:1px; height:34px; background:#e8eee9; }
        .react-user { gap:.6rem; }
        .react-user .user-avatar { width:38px; height:38px; flex:0 0 38px; font-size:.9rem; }
        .react-user-copy { display:grid; gap:.15rem; }
        .react-user-copy strong { color:#293d31; font-size:.78rem; font-weight:700; }
        .react-user-copy small { color:#7d8b81; font-size:.69rem; }
        .react-user-chevron { color:#9aa79e; font-size:.65rem; }
        .react-mobile-menu { display:none; width:36px; height:36px; border:1px solid #e1e9e3; border-radius:9px; color:#315441; background:#fff; }
        .react-command-backdrop { position:fixed; inset:0; z-index:1200; display:flex; justify-content:center; align-items:flex-start; padding:14vh 1rem 2rem; background:rgba(10,26,18,.48); backdrop-filter:blur(5px); }
        .react-command-palette { width:min(100%,560px); overflow:hidden; border:1px solid #e0e9e2; border-radius:15px; background:#fff; box-shadow:0 30px 90px rgba(7,30,17,.26); }
        .react-command-input { display:flex; align-items:center; gap:.75rem; padding:1rem 1.05rem; border-bottom:1px solid #edf1ee; color:#7a8b80; }
        .react-command-input input { flex:1; min-width:0; border:0; outline:0; color:#263c2e; font:inherit; font-size:.94rem; }
        .react-command-input kbd { border-color:#e3eae4; color:#7d8b82; background:#f6f9f7; }
        .react-command-caption { padding:.8rem 1rem .45rem; color:#89968d; font-size:.65rem; font-weight:750; letter-spacing:1px; }
        .react-command-results { max-height: min(50vh,420px); overflow:auto; padding:.35rem .55rem .65rem; }
        .react-command-item { display:flex; align-items:center; gap:.7rem; padding:.7rem .65rem; border-radius:9px; color:#34493b; font-size:.84rem; text-decoration:none; }
        .react-command-item:hover,.react-command-item.is-selected { color:#176b49; background:#eff8f2; }
        .react-command-icon { display:grid; place-items:center; width:30px; height:30px; border-radius:8px; color:#31845e; background:#e5f3e9; }
        .react-command-item > i:last-child { color:#9aaba0; font-size:.7rem; }
        .react-command-empty { padding:1.5rem 1rem; color:#77857c; font-size:.84rem; text-align:center; }
        .react-command-footer { display:flex; justify-content:space-between; padding:.7rem 1rem; border-top:1px solid #edf1ee; color:#87938a; font-size:.68rem; }
        .react-command-footer span { display:flex; align-items:center; gap:.28rem; }
        .react-command-footer kbd { border-color:#e4eae5; color:#77877d; background:#f8faf8; }
        body.app-navigating::before { content:''; position:fixed; z-index:1300; top:0; left:0; width:36%; height:3px; background:linear-gradient(90deg,#16805c,#63d6a1); box-shadow:0 1px 8px rgba(22,128,92,.25); animation:app-navigation-progress 1s ease-in-out infinite alternate; pointer-events:none; }
        @keyframes app-navigation-progress { from { transform:scaleX(.45); transform-origin:left; opacity:.75; } to { transform:scaleX(1); transform-origin:left; opacity:1; } }
        @media(max-width:768px){.react-sidebar-header .sidebar-toggle{display:grid;place-items:center}.react-topbar{padding:.85rem 1rem!important}.sidebar-toggle-fixed{display:none!important}.react-mobile-menu{display:grid;place-items:center}.react-topbar-title{gap:.55rem}.react-topbar-title h1{font-size:.95rem}.react-topbar-title p{font-size:.66rem}.react-command-trigger{width:38px;justify-content:center;padding:.4rem}.react-command-trigger span,.react-command-trigger kbd,.react-new-sale,.react-topbar-divider,.react-user-copy,.react-user-chevron{display:none!important}.react-topbar-actions{gap:.55rem}.react-user .user-avatar{width:34px;height:34px;flex-basis:34px}.react-command-footer{display:none}}
        /* Cipta Imaji inspired POS visual system */
        :root {
            --brand-ink: #171718;
            --brand-lime: #d2f801;
            --brand-blue: #4a6de0;
            --brand-surface: #ffffff;
            --brand-canvas: #f4f5f7;
            --brand-line: #e6e9f2;
            --bs-primary: var(--brand-ink);
            --bs-primary-rgb: 23, 23, 24;
            --bs-success: #4a6de0;
            --bs-success-rgb: 74, 109, 224;
            --sidebar-width: 264px;
            --bg-dark: #171718;
            --bg-darker: #121214;
            --bg-light: var(--brand-canvas);
            --text-dark: var(--brand-ink);
            --text-muted: #777985;
            --border-color: var(--brand-line);
            --accent-primary: var(--brand-ink);
            --accent-hover: #303035;
            --shadow-sm: 0 2px 10px rgba(23,23,24,.045);
            --shadow-md: 0 10px 28px rgba(23,23,24,.075);
            --shadow-lg: 0 24px 60px rgba(23,23,24,.14);
        }
        body { background:var(--brand-canvas); color:var(--brand-ink); font-family:'Poppins','Inter',sans-serif; }
        .sidebar { background:linear-gradient(180deg,#1c1c1e 0%,#171718 72%,#121214 100%); box-shadow:8px 0 32px rgba(23,23,24,.08); }
        .react-brand-mark { color:var(--brand-ink); background:var(--brand-lime); box-shadow:0 8px 22px rgba(210,249,1,.16); }
        .react-brand-copy small { color:#b5b5bc; }
        .react-workspace-pill { border-color:rgba(255,255,255,.1); color:#dedee2; background:rgba(255,255,255,.045); }
        .react-online-dot { background:var(--brand-lime); box-shadow:0 0 0 3px rgba(210,249,1,.15); }
        .react-role { color:#ededf0; background:rgba(255,255,255,.1); }
        .react-nav-search { border-color:rgba(255,255,255,.12); color:#aaaab2; background:rgba(0,0,0,.18); }
        .react-nav-search input::placeholder { color:#898991; }
        .react-nav-label { color:#888890; }
        .react-nav .nav-link { color:#b4b4be; }
        .react-nav .nav-link i { color:#9696a2; }
        .react-nav .nav-link:hover { color:#fff; background:rgba(255,255,255,.075); }
        .react-nav .nav-link.active { color:var(--brand-ink); background:var(--brand-lime); box-shadow:none; font-weight:700; }
        .react-nav .nav-link.active i,.react-nav .nav-link.active:hover i { color:var(--brand-ink); }
        .react-nav .nav-link.active::before { background:var(--brand-ink); }
        .react-nav .nav-link.is-restricted { display:grid; grid-template-columns:19px minmax(0,1fr) 15px; align-items:center; column-gap:.7rem; min-height:58px; color:#b4b4be; opacity:.78; cursor:not-allowed; }
        .react-nav .nav-link.is-restricted > i { grid-row:1; grid-column:1; }
        .react-nav .nav-link.is-restricted .restricted-copy { display:grid; gap:.16rem; grid-column:2; }
        .react-nav .nav-link.is-restricted .restricted-copy > span { color:#d0d0d5; font-size:.78rem; }
        .react-nav .nav-link.is-restricted small { color:#a5a5ae; font-size:.62rem; line-height:1.25; }
        .react-nav .nav-link.is-restricted .restricted-lock { grid-column:3; grid-row:1; color:var(--brand-lime); font-size:.75rem; }
        .react-nav .nav-link.is-restricted:hover { color:#b4b4be; background:transparent; }
        .react-nav-plus { color:var(--brand-ink); background:rgba(23,23,24,.1); }
        .react-sidebar-footer { border-color:rgba(255,255,255,.1); }
        .react-collapse-button,.react-logout { color:#b4b4be; }
        .react-logout { color:#ffaaa3; background:rgba(249,31,1,.08); }
        .main-content { background:var(--brand-canvas); }
        .top-navbar { background:rgba(255,255,255,.94); border-bottom-color:var(--brand-line); backdrop-filter:blur(14px); }
        .react-topbar-title h1 { color:var(--brand-ink); }
        .react-topbar-title p { color:#777985; }
        .react-command-trigger { border-color:#e2e4eb; color:#686a74; background:#fff; }
        .react-command-trigger:hover { border-color:#c5d0f6; color:var(--brand-blue); background:#f8f9ff; }
        .react-command-trigger kbd { border-color:#e6e7ed; color:#7b7d87; background:#f4f5f7; }
        .react-topbar-divider { background:#e6e7ed; }
        .react-user-copy strong { color:#252529; }
        .react-user-copy small { color:#777985; }
        .react-user .user-avatar,.user-avatar { color:#fff; background:var(--brand-blue); box-shadow:none; }
        .react-mobile-menu { color:#242428; border-color:#e2e4eb; }
        .container-fluid { max-width:1680px; }
        .card { border:1px solid var(--brand-line); border-radius:16px; background:var(--brand-surface); box-shadow:0 3px 14px rgba(23,23,24,.035); }
        .card-header { color:var(--brand-ink); border-color:#eceef3; background:#fff; font-weight:700; }
        .card-footer { border-color:#eceef3; background:#fafbfc; }
        .btn { border-radius:10px; font-weight:650; }
        .btn-primary { color:#fff; background:var(--brand-ink); border-color:var(--brand-ink); box-shadow:0 4px 12px rgba(23,23,24,.12); }
        .btn-primary:hover,.btn-primary:focus { color:#fff; background:#303035; border-color:#303035; box-shadow:0 7px 18px rgba(23,23,24,.16); }
        .react-new-sale { color:var(--brand-ink); background:var(--brand-lime); border-color:var(--brand-lime); box-shadow:none; }
        .react-new-sale:hover,.react-new-sale:focus { color:var(--brand-ink); background:#c4ec00; border-color:#c4ec00; }
        .btn-success,.btn-info { color:#2948ad; background:#eef1ff; border-color:#e0e6ff; }
        .btn-success:hover,.btn-info:hover { color:#1f3992; background:#e3e9ff; border-color:#d6dfff; }
        .btn-warning { color:#72500b; background:#fff3d8; border-color:#ffe8b3; }
        .btn-danger { background:#d94336; border-color:#d94336; }
        .form-control,.form-select { border-color:#dfe1e8; border-radius:10px; color:#252529; background-color:#fff; }
        .form-control::placeholder { color:#a0a1aa; }
        .form-control:focus,.form-select:focus { border-color:#8298ed; box-shadow:0 0 0 .22rem rgba(74,109,224,.12); }
        .form-label { color:#37383e; font-weight:650; }
        .table { color:#383940; }
        .table thead th { color:#777985; background:#f7f8fa; border-color:#e6e8ee; font-size:.76rem; letter-spacing:.035em; text-transform:uppercase; }
        .table td { border-color:#eef0f4; }
        .table-hover tbody tr:hover { --bs-table-accent-bg:#f8f9fc; }
        .badge.bg-success,.badge.bg-primary { color:#334ea5!important; background:#e9edff!important; }
        .badge.bg-warning { color:#775107!important; background:#fff1d2!important; }
        .badge.bg-info { color:#3153b5!important; background:#e9eeff!important; }
        .badge.bg-danger { color:#a33931!important; background:#ffebe8!important; }
        .badge.bg-secondary { color:#666873!important; background:#eff0f3!important; }
        .alert-success { color:#334ea5; background:#eef1ff; border-left:4px solid var(--brand-blue); }
        .alert-danger { color:#983d35; background:#fff0ed; border-left:4px solid #e15b4f; }
        .modal-content { border-color:#e6e8ee; border-radius:17px; }
        .modal-header,.modal-footer { border-color:#eceef3; }
        .accordion-item { border-color:#e6e8ee; }
        .accordion-button { color:#252529; }
        .accordion-button:not(.collapsed) { color:#344fa9; background:#f0f2ff; }
        .page-link { color:#344fa9; border-color:#e3e6ef; }
        .page-item.active .page-link { color:var(--brand-ink); background:var(--brand-lime); border-color:var(--brand-lime); }
        .sidebar-toggle-fixed { color:var(--brand-lime); background:#171718; }
        .guest-shell { background:radial-gradient(ellipse at 76% 8%,rgba(210,249,1,.18),transparent 35%),radial-gradient(ellipse at 12% 88%,rgba(74,109,224,.1),transparent 34%),#f4f5f7; }
        .guest-card { border-color:#e6e9f2; border-radius:20px; box-shadow:0 24px 70px rgba(23,23,24,.11); }
        .guest-brand { color:var(--brand-ink); background:var(--brand-lime); box-shadow:0 8px 20px rgba(210,249,1,.22); }
        body.app-navigating::before { background:linear-gradient(90deg,var(--brand-blue),var(--brand-lime)); box-shadow:0 1px 10px rgba(74,109,224,.24); }
        .react-command-backdrop { background:rgba(23,23,24,.52); }
        .react-command-palette { border-color:#e6e9f2; }
        .react-command-input { border-color:#eceef3; color:#777985; }
        .react-command-input input { color:#252529; }
        .react-command-caption { color:#858691; }
        .react-command-item { color:#34353b; }
        .react-command-item:hover,.react-command-item.is-selected { color:#344fa9; background:#f0f2ff; }
        .react-command-icon { color:var(--brand-blue); background:#edf0ff; }
        .react-command-footer { border-color:#eceef3; color:#858691; }
        /* Page-level polish for indexes, reports, POS and authentication */
        #app > .main-content .container-fluid > .mb-4:first-child { padding:1.25rem 1.4rem; border:1px solid var(--brand-line); border-radius:16px; background:linear-gradient(110deg,#fff 0%,#fff 68%,#f8f9ff 100%); box-shadow:0 3px 14px rgba(23,23,24,.025); }
        #app > .main-content .container-fluid > .mb-4:first-child h2 { color:var(--brand-ink); font-size:clamp(1.35rem,2vw,1.8rem); letter-spacing:-.045em!important; }
        #app > .main-content .container-fluid > .mb-4:first-child p.text-success { color:var(--brand-blue)!important; letter-spacing:.09em; }
        #app .card-header i { color:var(--brand-blue); }
        #app .card-header .btn i { color:inherit; }
        #app .btn-outline-primary { color:#425dbb; border-color:#cbd4f6; }
        #app .btn-outline-primary:hover,#app .btn-outline-primary.active { color:var(--brand-ink); border-color:var(--brand-lime); background:var(--brand-lime); }
        #app .list-group-item { border-color:#eceef3; }
        #app .table code { color:#455bb0; background:#f0f2ff; padding:.18rem .38rem; border-radius:5px; }
        #app .table .btn-warning { color:#34353b; background:#f1f2f5; border-color:#e6e8ee; }
        #app .table .btn-warning:hover { color:var(--brand-ink); background:var(--brand-lime); border-color:var(--brand-lime); }
        #app .form-check-input:checked { border-color:var(--brand-blue); background-color:var(--brand-blue); }
        #app .form-check-input:focus { border-color:#8298ed; box-shadow:0 0 0 .2rem rgba(74,109,224,.15); }
        #app .card-header[style*="linear-gradient"] { color:var(--brand-ink)!important; background:var(--brand-lime)!important; }
        #app .card-header[style*="linear-gradient"] h6 { color:var(--brand-ink); font-weight:750; }
        .pos-page { --pos-ink:#171718; --pos-muted:#777985; --pos-line:#e6e9f2; --pos-green:#4a6de0; --pos-soft:#f4f5f7; color:var(--brand-ink); }
        .pos-live { color:#555761; }
        .pos-live:before { background:var(--brand-lime); box-shadow:0 0 0 4px rgba(210,249,1,.24); }
        .pos-card,.summary-card { border-color:var(--brand-line); box-shadow:0 5px 22px rgba(23,23,24,.045); }
        .pos-card-head { border-color:#eceef3; }
        .pos-card-head .step { color:#344fa9; background:#edf0ff; }
        .pos-page .form-label { color:#37383e; }
        .pos-page .form-control,.pos-page .form-select,.pay-option span { border-color:#dfe1e8; }
        .pos-page .form-control:focus,.pos-page .form-select:focus { border-color:#8298ed; box-shadow:0 0 0 .22rem rgba(74,109,224,.12); }
        .product-subtotal { color:#34353b; background:#f7f8fa!important; }
        .btn-add { border-color:#b6c2ef; color:#344fa9; background:#f7f8ff; }
        .btn-add:hover { border-color:#a5b4ee; color:#273e9c; background:#edf0ff; }
        .summary-top { color:var(--brand-ink); background:linear-gradient(135deg,#d2f801,#e4ff5b); }
        .summary-top small { color:#34353b; opacity:.75; }
        .summary-line { color:#777985; }
        .summary-line strong,.summary-total strong { color:var(--brand-ink); }
        .change-box { background:#f0f2ff; color:#344fa9; }
        .pay-option input:checked+span { color:#344fa9; border-color:var(--brand-blue); background:#edf0ff; box-shadow:inset 0 0 0 1px var(--brand-blue); }
        .checkout-btn { color:var(--brand-ink); background:var(--brand-lime); }
        .checkout-btn:hover { color:var(--brand-ink); background:#c4ec00; }
        .pos-page .select2-container--default .select2-selection--single { border-color:#dfe1e8; }
        .pos-page .select2-container--default.select2-container--focus .select2-selection--single { border-color:#8298ed; box-shadow:0 0 0 .22rem rgba(74,109,224,.12); }
        .guest-card { border-top:4px solid var(--brand-lime); }
        .guest-card-head h1 { color:var(--brand-ink); letter-spacing:-.04em; }
        .guest-card-head .guest-brand { font-size:1.35rem; }
        .guest-card .input-group-text { color:#777985; border-color:#dfe1e8; }
        .guest-card .form-control { min-height:44px; }
        .guest-card a.text-success { color:var(--brand-blue)!important; }
        .guest-card .btn-primary { color:var(--brand-ink); background:var(--brand-lime); border-color:var(--brand-lime); box-shadow:none; }
        .guest-card .btn-primary:hover { color:var(--brand-ink); background:#c4ec00; border-color:#c4ec00; }
        :where(#app) :where(button,a,input,select,textarea):focus-visible { outline:3px solid rgba(74,109,224,.32); outline-offset:2px; }
        @media(max-width:768px){.react-mobile-menu{color:#252529;background:#fff}.container-fluid{padding:1.2rem 1rem 2rem}}
    </style>
</head>
<body>
    <div id="app">
        @auth
        @php
            $navigationGroups = [
                'Operasional' => [
                    ['label' => 'Dashboard', 'href' => route('dashboard'), 'icon' => 'fas fa-house', 'active' => request()->routeIs('dashboard')],
                    ['label' => 'Transaksi Baru', 'href' => route('transactions.create'), 'icon' => 'fas fa-cash-register', 'active' => request()->routeIs('transactions.create')],
                    ['label' => 'Riwayat Transaksi', 'href' => route('transactions.index'), 'icon' => 'fas fa-receipt', 'active' => request()->routeIs('transactions.index')],
                    ['label' => 'Jadwal Booking', 'href' => route('bookings.index'), 'icon' => 'fas fa-calendar-check', 'active' => request()->routeIs('bookings.*')],
                ],
                'Data Utama' => [
                    ['label' => 'Pelanggan', 'href' => route('customers.index'), 'icon' => 'fas fa-users', 'active' => request()->routeIs('customers.*')],
                    ['label' => 'Kategori Lapangan', 'href' => route('kategori-lapangan.index'), 'icon' => 'fas fa-layer-group', 'active' => request()->routeIs('kategori-lapangan.*'), 'restricted' => !auth()->user()->isAdmin()],
                    ['label' => 'Lapangan', 'href' => route('lapangan.index'), 'icon' => 'fas fa-futbol', 'active' => request()->routeIs('lapangan.*')],
                    ['label' => 'Voucher & Diskon', 'href' => route('discounts.index'), 'icon' => 'fas fa-ticket', 'active' => request()->routeIs('discounts.*')],
                    ['label' => 'Produk & Stok', 'href' => route('products.index'), 'icon' => 'fas fa-box', 'active' => request()->routeIs('products.*')],
                ],
            ];
            if (auth()->user()->isAdmin()) {
                $navigationGroups['Administrasi'] = [
                    ['label' => 'Laporan Omset', 'href' => route('reports.omset'), 'icon' => 'fas fa-chart-line', 'active' => request()->routeIs('reports.omset')],
                    ['label' => 'Laporan Produk', 'href' => route('reports.product'), 'icon' => 'fas fa-chart-column', 'active' => request()->routeIs('reports.product')],
                    ['label' => 'Permintaan Pembatalan', 'href' => route('transactions.cancel-requests'), 'icon' => 'fas fa-ban', 'active' => request()->routeIs('transactions.cancel-requests')],
                ];
            }
        @endphp
        <aside class="sidebar" id="sidebar">
            <div id="react-sidebar-root" data-brand="{{ config('app.name', 'Futsal Booking') }}" data-role="{{ auth()->user()->role->name }}" data-navigation='@json($navigationGroups)' data-logout="{{ route('logout') }}" data-csrf="{{ csrf_token() }}"></div>
        </aside>
        <!-- Toggle button when sidebar is collapsed -->
        <button class="sidebar-toggle-fixed" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        
        <!-- Main Content -->
        <div class="main-content">
            <div id="react-navbar-root" data-title="@yield('title', 'Dashboard')" data-user="{{ auth()->user()->name }}" data-email="{{ auth()->user()->email }}" data-role="{{ auth()->user()->role->name }}" data-initial="{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}" data-new-transaction="{{ route('transactions.create') }}"></div>
            <div id="react-command-root" data-navigation='@json($navigationGroups)'></div>
            <div class="container-fluid">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @yield('content')
            </div>
        </div>
        @else
        <!-- Guest Layout -->     
        
        <main class="guest-shell">
            @yield('content')
        </main>
        @endauth
    </div>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const isCollapsed = sidebar.classList.contains('collapsed');
            const isMobile = window.innerWidth <= 768;
            
            if (isMobile) {
                sidebar.classList.toggle('show');
            } else {
                sidebar.classList.toggle('collapsed');
            }
        }
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.querySelector('.sidebar-toggle');
            const fixedToggle = document.querySelector('.sidebar-toggle-fixed');
            const mobileToggle = document.querySelector('.react-mobile-menu');
            
            if (window.innerWidth <= 768 && sidebar && toggle) {
                if (!sidebar.contains(event.target) && !toggle.contains(event.target) && !fixedToggle?.contains(event.target) && !mobileToggle?.contains(event.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });
        
        // Adjust sidebar toggle position on window resize
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            if (window.innerWidth > 768 && sidebar) {
                sidebar.classList.remove('show');
            }
        });

    </script>
    
    <div id="page-scripts" hidden>@stack('scripts')</div>
</body>
</html>
