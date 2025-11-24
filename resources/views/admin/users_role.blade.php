@extends('layouts.admin')

@section('page', 'Manage Users & Roles')

@section('content')

<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center p-3">
        <h4 class="mb-0 fw-semibold"><i class="bi bi-person-gear me-2 text-primary"></i> User & Role Management</h4>

        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-plus-circle me-1"></i> Add New User
        </button>
    </div>

    <div class="card-body p-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-x-octagon me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th class="">Role</th>
                        <th class="">created_at</th>
                        <th class="">updated_at</th>
                        <th width="120" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $u)
                    <tr>
                        <td class="d-flex align-items-center">
                            @php
                                // Check if the user has an image and it exists in public/images/users
                                $image_path = $u->image;
                                $avatar_url = ($image_path && file_exists(public_path($image_path)))
                                    ? asset($image_path) // Use asset() for public folder
                                    : 'https://ui-avatars.com/api/?name=' . urlencode($u->username) . '&background=0d6efd&color=fff&size=50&bold=true';
                            @endphp

                            <img src="{{ $avatar_url }}" 
                                class="rounded-circle me-3 border border-1" 
                                width="50" height="50" 
                                style="object-fit: cover;" 
                                alt="{{ $u->username }}">
                                
                            <div class="fw-semibold">{{ $u->username }}</div>
                        </td>

                        <td>{{ $u->email }}</td>
                        <td class="text-center">
                            @php
                                $role_class = [
                                    'Administrator' => 'bg-danger',
                                    'Staff' => 'bg-success',
                                    'User'  => 'bg-primary'
                                ][$u->role] ?? 'bg-secondary';
                            @endphp
                            <span class="badge {{ $role_class }}">{{ $u->role }}</span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($u->created_at)->timezone('Asia/Phnom_Penh')->format('d M Y H:i:s') }}</td>
                        <td>{{ \Carbon\Carbon::parse($u->updated_at)->timezone('Asia/Phnom_Penh')->format('d M Y H:i:s') }}</td>

                        <td class="text-center">
                            {{-- Modern Dropdown for Actions --}}
                            <div class="dropdown">
                                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $u->id }}">
                                            <i class="bi bi-pencil me-2 text-warning"></i> Edit
                                        </button>
                                    </li>
                                    <li>
                                        <button class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $u->id }}">
                                            <i class="bi bi-trash me-2"></i> Delete
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>

                    {{-- ------------------------------------------------------------------------------------------------------------------------------------- --}}
                    {{-- EDIT USER MODAL (Inside the loop) --}}
                    {{-- ------------------------------------------------------------------------------------------------------------------------------------- --}}
                    <div class="modal fade" id="editUserModal{{ $u->id }}" tabindex="-1" aria-labelledby="editUserModalLabel{{ $u->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered"> 
                            <form method="POST" action="{{ route('users_role.update', $u->id) }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title" id="editUserModalLabel{{ $u->id }}">Edit User: {{ $u->username }}</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-3"> 
                                            <div class="col-12 text-center mt-3">
                                                <div style="width:100px; height:100px; border:2px solid #dee2e6; border-radius:50%; overflow:hidden; display:inline-block;">
                                                    <img id="editPreview{{ $u->id }}" 
                                                        src="{{ $u->image ? asset($u->image) : 'https://ui-avatars.com/api/?name='.urlencode($u->username).'&background=337ab7&color=fff&size=100&bold=true' }}" 
                                                        style="width:100%; height:100%; object-fit:cover;" 
                                                        alt="Profile Image Preview">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="username{{ $u->id }}" class="form-label">Username</label>
                                                <input type="text" id="username{{ $u->id }}" name="username" value="{{ old('username', $u->username) }}" class="form-control" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="email{{ $u->id }}" class="form-label">Email</label>
                                                <input type="email" id="email{{ $u->id }}" name="email" value="{{ old('email', $u->email) }}" class="form-control" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="password{{ $u->id }}" class="form-label">New Password</label>
                                                <input type="password" id="password{{ $u->id }}" name="password" class="form-control" placeholder="Leave blank to keep password">
                                                <small class="text-muted">Min 8 characters</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="role{{ $u->id }}" class="form-label">User Role</label>
                                                <select id="role{{ $u->id }}" name="role" class="form-select" required>
                                                    <option value="Admin" {{ $u->role=='Admin'?'selected':'' }}>Admin</option>
                                                    <option value="Staff" {{ $u->role=='Staff'?'selected':'' }}>Staff</option>
                                                    <option value="User" {{ $u->role=='User'?'selected':'' }}>User</option>
                                                </select>
                                            </div>
                                            
                                            <div class="col-12 mt-3">
                                                <label for="image{{ $u->id }}" class="form-label">Profile Image</label>
                                                <input type="file" id="image{{ $u->id }}" name="image" class="form-control" onchange="previewImage(event, 'editPreview{{ $u->id }}')">
                                            </div>
                                            

                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i> Save Changes</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    {{-- ------------------------------------------------------------------------------------------------------------------------------------- --}}
                    {{-- DELETE USER MODAL (Inside the loop) --}}
                    {{-- ------------------------------------------------------------------------------------------------------------------------------------- --}}
                    <div class="modal fade" id="deleteUserModal{{ $u->id }}" tabindex="-1" aria-labelledby="deleteUserModalLabel{{ $u->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-sm modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title" id="deleteUserModalLabel{{ $u->id }}"><i class="bi bi-exclamation-triangle-fill me-2"></i> Confirm Deletion</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center p-4">
                                    <p class="mb-0">Are you absolutely sure you want to delete **{{ $u->username }}**?</p>
                                    <small class="text-danger">This action cannot be undone.</small>
                                </div>
                                <div class="modal-footer justify-content-center border-0 pt-0">
                                    <form action="{{ route('users_role.destroy', $u->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger"><i class="bi bi-trash-fill me-1"></i> Yes, Delete User</button>
                                    </form>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        {{-- Add Pagination here if using Laravel Paginator --}}
        {{-- <div class="d-flex justify-content-center mt-3">{{ $users->links() }}</div> --}}
    </div>
