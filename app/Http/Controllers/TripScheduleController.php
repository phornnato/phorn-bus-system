<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\TripSchedule;
use Illuminate\Http\Request;

class TripScheduleController extends Controller
{

    public function index()
    {
        $schedules = TripSchedule::with('trip:id,operator_name')
            ->select('id', 'trip_id', 'journey_date', 'available_seats')
            ->get();

        return view('admin.trip_schedules.index', compact('schedules'));
    }

    public function create()
    {
        $trips = Trip::select('id', 'operator_name')->get();
        return view('admin.trip_schedules.create', compact('trips'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'trip_id'        => 'required|exists:trips,id',
            'journey_date'   => 'required|date',
            'available_seats'=> 'required|integer|min:1',
        ]);

        TripSchedule::create([
            'trip_id'        => $request->trip_id,
            'journey_date'   => $request->journey_date,
            'available_seats'=> $request->available_seats,
        ]);

        return redirect()->route('admin.trip_schedules.index')
            ->with('success', 'Schedule added successfully!');
    }

    public function edit($id)
    {
        $schedule = TripSchedule::findOrFail($id);
        $trips = Trip::select('id', 'operator_name')->get();

        return view('admin.trip_schedules.edit', compact('schedule', 'trips'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'trip_id'        => 'required|exists:trips,id',
            'journey_date'   => 'required|date',
            'available_seats'=> 'required|integer|min:1',
        ]);

        $schedule = TripSchedule::findOrFail($id);

        $schedule->update([
            'trip_id'        => $request->trip_id,
            'journey_date'   => $request->journey_date,
            'available_seats'=> $request->available_seats,
        ]);

        return redirect()->route('admin.trip_schedules.index')
            ->with('success', 'Schedule updated successfully!');
    }

     public function destroy($id)
    {
        $schedule = TripSchedule::findOrFail($id);
        $schedule->delete();

        return redirect()->route('admin.trip_schedules.index')
            ->with('success', 'Schedule deleted successfully!');
    }

    
}
