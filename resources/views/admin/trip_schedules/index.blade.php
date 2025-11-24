@extends('layouts.admin')

@section('page', 'Manage Trip Schedules')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header d-flex justify-content-between align-items-center bg-white py-3 px-4 border-bottom">
        <h5 class="mb-0 fw-bold text-primary">
            <i class="bi bi-calendar-event me-2"></i> Trip Schedules
        </h5>
        <a href="{{ route('admin.trip_schedules.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Add New Schedule
        </a>
    </div>

    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle text-center mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Trip</th>
                        <th>Journey Date</th>
                        <th>Available Seats</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $schedule)
                        <tr>
                            <td class="fw-semibold">{{ $schedule->id }}</td>
                            <td>{{ $schedule->trip->operator_name ?? '—' }}</td>
                            <td>
                                <span class="badge bg-info-subtle text-info px-3 py-2">
                                    {{ \Carbon\Carbon::parse($schedule->journey_date)->format('Y-m-d') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-success-subtle text-success px-3 py-2">
                                    {{ $schedule->available_seats }}
                                </span>
                            </td>

                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a href="{{ route('admin.trip_schedules.edit', $schedule->id) }}" class="dropdown-item">
                                                <i class="bi bi-pencil-square text-warning me-2"></i> Edit
                                            </a>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#deleteScheduleModal{{ $schedule->id }}">
                                                <i class="bi bi-trash me-2"></i> Delete
                                            </button>
                                        </li>

                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-muted py-4">
                                <i class="bi bi-exclamation-circle me-2"></i>No schedules found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Delete Confirmation Modal -->
            <div class="modal fade" id="deleteScheduleModal{{ $schedule->id }}" tabindex="-1" aria-labelledby="deleteScheduleLabel{{ $schedule->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteScheduleLabel{{ $schedule->id }}">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this schedule for <strong>{{ $schedule->trip->operator_name ?? 'Trip' }}</strong> on <strong>{{ $schedule->journey_date }}</strong>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('admin.trip_schedules.delete', $schedule->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                    </form>
                </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>
@endsection
