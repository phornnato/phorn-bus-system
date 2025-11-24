@extends('layouts.admin')

@section('page', 'Dashboard')

@section('content')

{{-- Custom Styles for Modern Card and Hover Effects --}}
<style>
    .dashboard-card {
        border: none;
        border-radius: 12px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .dashboard-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important;
    }

    .metric-icon-container {
        width: 60px;
        height: 60px;
        line-height: 60px;
        text-align: center;
        border-radius: 50%;
        font-size: 1.8rem;
        flex-shrink: 0;
    }

    .bg-primary-subtle { background-color: #e0f2fe !important; }
    .bg-success-subtle { background-color: #d1e7dd !important; }
    .bg-warning-subtle { background-color: #fff3cd !important; }
    .bg-danger-subtle { background-color: #f8d7da !important; }
    .bg-info-subtle { background-color: #d1ecf1 !important; }
    .bg-purple-subtle { background-color: #e2e3f3 !important; }

    .main-content-card {
        border-radius: 12px;
    }
    
    .booking-status-badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
    }
</style>

<div class="row g-4 mb-5">
    {{-- Total Users Card --}}
    <div class="col-md-6 col-lg-3">
        <div class="card dashboard-card shadow-lg p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted text-uppercase mb-1 small">Total Users</h6>
                    <h2 class="fw-bold mb-0">{{ $totalUsers }}</h2>
                </div>
                <div class="metric-icon-container bg-primary-subtle text-primary">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Bookings Card --}}
    <div class="col-md-6 col-lg-3">
        <div class="card dashboard-card shadow-lg p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted text-uppercase mb-1 small">Total Bookings</h6>
                    <h2 class="fw-bold mb-0">{{ $totalBookings }}</h2>
                </div>
                <div class="metric-icon-container bg-success-subtle text-success">
                    <i class="bi bi-ticket-perforated"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Trips Card --}}
    <div class="col-md-6 col-lg-3">
        <div class="card dashboard-card shadow-lg p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted text-uppercase mb-1 small">Active Trips</h6>
                    <h2 class="fw-bold mb-0">{{ $totalTrips }}</h2>
                </div>
                <div class="metric-icon-container bg-warning-subtle text-warning">
                    <i class="bi bi-bus-front"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Revenue Card --}}
    <div class="col-md-6 col-lg-3">
        <div class="card dashboard-card shadow-lg p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted text-uppercase mb-1 small">Total Revenue</h6>
                    <h2 class="fw-bold mb-0 text-success">${{ number_format($totalRevenue, 2) }}</h2>
                </div>
                <div class="metric-icon-container bg-info-subtle text-info">
                    <i class="bi bi-currency-dollar"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Booking Status Overview --}}
<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="card dashboard-card shadow-lg p-3 bg-white h-100 border-start border-4 border-success">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted text-uppercase mb-1 small">Confirmed</h6>
                    <h2 class="fw-bold mb-0 text-success">{{ $confirmedBookings }}</h2>
                </div>
                <div class="metric-icon-container bg-success-subtle text-success">
                    <i class="bi bi-check-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card dashboard-card shadow-lg p-3 bg-white h-100 border-start border-4 border-warning">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted text-uppercase mb-1 small">Pending</h6>
                    <h2 class="fw-bold mb-0 text-warning">{{ $pendingBookings }}</h2>
                </div>
                <div class="metric-icon-container bg-warning-subtle text-warning">
                    <i class="bi bi-clock"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card dashboard-card shadow-lg p-3 bg-white h-100 border-start border-4 border-danger">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted text-uppercase mb-1 small">Cancelled</h6>
                    <h2 class="fw-bold mb-0 text-danger">{{ $cancelledBookings }}</h2>
                </div>
                <div class="metric-icon-container bg-danger-subtle text-danger">
                    <i class="bi bi-x-circle"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Main Content Area: Recent Bookings and Quick Stats --}}
<div class="row g-4">
    {{-- Recent Bookings Table --}}
    <div class="col-lg-8">
        <div class="card main-content-card shadow-lg h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold"><i class="bi bi-clock-history me-2 text-primary"></i> Recent Bookings</h5>
                <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                @if($allBookings->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Booking Ref</th>
                                    <th>User</th>
                                    <th>Route</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th class="pe-4">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allBookings->take(8) as $booking)
                                <tr class="cursor-pointer" onclick="window.location='#'">
                                    <td class="ps-4">
                                        <strong class="text-primary">#{{ $booking->booking_reference }}</strong>
                                    </td>
                                    <td>
                                        @if($booking->user)
                                            <div class="fw-bold">{{ $booking->user->name }}</div>
                                            <small class="text-muted">{{ $booking->user->email }}</small>
                                        @else
                                            <span class="text-muted">Guest</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($booking->schedule && $booking->schedule->trip)
                                            <small>
                                                {{ $booking->schedule->trip->origin->city_name ?? 'N/A' }} 
                                                <i class="bi bi-arrow-right mx-1 text-muted"></i>
                                                {{ $booking->schedule->trip->destination->city_name ?? 'N/A' }}
                                            </small>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        <strong class="text-success">${{ number_format($booking->total_price, 2) }}</strong>
                                    </td>
                                    <td>
                                        <span class="booking-status-badge badge 
                                            @if($booking->status == 'confirmed') bg-success
                                            @elseif($booking->status == 'pending') bg-warning
                                            @elseif($booking->status == 'cancelled') bg-danger
                                            @else bg-secondary @endif">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    <td class="pe-4">
                                        <small>{{ $booking->created_at->format('M d') }}</small>
                                        <br>
                                        <small class="text-muted">{{ $booking->created_at->format('h:i A') }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-ticket-perforated text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3 mb-0">No bookings found</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Quick Stats & Actions --}}
    <div class="col-lg-4">
        {{-- Quick Actions Card --}}
        <div class="card main-content-card shadow-lg mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="mb-0 fw-semibold"><i class="bi bi-lightning me-2 text-warning"></i> Quick Actions</h5>
            </div>
            <div class="card-body p-3">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.trips.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle me-1"></i> Add New Trip
                    </a>
                    <a href="{{ route('admin.trip_schedules.create') }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-calendar-plus me-1"></i> Create Schedule
                    </a>
                    <a href="/admin/users_role" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-people me-1"></i> Manage Users
                    </a>
                </div>
            </div>
        </div>

        {{-- System Alerts Card --}}
        <div class="card main-content-card shadow-lg">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="mb-0 fw-semibold"><i class="bi bi-bell me-2 text-danger"></i> System Alerts</h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @if($pendingBookings > 0)
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <div>
                            <i class="bi bi-clock text-warning me-2"></i>
                            <span class="small">{{ $pendingBookings }} pending bookings</span>
                        </div>
                        <span class="badge bg-warning text-dark">Action Required</span>
                    </li>
                    @endif
                    
                    @if($cancelledBookings > 0)
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <div>
                            <i class="bi bi-x-circle text-danger me-2"></i>
                            <span class="small">{{ $cancelledBookings }} cancelled bookings</span>
                        </div>
                        <span class="badge bg-danger">Review</span>
                    </li>
                    @endif

                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <div>
                            <i class="bi bi-currency-dollar text-success me-2"></i>
                            <span class="small">Revenue: ${{ number_format($totalRevenue, 2) }}</span>
                        </div>
                        <span class="badge bg-success">Good</span>
                    </li>

                    @if($allBookings->count() == 0)
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <div>
                            <i class="bi bi-info-circle text-info me-2"></i>
                            <span class="small">No bookings in system</span>
                        </div>
                        <span class="badge bg-info">Info</span>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection