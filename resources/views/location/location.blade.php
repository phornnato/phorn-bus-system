@extends('layouts.admin')

@section('page', 'Manage Locations')

@section('content')

<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center p-3">
        <h4 class="mb-0 fw-semibold"><i class="bi bi-geo-alt-fill me-2 text-primary"></i> Locations</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLocationModal">
            <i class="bi bi-plus-circle me-1"></i> Add New Location
        </button>
    </div>

    <div class="card-body p-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>City Name</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th width="120" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $loc)
                    <tr>
                        <td>{{ $loc->id }}</td>
                        <td>{{ $loc->city_name }}</td>
                        <td>{{ $loc->created_at }}</td>
                        <td>{{ $loc->updated_at }}</td>
                        <td class="text-center">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editLocationModal{{ $loc->id }}">
                                            <i class="bi bi-pencil me-2 text-warning"></i> Edit
                                        </button>
                                    </li>
                                    <li>
                                        <button class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#deleteLocationModal{{ $loc->id }}">
                                            <i class="bi bi-trash me-2"></i> Delete
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>

                    {{-- Edit Location Modal --}}
                    <div class="modal fade" id="editLocationModal{{ $loc->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <form method="POST" action="{{ route('location.update', $loc->id) }}">
                                @csrf
                                @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title">Edit Location</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <label class="form-label">City Name</label>
                                        <input type="text" name="city_name" class="form-control" value="{{ old('city_name', $loc->city_name) }}" required>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i> Save Changes</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Delete Location Modal --}}
                    <div class="modal fade" id="deleteLocationModal{{ $loc->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-sm modal-dialog-centered">
                            <form action="{{ route('location.destroy', $loc->id) }}" method="POST" class="modal-content">
                                @csrf
                                @method('DELETE')
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i> Confirm Delete</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <p>Are you sure you want to delete <b>{{ $loc->city_name }}</b>?</p>
                                </div>
                                <div class="modal-footer justify-content-center">
                                    <button type="submit" class="btn btn-danger">Yes, Delete</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Add Location Modal --}}
<div class="modal fade" id="addLocationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('location.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i> Add New Location</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">City Name</label>
                    <input type="text" name="city_name" class="form-control" placeholder="Enter city name" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-check-circle me-1"></i> Create Location</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
