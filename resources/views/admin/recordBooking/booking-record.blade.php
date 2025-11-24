@extends('layouts.admin')

@section('page', 'Booking Record')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-ticket-detailed me-2"></i>
                        Booking Record: {{ $booking->booking_reference }}
                    </h5>
                    <a href="{{ route('admin.bookings.all') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Back to All Bookings
                    </a>
                </div>
                <div class="card-body">
                    <!-- Booking Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2 text-primary">
                                <i class="bi bi-info-circle me-2"></i>Booking Information
                            </h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%"><strong>Booking Reference:</strong></td>
                                    <td><code>#{{ $booking->booking_reference }}</code></td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge 
                                            @if($booking->status == 'confirmed') bg-success
                                            @elseif($booking->status == 'pending') bg-warning
                                            @elseif($booking->status == 'cancelled') bg-danger
                                            @else bg-secondary @endif">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Booking Date:</strong></td>
                                    <td>{{ $booking->created_at->format('F d, Y \a\t h:i A') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Amount:</strong></td>
                                    <td class="text-success fw-bold">${{ number_format($booking->total_price, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Payment Method:</strong></td>
                                    <td>{{ ucfirst($booking->payment_method) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2 text-primary">
                                <i class="bi bi-person me-2"></i>User Information
                            </h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%"><strong>User:</strong></td>
                                    <td>
                                        @if($booking->user)
                                            {{ $booking->user->name }}
                                            <br>
                                            <small class="text-muted">{{ $booking->user->email }}</small>
                                            @if($booking->user->phone)
                                                <br>
                                                <small class="text-muted">{{ $booking->user->phone }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">Guest User</span>
                                            <br>
                                            <small class="text-muted">{{ $booking->contact_email }}</small>
                                            <br>
                                            <small class="text-muted">{{ $booking->contact_phone }}</small>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Trip Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 text-primary">
                                <i class="bi bi-geo-alt me-2"></i>Trip Information
                            </h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Route:</strong><br>
                                    @if($booking->schedule && $booking->schedule->trip)
                                        {{ $booking->schedule->trip->origin->city_name ?? 'N/A' }} 
                                        <i class="bi bi-arrow-right mx-2 text-muted"></i>
                                        {{ $booking->schedule->trip->destination->city_name ?? 'N/A' }}
                                    @else
                                        N/A
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    <strong>Operator:</strong><br>
                                    {{ $booking->schedule->trip->operator_name ?? 'N/A' }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Bus Type:</strong><br>
                                    {{ $booking->schedule->trip->bus_type ?? 'N/A' }}
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <strong>Departure Time:</strong><br>
                                    @if($booking->schedule && $booking->schedule->trip)
                                        {{ date('M d, Y h:i A', strtotime($booking->schedule->trip->departure_time)) }}
                                    @else
                                        N/A
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    <strong>Boarding Point:</strong><br>
                                    {{ $booking->boardingPoint->name ?? 'N/A' }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Dropping Point:</strong><br>
                                    {{ $booking->droppingPoint->name ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Passenger Details -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 text-primary">
                                <i class="bi bi-people me-2"></i>Passenger Details ({{ $booking->passengers->count() }})
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Full Name</th>
                                            <th>Seat Number</th>
                                            <th>Age</th>
                                            <th>Gender</th>
                                            <th>Nationality</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($booking->passengers as $index => $passenger)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $passenger->full_name }}</td>
                                            <td>
                                                <span class="badge bg-primary">{{ $passenger->seat_number }}</span>
                                            </td>
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

                    <!-- Admin Actions -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                @if($booking->status != 'cancelled')
                                <button class="btn btn-danger" onclick="updateStatus('cancelled')">
                                    <i class="bi bi-x-circle me-1"></i> Cancel Booking
                                </button>
                                @endif
                                
                                @if($booking->status == 'pending')
                                <button class="btn btn-success" onclick="updateStatus('confirmed')">
                                    <i class="bi bi-check-circle me-1"></i> Confirm Booking
                                </button>
                                @endif
                                
                                <button class="btn btn-primary">
                                    <i class="bi bi-printer me-1"></i> Print Ticket
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateStatus(status) {
    if (!confirm('Are you sure you want to change booking status to ' + status + '?')) {
        return;
    }
    
    fetch('{{ route("admin.booking.status", $booking->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Booking status updated successfully!');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating status.');
    });
}
</script>
@endsection