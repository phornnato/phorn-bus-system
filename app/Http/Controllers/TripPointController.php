<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\TripPoint;
use Illuminate\Http\Request;

class TripPointController extends Controller
{
    // List all trip points
    public function index()
    {
        $points = TripPoint::with('trip:id,operator_name')
            ->select('id', 'trip_id', 'type', 'time', 'name', 'address', 'map')
            ->get();

        return view('admin.trip_points.index', compact('points'));
    }

    // Show create form
    public function create()
    {
        $trips = Trip::select('id', 'operator_name')->get();
        return view('admin.trip_points.create', compact('trips'));
    }

    // Store new trip point
    public function store(Request $request)
    {
        $request->validate([
            'trip_id' => 'required|exists:trips,id',
            'type'    => 'required|in:boarding,dropping',
            'time'    => 'required|date_format:H:i',
            'name'    => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'map'     => 'nullable|string',
        ]);

        TripPoint::create([
            'trip_id' => $request->trip_id,
            'type'    => $request->type,
            'time'    => $request->time,
            'name'    => $request->name,
            'address' => $request->address,
            'map'     => $request->map,
        ]);

        return redirect()->route('admin.trip_points.index')
            ->with('success', 'Trip point added successfully!');
    }

    // Show edit form
    public function edit($id)
    {
        $point = TripPoint::findOrFail($id);
        $trips = Trip::select('id', 'operator_name')->get();

        return view('admin.trip_points.edit', compact('point', 'trips'));
    }

    // Update trip point
    public function update(Request $request, $id)
    {
        $request->validate([
            'trip_id' => 'required|exists:trips,id',
            'type'    => 'required|in:boarding,dropping',
            'time'    => 'required|date_format:H:i',
            'name'    => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'map'     => 'nullable|string',
        ]);

        $point = TripPoint::findOrFail($id);

        $point->update([
            'trip_id' => $request->trip_id,
            'type'    => $request->type,
            'time'    => $request->time,
            'name'    => $request->name,
            'address' => $request->address,
            'map'     => $request->map,
        ]);

        return redirect()->route('admin.trip_points.index')
            ->with('success', 'Trip point updated successfully!');
    }

    // Delete trip point
    public function destroy($id)
    {
        $point = TripPoint::findOrFail($id);
        $point->delete();

        return redirect()->route('admin.trip_points.index')
            ->with('success', 'Trip point deleted successfully!');
    }
}
