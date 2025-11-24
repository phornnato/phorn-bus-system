@extends('layouts.admin')

@section('page', 'Manage Users')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header d-flex justify-content-between align-items-center bg-white py-3 px-4 border-bottom">
        <h5 class="mb-0 fw-bold text-primary">
            <i class="bi bi-people-fill me-2"></i> Users
        </h5>
      
    </div>

    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle text-center mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="fw-semibold">{{ $user->id }}</td>
                            <td class="text-start">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone ?? '—' }}</td>
                            <td>
                                <span class="badge bg-info-subtle text-info px-3 py-2">
                                    {{ \Carbon\Carbon::parse($user->created_at)->format('Y-m-d H:i') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-muted py-4">
                                <i class="bi bi-exclamation-circle me-2"></i>No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
