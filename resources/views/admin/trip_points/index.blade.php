@extends('layouts.admin')

@section('page', 'Manage Trip Points')

<style>
    .map-container iframe {
    width: 100% !important;
    height: 100% !important;
    border: 0;
}

</style>

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header d-flex justify-content-between align-items-center bg-white py-3 px-4 border-bottom">
        <h5 class="mb-0 fw-bold text-primary">
            <i class="bi bi-geo-alt-fill me-2"></i> Trip Points
        </h5>
        <a href="{{ route('admin.trip_points.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Add New Point
        </a>
    </div>

    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle text-center mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Trip</th>
                        <th>Type</th>
                        <th>Time</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Map</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($points as $point)
                        <tr>
                            <td class="fw-semibold">{{ $point->id }}</td>
                            <td>{{ $point->trip->operator_name ?? '—' }}</td>
                            <td>{{ ucfirst($point->type) }}</td>
                            <td>{{ $point->time ? \Carbon\Carbon::parse($point->time)->format('H:i') : '—' }}</td>
                            <td>{{ $point->name }}</td>
                            <td>{{ $point->address }}</td>
                            <td class="text-start">
                                <div class="map-container" style="max-width: 200px; height: 120px; overflow: hidden; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
                                    {!! $point->map !!}
                                </div>
                            </td>

                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a href="{{ route('admin.trip_points.edit', $point->id) }}" class="dropdown-item">
                                                <i class="bi bi-pencil-square text-warning me-2"></i> Edit
                                            </a>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#deletePointModal{{ $point->id }}">
                                                <i class="bi bi-trash me-2"></i> Delete
                                            </button>
                                        </li>
                                    </ul>
                                </div>

                                <!-- Delete Confirmation Modal -->
                                <div class="modal fade" id="deletePointModal{{ $point->id }}" tabindex="-1" aria-labelledby="deletePointLabel{{ $point->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deletePointLabel{{ $point->id }}">Confirm Delete</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete this <strong>{{ ucfirst($point->type) }}</strong> point "<strong>{{ $point->name }}</strong>" for trip <strong>{{ $point->trip->operator_name ?? 'Trip' }}</strong>?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('admin.trip_points.delete', $point->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Yes, Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-muted py-4">
                                <i class="bi bi-exclamation-circle me-2"></i>No trip points found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
