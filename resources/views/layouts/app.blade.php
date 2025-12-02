{{-- resources/views/layouts/app.blade.php --}}
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'Futsal Booking') }}</title>
    
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Inter:400,500,600,700" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
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
    </style>
</head>
<body>
    <div id="app">
        @auth
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-header-content">
                    <div class="sidebar-logo">
                        <div class="sidebar-logo-icon">
                            <i class="fas fa-futbol"></i>
                        </div>
                        <h4>Futsal Booking</h4>
                    </div>
                    <small>{{ auth()->user()->role->name }}</small>
                </div>
                <button class="sidebar-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav flex-column">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                    
                    <a href="{{ route('transactions.create') }}" class="nav-link {{ request()->routeIs('transactions.create') ? 'active' : '' }}">
                        <i class="fas fa-cash-register"></i>
                        <span>Transaksi Baru</span>
                    </a>
                    
                    <a href="{{ route('transactions.index') }}" class="nav-link {{ request()->routeIs('transactions.index') ? 'active' : '' }}">
                        <i class="fas fa-receipt"></i>
                        <span>Riwayat Transaksi</span>
                    </a>
                    
                    <a href="{{ route('bookings.index') }}" class="nav-link {{ request()->routeIs('bookings.*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-check"></i>
                        <span>Jadwal Booking</span>
                    </a>
                    
                    <div class="sidebar-divider"></div>
                    <div class="sidebar-title">Master Data</div>
                    
                    <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        <span>Customer</span>
                    </a>
                    
                    <a href="{{ route('kategori-lapangan.index') }}" class="nav-link {{ request()->routeIs('kategori-lapangan.*') ? 'active' : '' }}">
                        <i class="fas fa-th-large"></i>
                        <span> Kategori Lapangan</span>
                    </a>
                    <a href="{{ route('lapangan.index') }}" class="nav-link {{ request()->routeIs('lapangan.*') ? 'active' : '' }}">
                        <i class="fas fa-th-large"></i>
                        <span>Lapangan</span>
                    </a>

                    <a href="{{ route('discounts.index') }}" class="nav-link {{ request()->routeIs('discounts.*') ? 'active' : '' }}">
                        <i class="fas fa-percent"></i>
                        <span>Diskon</span>
                    </a>
                    
                    <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                        <i class="fas fa-box"></i>
                        <span>Produk</span>
                    </a>
                    
                    @if(auth()->user()->isAdmin())
                    <div class="sidebar-divider"></div>
                    <div class="sidebar-title">Admin Menu</div>
                    
                    <a href="{{ route('reports.omset') }}" class="nav-link {{ request()->routeIs('reports.omset') ? 'active' : '' }}">
                        <i class="fas fa-chart-line"></i>
                        <span>Laporan Omset</span>
                    </a>
                    
                    <a href="{{ route('reports.product') }}" class="nav-link {{ request()->routeIs('reports.product') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar"></i>
                        <span>Laporan Produk</span>
                    </a>
                    <a href="{{ route('transactions.cancel-requests') }}" class="nav-link {{ request()->routeIs('reports.product') ? 'active' : '' }}">
                        <i class="fas fa-check"></i>
                        <span>Perizian Pembatalan</span>
                    </a>
                    @endif
                </div>
            </nav>
            
            <div class="sidebar-footer">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-logout">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Toggle button when sidebar is collapsed -->
        <button class="sidebar-toggle-fixed" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="top-navbar d-flex justify-content-between align-items-center">
                <h5>@yield('title', 'Dashboard')</h5>
                <div class="user-info">
                    <div class="user-info-text d-none d-md-block">
                        <div class="fw-bold">{{ auth()->user()->name }}</div>
                        <small class="text-muted">{{ auth()->user()->email }}</small>
                    </div>
                    <div class="user-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="badge bg-primary">{{ auth()->user()->role->name }}</span>
                </div>
            </div>
            
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
        
        <main class="py-4">
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
            
            if (window.innerWidth <= 768 && sidebar && toggle) {
                if (!sidebar.contains(event.target) && !toggle.contains(event.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });
        
        // Adjust sidebar toggle position on window resize
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            if (window.innerWidth > 768) {
                sidebar.classList.remove('show');
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>