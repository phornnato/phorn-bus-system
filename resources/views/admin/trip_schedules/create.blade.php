@extends('layouts.admin')

@section('page', 'Add Trip Schedule')

@section('content')
<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0">Add New Trip Schedule</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.trip_schedules.store') }}" method="POST">
            @csrf

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Trip</label>
                    <select name="trip_id" class="form-select" required>
                        <option value="">-- Select Trip --</option>
                        @foreach($trips as $trip)
                            <option value="{{ $trip->id }}">{{ $trip->operator_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Journey Date</label>
                    <input type="date" name="journey_date" class="form-control" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Available Seats</label>
                    <input type="number" name="available_seats" class="form-control" min="1" required>
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('admin.trip_schedules.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Add Schedule</button>
            </div>
        </form>
    </div>
</div>
@endsection
