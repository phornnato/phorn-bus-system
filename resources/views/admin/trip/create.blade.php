@extends('layouts.admin')

@section('page', 'Add Trip')

@section('content')
<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0">Add New Trip</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.trips.store') }}" method="POST">
            @csrf
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Origin</label>
                    <select name="origin_id" class="form-select" required>
                        <option value="">-- Select Origin --</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}">{{ $loc->city_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Destination</label>
                    <select name="destination_id" class="form-select" required>
                        <option value="">-- Select Destination --</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}">{{ $loc->city_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Operator Name</label>
                    <input type="text" name="operator_name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Bus Type</label>
                    <input type="text" name="bus_type" class="form-control" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Price ($)</label>
                    <input type="number" name="price" class="form-control" step="0.01" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Capacity</label>
                    <input type="number" name="capacity" class="form-control" required>
                </div>
                
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Departure Time</label>
                    <input type="time" name="departure_time" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Arrival Time</label>
                    <input type="time" name="arrival_time" class="form-control" required>
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('admin.trips.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Add Trip</button>
            </div>
        </form>
    </div>
</div>
@endsection
