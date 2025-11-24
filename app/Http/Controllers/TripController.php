<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Location;
use App\Models\TripPoint;
use Illuminate\Http\Request;

class TripController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $trips = Trip::select('id', 'origin_id', 'destination_id', 'operator_name','bus_type','price','capacity','departure_time','arrival_time')
        ->with([
            'origin:id,city_name',        
            'destination:id,city_name'   
        ])
        ->get();

        return view('admin.trip.index', compact('trips'));
    }

    public function create()
    {
        $locations = Location::select('id', 'city_name')->get();
        return view('admin.trip.create', compact('locations'));
    }
     public function showBooking($id)
    {
        $trip = Trip::with(['origin', 'destination'])->findOrFail($id);
        
        // Get boarding and dropping points for this trip
        $boardingPoints = TripPoint::where('trip_id', $id)
            ->where('type', 'boarding')
            ->orderBy('time')
            ->get();
            
        $droppingPoints = TripPoint::where('trip_id', $id)
            ->where('type', 'dropping')
            ->orderBy('time')
            ->get();

        // Get already booked seats
        $bookedSeats = \App\Models\BookingSeat::where('schedule_id', $id)
            ->pluck('seat_number')
            ->toArray();

        return view('booking.index', compact(
            'trip', 
            'boardingPoints', 
            'droppingPoints', 
            'bookedSeats'
        ));
    }

public function store(Request $request)
{
    $request->validate([
        'origin_id'       => 'required|integer',
        'destination_id'  => 'required|integer|different:origin_id',
        'operator_name'   => 'required|string|max:255',
        'bus_type'        => 'required|string|max:255',
        'price'           => 'required|numeric|min:0',
        'capacity'        => 'required|integer|min:1',
        'departure_time'  => 'required|date_format:H:i',
        'arrival_time'    => 'required|date_format:H:i|after:departure_time',
    ]);

    Trip::create([
        'origin_id'      => $request->origin_id,
        'destination_id' => $request->destination_id,
        'operator_name'  => $request->operator_name,
        'bus_type'       => $request->bus_type,
        'price'          => $request->price,
        'capacity'       => $request->capacity,
        'departure_time' => $request->departure_time,
        'arrival_time'   => $request->arrival_time,
    ]);

    return redirect()->route('admin.trips.index')->with('success', 'Trip added successfully!');
}


   
   
        public function edit($id)
        {
            $trip = Trip::findOrFail($id);

            // For dropdowns
            $locations = Location::select('id', 'city_name')->get();

            return view('admin.trip.edit', compact('trip', 'locations'));
        }

        public function update(Request $request, $id)
        {
            $request->validate([
                'origin_id'       => 'required|integer',
                'destination_id'  => 'required|integer|different:origin_id',
                'operator_name'   => 'required|string|max:255',
                'bus_type'        => 'required|string|max:255',
                'price'           => 'required|numeric|min:0',
                'capacity'        => 'required|integer|min:1',
                'departure_time'  => 'required|date_format:H:i',
                'arrival_time'    => 'required|date_format:H:i|after:departure_time',
            ]);

            $trip = Trip::findOrFail($id);

            $trip->update([
                'origin_id'      => $request->origin_id,
                'destination_id' => $request->destination_id,
                'operator_name'  => $request->operator_name,
                'bus_type'       => $request->bus_type,
                'price'          => $request->price,
                'capacity'       => $request->capacity,
                'departure_time' => $request->departure_time,
                'arrival_time'   => $request->arrival_time,
            ]);

            return redirect()->route('admin.trips.index')->with('success', 'Trip updated successfully!');
        }

        public function destroy($id)
        {
            // Find the trip, if not found, throw 404
            $trip = Trip::findOrFail($id);

            // Delete the trip
            $trip->delete();

            // Redirect back to trips index with success message
            return redirect()->route('admin.trips.index')
                            ->with('success', 'Trip deleted successfully!');
        }


    
   
}
