<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Bus Booking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .dashboard-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        .stat-card {
            border-radius: 15px;
            color: white;
            text-align: center;
            padding: 20px;
        }
        .booking-card {
            border-left: 4px solid #007bff;
        }
        .badge-confirmed { background-color: #28a745; }
        .badge-pending { background-color: #ffc107; color: #000; }
        .badge-cancelled { background-color: #dc3545; }
        .user-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(45deg, #007bff, #6610f2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            margin: 0 auto;
        }
    </style>
</head>
<!-- Toast container -->
<div aria-live="polite" aria-atomic="true" class="position-relative">
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1080">
        @if(request()->has('msg'))
            <div class="toast show align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ request()->get('msg') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        @endif
    </div>
</div>
<body class="bg-light">
    <div class="container py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 text-primary mb-1">
                            <i class="fas fa-user-circle me-2"></i>Welcome Back! {{ $user->name }}
                        </h1>
                        <p class="text-muted mb-0">Manage your bookings and profile</p>
                    </div>
                    <div>
                        <a href="{{ route('home') }}" class="btn btn-outline-primary me-2">
                            <i class="fas fa-home me-1"></i> Home
                        </a>
                        <a href="{{ route('user.logout') }}" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt me-1"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Profile Section -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-2 text-center">
                                <div class="user-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong><i class="fas fa-user me-2 text-primary"></i>Full Name</strong>
                                        <p class="mb-0">{{ $user->name }}</p>
                                    </div>
                                    <div class="col-md-3">
                                        <strong><i class="fas fa-envelope me-2 text-primary"></i>Email</strong>
                                        <p class="mb-0">{{ $user->email }}</p>
                                    </div>
                                    <div class="col-md-3">
                                        <strong><i class="fas fa-phone me-2 text-primary"></i>Phone</strong>
                                        <p class="mb-0">{{ $user->phone ?? 'Not provided' }}</p>
                                    </div>
                                    <div class="col-md-3">
                                        <strong><i class="fas fa-calendar me-2 text-primary"></i>Member Since</strong>
                                        <p class="mb-0">{{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card bg-primary">
                    <i class="fas fa-ticket-alt fa-2x mb-2"></i>
                    <h2>{{ $totalBookings }}</h2>
                    <p class="mb-0">Total Bookings</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-success">
                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                    <h2>{{ $confirmedBookings }}</h2>
                    <p class="mb-0">Confirmed</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-warning">
                    <i class="fas fa-clock fa-2x mb-2"></i>
                    <h2>{{ $pendingBookings }}</h2>
                    <p class="mb-0">Pending</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-danger">
                    <i class="fas fa-times-circle fa-2x mb-2"></i>
                    <h2>{{ $cancelledBookings }}</h2>
                    <p class="mb-0">Cancelled</p>
                </div>
            </div>
        </div>

        <!-- Booking History -->
        <div class="card dashboard-card">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 text-primary">
                        <i class="fas fa-history me-2"></i>Booking History
                    </h5>
                    <span class="badge bg-primary">{{ $totalBookings }} Bookings</span>
                </div>
            </div>
            <div class="card-body">
                @if($bookings->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Booking Ref</th>
                                    <th>Route</th>
                                    <th>Departure Time</th>
                                    <th>Seats</th>
                                    <th>Boarding Point</th>
                                    <th>Total Price</th>
                                    <th>Status</th>
                                    <th>Booking Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                <tr class="booking-card">
                                    <td>
                                        <strong class="text-primary">{{ $booking->booking_reference }}</strong>
                                    </td>
                                    <td>
                                        @if($booking->schedule && $booking->schedule->trip)
                                            <div class="fw-bold">
                                                {{ $booking->schedule->trip->origin->city_name ?? 'N/A' }} 
                                                <i class="fas fa-arrow-right mx-2 text-muted"></i>
                                                {{ $booking->schedule->trip->destination->city_name ?? 'N/A' }}
                                            </div>
                                            <small class="text-muted">
                                                {{ $booking->schedule->trip->operator_name }}
                                            </small>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if($booking->schedule && $booking->schedule->trip)
                                            <i class="fas fa-clock text-muted me-1"></i>
                                            {{ date('h:i A', strtotime($booking->schedule->trip->departure_time)) }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @foreach($booking->passengers as $passenger)
                                            <span class="badge bg-secondary me-1">{{ $passenger->seat_number }}</span>
                                        @endforeach
                                    </td>
                                    <td>{{ $booking->boardingPoint->name ?? 'N/A' }}</td>
                                    <td>
                                        <strong class="text-success">${{ number_format($booking->total_price, 2) }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge 
                                            @if($booking->status == 'confirmed') badge-confirmed
                                            @elseif($booking->status == 'pending') badge-pending
                                            @elseif($booking->status == 'cancelled') badge-cancelled
                                            @else bg-secondary @endif">
                                            <i class="fas 
                                                @if($booking->status == 'confirmed') fa-check
                                                @elseif($booking->status == 'pending') fa-clock
                                                @elseif($booking->status == 'cancelled') fa-times
                                                @else fa-question @endif me-1">
                                            </i>
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <small>{{ $booking->created_at->format('M d, Y') }}</small>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#bookingModal{{ $booking->id }}">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-ticket-alt fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No Bookings Found</h4>
                        <p class="text-muted mb-4">You haven't made any bookings yet. Start your journey with us!</p>
                        <a href="{{ route('home') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-bus me-2"></i>Book Your First Trip
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Booking Details Modals -->
    @foreach($bookings as $booking)
    <div class="modal fade" id="bookingModal{{ $booking->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-ticket-alt me-2"></i>
                        Booking Details - {{ $booking->booking_reference }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Trip Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2">
                                <i class="fas fa-route me-2 text-primary"></i>Trip Information
                            </h6>
                            <p><strong>Booking Reference:</strong> {{ $booking->booking_reference }}</p>
                            <p><strong>Route:</strong> 
                                @if($booking->schedule && $booking->schedule->trip)
                                    {{ $booking->schedule->trip->origin->city_name ?? 'N/A' }} 
                                    <i class="fas fa-arrow-right mx-2 text-muted"></i>
                                    {{ $booking->schedule->trip->destination->city_name ?? 'N/A' }}
                                @else
                                    N/A
                                @endif
                            </p>
                            <p><strong>Operator:</strong> {{ $booking->schedule->trip->operator_name ?? 'N/A' }}</p>
                            <p><strong>Bus Type:</strong> {{ $booking->schedule->trip->bus_type ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2">
                                <i class="fas fa-clock me-2 text-primary"></i>Schedule
                            </h6>
                            <p><strong>Departure Time:</strong> 
                                @if($booking->schedule && $booking->schedule->trip)
                                    {{ date('h:i A', strtotime($booking->schedule->trip->departure_time)) }}
                                @else
                                    N/A
                                @endif
                            </p>
                            <p><strong>Arrival Time:</strong> 
                                @if($booking->schedule && $booking->schedule->trip)
                                    {{ date('h:i A', strtotime($booking->schedule->trip->arrival_time)) }}
                                @else
                                    N/A
                                @endif
                            </p>
                            <p><strong>Boarding Point:</strong> {{ $booking->boardingPoint->name ?? 'N/A' }}</p>
                            <p><strong>Dropping Point:</strong> {{ $booking->droppingPoint->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    
                    <!-- Passenger Details -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2">
                                <i class="fas fa-users me-2 text-primary"></i>Passenger Details
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Name</th>
                                            <th>Seat</th>
                                            <th>Age</th>
                                            <th>Gender</th>
                                            <th>Nationality</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($booking->passengers as $passenger)
                                        <tr>
                                            <td>{{ $passenger->full_name }}</td>
                                            <td><span class="badge bg-primary">{{ $passenger->seat_number }}</span></td>
                                            <td>{{ $passenger->age }}</td>
                                            <td>{{ $passenger->gender }}</td>
                                            <td>{{ $passenger->nationality }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2">
                                <i class="fas fa-credit-card me-2 text-primary"></i>Payment Information
                            </h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <p><strong>Total Amount:</strong> 
                                        <span class="text-success fw-bold">${{ number_format($booking->total_price, 2) }}</span>
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Payment Method:</strong> {{ ucfirst($booking->payment_method) }}</p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Status:</strong> 
                                        <span class="badge 
                                            @if($booking->status == 'confirmed') badge-confirmed
                                            @elseif($booking->status == 'pending') badge-pending
                                            @elseif($booking->status == 'cancelled') badge-cancelled
                                            @else bg-secondary @endif">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <p class="text-muted mb-0">
                                <small>
                                    <i class="fas fa-calendar me-1"></i>
                                    Booked on: {{ $booking->created_at->format('F d, Y \a\t h:i A') }}
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Close
                    </button>
                   
            </div>
        </div>
    </div>
    @endforeach

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simple JavaScript for any interactive features
        document.addEventListener('DOMContentLoaded', function() {
            // Add any custom JavaScript here if needed
        });
    </script>
</body>
</html>