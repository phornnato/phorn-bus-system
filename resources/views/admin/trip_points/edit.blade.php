@extends('layouts.admin')

@section('page', 'Edit Trip Point')

@section('content')
<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0">Edit Trip Point #{{ $point->id }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.trip_points.update', $point->id) }}" method="POST">
            @csrf

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Trip</label>
                    <select name="trip_id" class="form-select" required>
                        <option value="">-- Select Trip --</option>
                        @foreach($trips as $trip)
                            <option value="{{ $trip->id }}" {{ $point->trip_id == $trip->id ? 'selected' : '' }}>
                                {{ $trip->operator_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select" required>
                        <option value="">-- Select Type --</option>
                        <option value="boarding" {{ $point->type == 'boarding' ? 'selected' : '' }}>Boarding</option>
                        <option value="dropping" {{ $point->type == 'dropping' ? 'selected' : '' }}>Dropping</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Time</label>
                    <input type="time" name="time" class="form-control" 
                           value="{{ $point->time ? \Carbon\Carbon::parse($point->time)->format('H:i') : '' }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $point->name }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control" value="{{ $point->address }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Map Iframe</label>
                    <textarea name="map" class="form-control" rows="3" placeholder="Paste Google Maps iframe here">{{ $point->map }}</textarea>
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('admin.trip_points.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Point</button>
            </div>
        </form>
    </div>
</div>
@endsection
