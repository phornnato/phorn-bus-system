@extends('layouts.admin')

@section('page', 'Manage Bookings')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold text-primary">
            <i class="bi bi-ticket-perforated me-2"></i> Bookings Management
        </h5>
    </div>

    <div class="card-body p-4">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card border-0 bg-primary bg-opacity-10">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="bi bi-ticket-perforated fs-4 text-primary"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 text-muted">Total Bookings</h6>
                                <h4 class="mb-0 fw-bold text-primary">{{ $allBookings->count() }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 bg-success bg-opacity-10">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="bi bi-check-circle fs-4 text-success"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 text-muted">Confirmed</h6>
                                <h4 class="mb-0 fw-bold text-success">{{ $allBookings->where('status', 'confirmed')->count() }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 bg-warning bg-opacity-10">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="bi bi-clock fs-4 text-warning"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 text-muted">Pending</h6>
                                <h4 class="mb-0 fw-bold text-warning">{{ $allBookings->where('status', 'pending')->count() }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 bg-danger bg-opacity-10">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="bi bi-x-circle fs-4 text-danger"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 text-muted">Cancelled</h6>
                                <h4 class="mb-0 fw-bold text-danger">{{ $allBookings->where('status', 'cancelled')->count() }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bookings Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Booking ID</th>
                        <th>User</th>
                        <th>Trip</th>
                        <th>Passengers</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allBookings as $booking)
                        <tr>
                            <td class="fw-bold">#{{ $booking->id }}</td>
                            <td>
                                <div>
                                    <div class="fw-semibold">{{ $booking->user->name ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $booking->user->email ?? '' }}</small>
                                </div>
                            </td>
                            <td>
                                @if($booking->schedule && $booking->schedule->trip)
                                    <div>
                                        <div class="fw-semibold">
                                            {{ $booking->schedule->trip->origin->city_name ?? 'N/A' }} 
                                            → 
                                            {{ $booking->schedule->trip->destination->city_name ?? 'N/A' }}
                                        </div>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($booking->schedule->departure_time)->format('M d, Y H:i') }}
                                        </small>
                                    </div>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ $booking->passengers->count() }}
                                </span>
                            </td>
                            <td class="fw-bold text-success">
                                ${{ number_format($booking->total_price, 2) }}
                            </td>
                            <td>
                                @if($booking->status == 'confirmed')
                                    <span class="badge bg-success">Confirmed</span>
                                @elseif($booking->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @else
                                    <span class="badge bg-danger">Cancelled</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($booking->created_at)->format('M d, Y') }}
                                </small>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-sm btn-outline-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#bookingModal{{ $booking->id }}">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @if($booking->status == 'pending')
                                        <form action="{{ route('admin.bookings.confirm', $booking->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-check"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.bookings.cancel', $booking->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                <!-- Modal -->
                                <div class="modal fade" id="bookingModal{{ $booking->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title">Booking #{{ $booking->id }}</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h6>User Information</h6>
                                                        <p><strong>Name:</strong> {{ $booking->user->name ?? 'N/A' }}</p>
                                                        <p><strong>Email:</strong> {{ $booking->user->email ?? 'N/A' }}</p>
                                                        <p><strong>Phone:</strong> {{ $booking->user->phone ?? 'N/A' }}</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6>Trip Information</h6>
                                                        <p><strong>Route:</strong> 
                                                            {{ $booking->schedule->trip->origin->city_name ?? 'N/A' }} 
                                                            → 
                                                            {{ $booking->schedule->trip->destination->city_name ?? 'N/A' }}
                                                        </p>
                                                        <p><strong>Departure:</strong> 
                                                            {{ \Carbon\Carbon::parse($booking->schedule->departure_time)->format('M d, Y H:i') }}
                                                        </p>
                                                        <p><strong>Boarding:</strong> {{ $booking->boardingPoint->name ?? 'N/A' }}</p>
                                                        <p><strong>Dropping:</strong> {{ $booking->droppingPoint->name ?? 'N/A' }}</p>
                                                    </div>
                                                </div>
                                                
                                                <hr>
                                                
                                                <h6>Passengers ({{ $booking->passengers->count() }})</h6>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Name</th>
                                                                <th>Age</th>
                                                                <th>Gender</th>
                                                                <th>Seat</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($booking->passengers as $passenger)
                                                                <tr>
                                                                    <td>{{ $passenger->full_name }}</td>
                                                                    <td>{{ $passenger->age }}</td>
                                                                    <td>{{ ucfirst($passenger->gender) }}</td>
                                                                    <td>{{ $passenger->seat_number ?? '—' }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                
                                                <div class="row mt-3">
                                                    <div class="col-md-6">
                                                        <p><strong>Total Amount:</strong> ${{ number_format($booking->total_price, 2) }}</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p><strong>Status:</strong> 
                                                            <span class="badge 
                                                                @if($booking->status == 'confirmed') bg-success
                                                                @elseif($booking->status == 'pending') bg-warning
                                                                @else bg-danger
                                                                @endif">
                                                                {{ ucfirst($booking->status) }}
                                                            </span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="bi bi-exclamation-circle me-2"></i>No bookings found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($allBookings->count() > 0)
            <div class="mt-3">
                <p class="text-muted">
                    Showing {{ $allBookings->count() }} bookings
                </p>
            </div>
        @endif
    </div>
</div>
@endsection