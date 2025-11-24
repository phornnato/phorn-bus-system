@extends('layouts.admin')

@section('page', 'Edit Trip')

@section('content')
<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0">Edit Trip #{{ $trip->id }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.trips.update', $trip->id) }}" method="POST">
            @csrf

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Origin</label>
                    <select name="origin_id" class="form-select" required>
                        <option value="">-- Select Origin --</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ $trip->origin_id == $loc->id ? 'selected' : '' }}>
                                {{ $loc->city_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Destination</label>
                    <select name="destination_id" class="form-select" required>
                        <option value="">-- Select Destination --</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ $trip->destination_id == $loc->id ? 'selected' : '' }}>
                                {{ $loc->city_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Operator Name</label>
                    <input type="text" name="operator_name" value="{{ $trip->operator_name }}" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Bus Type</label>
                    <input type="text" name="bus_type" value="{{ $trip->bus_type }}" class="form-control" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Price ($)</label>
                    <input type="number" name="price" value="{{ $trip->price }}" class="form-control" required step="0.01">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Capacity</label>
                    <input type="number" name="capacity" value="{{ $trip->capacity }}" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Bus Type</label>
                    <input type="text" name="bus_type" value="{{ $trip->bus_type }}" class="form-control" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Departure Time</label>
                   <input type="time" name="departure_time"
                        value="{{ \Carbon\Carbon::parse($trip->departure_time)->format('H:i') }}"
                        class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Arrival Time</label>
                  <input type="time" name="arrival_time"
       value="{{ \Carbon\Carbon::parse($trip->arrival_time)->format('H:i') }}"
       class="form-control" required>
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('admin.trips.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Trip</button>
            </div>
        </form>
    </div>
</div>
@endsection
