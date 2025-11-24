@extends('layouts.admin')

@section('page', 'Edit Trip Schedule')

@section('content')
<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0">Edit Schedule #{{ $schedule->id }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.trip_schedules.update', $schedule->id) }}" method="POST">
            @csrf

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Trip</label>
                    <select name="trip_id" class="form-select" required>
                        <option value="">-- Select Trip --</option>
                        @foreach($trips as $trip)
                            <option value="{{ $trip->id }}" {{ $schedule->trip_id == $trip->id ? 'selected' : '' }}>
                                {{ $trip->operator_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Journey Date</label>
                    <input type="date" name="journey_date" class="form-control" 
                           value="{{ \Carbon\Carbon::parse($schedule->journey_date)->format('Y-m-d') }}" 
                           required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Available Seats</label>
                    <input type="number" name="available_seats" class="form-control" min="1" 
                           value="{{ $schedule->available_seats }}" required>
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('admin.trip_schedules.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Schedule</button>
            </div>
        </form>
    </div>
</div>
@endsection
