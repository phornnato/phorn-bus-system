<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Booking Admin Dashboard - Modern Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        :root {
            --sidebar-width: 250px;
            --primary-color: #3B82F6;
            --primary-hover: #2563EB;
            --light-bg: #f4f7f9;
            --sidebar-bg: #fff;
            --text-dark: #1F2937;
            --text-muted-dark: #6B7280;
            --border-light: #E5E7EB;
        }
        body {
            font-family: "Inter", "Segoe UI", Roboto, Arial, sans-serif;
            background-color: var(--light-bg);
            display: flex;
            min-height: 100vh;
            margin: 0;
        }
        .sidebar { width: var(--sidebar-width); background: var(--sidebar-bg); border-right: 1px solid var(--border-light); box-shadow: 1px 0 10px rgba(0,0,0,0.05); flex-shrink: 0; display: flex; flex-direction: column; position: sticky; top: 0; height: 100vh; }
        .sidebar-header { padding: 1.5rem 1rem; border-bottom: 1px solid var(--border-light); text-align: center; }
        .sidebar-header .logo-text { font-size: 1.25rem; font-weight: 700; color: var(--primary-color); }
        .profile-box { padding: 1rem; border-bottom: 1px solid var(--border-light); text-align: center; }
        .profile-box strong { color: var(--text-dark); font-weight: 600; }
        .profile-box small { color: var(--text-muted-dark); font-size: 0.85rem; }
        .profile-img { border: 2px solid var(--primary-color); }
        .sidebar .nav-list { padding-top: 0.5rem; padding-bottom: 0.5rem; }
        .sidebar .nav-link { color: var(--text-dark); padding: 0.75rem 1.5rem; border-radius: .4rem; margin: .1rem .75rem; font-weight: 500; display: flex; align-items: center; transition: all 0.2s; }
        .sidebar .nav-link i { margin-right: 1rem; font-size: 1.1rem; color: var(--text-muted-dark); }
        .sidebar .nav-link.active { background: var(--primary-color); color: #fff !important; }
        .sidebar .nav-link.active i { color: #fff !important; }
        .sidebar .nav-link:hover { background: #EFF6FF; color: var(--primary-hover) !important; }
        .sidebar .nav-link:hover i { color: var(--primary-hover); }
        .sidebar .logout-link { background: #FEE2E2 !important; color: #DC2626 !important; font-weight: 600; margin-top: 0.5rem; }
        .sidebar .logout-link i { color: #DC2626 !important; }
        .sidebar .logout-link:hover { background: #FCA5A5 !important; color: #B91C1C !important; }
        .account-pages-header { padding: 0.5rem 1.5rem 0.25rem 1.5rem; color: var(--text-muted-dark); font-size: 0.85rem; text-transform: uppercase; font-weight: 600; margin-top: 1rem; }
        .content { flex-grow: 1; display: flex; flex-direction: column; }
        .navbar { background: #fff; border-bottom: 1px solid var(--border-light); padding: 1rem 2rem; }
        .main-content { padding: 2rem; flex-grow: 1; }
        #loading-screen { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255, 255, 255, 0.98); display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 9999; visibility: hidden; opacity: 0; transition: opacity 0.4s ease, visibility 0.4s; }
        #loading-screen.show { visibility: visible; opacity: 1; }
        .loading-bus { font-size: 4rem; color: var(--primary-color); animation: bounce 1s infinite alternate; }
        .loading-text { margin-top: 1rem; font-weight: 600; color: var(--primary-color); font-size: 1.1rem; letter-spacing: 1px; }
        @keyframes bounce { from { transform: translateY(0); } to { transform: translateY(-10px); } }
    </style>
</head>
<body>

    {{-- Sidebar --}}
    <div class="sidebar">
        <div class="sidebar-header">
            <span class="logo-text"><i class="bi bi-bus-front me-2"></i>BUS ADMIN</span>
        </div>

        <div class="profile-box">
            {{-- <img src="" class="rounded-circle mb-2 profile-img" width="70" height="70" alt="Admin"> --}}
            @php
                $imagePath = session('image') && file_exists(public_path(session('image')))
                    ? asset(session('image'))
                    : 'https://ui-avatars.com/api/?name='.urlencode(session('username') ?? 'Admin').'&background=0d6efd&color=fff&size=70&bold=true';
            @endphp

            <img src="{{ $imagePath }}" class="rounded-circle mb-2 profile-img" width="70" height="70" alt="{{ session('username') ?? 'Admin' }} "style="object-fit: cover;">

            <div><strong>{{ session('username') ?? 'Long Admin' }}</strong></div>
            <small>{{session('role')}}</small>
        </div>

        <ul class="nav flex-column nav-list">
            <li>
                <a href="{{ url('admin/dashboard') }}" 
                class="nav-link {{ request()->is('admin/dashboard*') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>

            
            <li><a href="{{ url('/admin/location') }}" class="nav-link {{ request()->is('admin/location*') ? 'active' : '' }}"><i class="bi bi-geo-alt-fill"></i> Locations</a></li>
            <li><a href="{{ url('/admin/trips') }}" class="nav-link {{ request()->is('admin/trips*') ? 'active' : '' }}"><i class="bi bi-bus-front"></i> Trips Bus</a></li>
            
            <li><a href="{{ url('/admin/trip-schedules') }}" class="nav-link {{ request()->is('admin/trip-schedules*') ? 'active' : '' }}"><i class="bi bi-calendar-day"></i> Trips Schedule </a></li>
            <li><a href="{{ url('/admin/trip-points') }}" class="nav-link {{ request()->is('admin/trip-points*') ? 'active' : '' }}"><i class="bi bi-compass"></i> Trips Points </a></li>
            
           <li>
                <a href="{{ url('/admin/booking') }}" class="nav-link {{ request()->is('admin/booking*') ? 'active' : '' }}">
                    <i class="bi bi-ticket-perforated"></i> Bookings
                </a>
            </li>
            <li>
                <a href="{{ url('/admin/users_role') }}" class="nav-link {{ request()->is('admin/users_role*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Manage Role
                </a>
            </li>
            <li><a href="{{ url('/admin/users-info') }}" class="nav-link {{ request()->is('admin/users-info*') ? 'active' : '' }}"><i class="bi bi-person-gear"></i> Client Register</a></li>
            
    <a href="#"
       class="nav-link logout-link" 
       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
       <i class="bi bi-box-arrow-right"></i> Logout
    </a>

    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display:none;">
        @csrf
    </form>
</li>

        </ul>
    </div>

    {{-- Content --}}
    <div class="content">
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <span class="navbar-brand-text text-muted fw-normal fs-6">Home / <strong class="text-dark">@yield('page')</strong></span>
                <div class="ms-auto d-flex align-items-center">
                    <form class="d-none d-md-block me-3">
                        <input type="text" class="form-control form-control-sm" placeholder="Search...">
                    </form>
                    <button class="btn btn-outline-secondary btn-sm me-2"><i class="bi bi-bell"></i></button>
                    <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-gear"></i></button>
                </div>
            </div>
        </nav>

        <div class="main-content">
            @yield('content')
        </div>
    </div>

    <div id="loading-screen">
        <i class="bi bi-bus-front-fill loading-bus"></i>
        <div class="loading-text">Loading...</div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('logoutButton').addEventListener('click', function(e) {
            e.preventDefault();
            const loading = document.getElementById('loading-screen');
            loading.classList.add('show');
            setTimeout(() => { window.location.href = this.getAttribute('href'); }, 1000);
        });
    </script>
</body>
</html>