</div>

{{-- ------------------------------------------------------------------------------------------------------------------------------------- --}}
{{-- ADD USER MODAL (Outside the loop) --}}
{{-- ------------------------------------------------------------------------------------------------------------------------------------- --}}
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('users_role.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="addUserModalLabel"><i class="bi bi-person-plus me-2"></i> Add New User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-12 text-center mt-3">
                            <div style="width:100px; height:100px; border:2px solid #dee2e6; border-radius:50%; overflow:hidden; display:inline-block;">
                                <img id="addPreview" 
                                    src="https://cdn.pixabay.com/photo/2023/02/18/11/00/icon-7797704_640.png" 
                                    style="width:100%; height:100%; object-fit:cover;" 
                                    alt="Profile Image Preview">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="add_username" class="form-label">Username</label>
                            <input type="text" id="add_username" name="username" class="form-control" placeholder="Username" required>
                        </div>
                        <div class="col-md-6">
                            <label for="add_email" class="form-label">Email</label>
                            <input type="email" id="add_email" name="email" class="form-control" placeholder="Email" required>
                        </div>
                        <div class="col-md-6">
                            <label for="add_password" class="form-label">Password</label>
                            <input type="password" id="add_password" name="password" class="form-control" placeholder="Password" required>
                            <small class="text-muted">Required for new user</small>
                        </div>
                        <div class="col-md-6">
                            <label for="add_role" class="form-label">User Role</label>
                            <select id="add_role" name="role" class="form-select" required>
                                <option value="User" selected>User</option>
                                <option value="Staff">Staff</option>
                                <option value="Admin">Admin</option>
                            </select>
                        </div>

                        <div class="col-12 mt-3">
                            <label for="add_image" class="form-label">Profile Image</label>
                            <input type="file" id="add_image" name="image" class="form-control" onchange="previewImage(event, 'addPreview')">
                        </div>
                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-check-circle me-1"></i> Create User</button>
                </div>
            </div>
        </form>
    </div>
</div>



<script>
    // Preview Image (Keep this script)
    function previewImage(event, previewId) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById(previewId);
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    // Auto-dismiss success alert after 10 seconds (Keep this script)
    document.addEventListener('DOMContentLoaded', function() {
        const successAlert = document.querySelector('.alert-success');
        if (successAlert) {
            setTimeout(() => {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(successAlert);
                bsAlert.close();
            }, 10000);
        }
    });
</script>
@endsection