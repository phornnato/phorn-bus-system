<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Trip;
use Illuminate\Http\Request;

class BusController extends Controller
{
    public function index()
    {
        $locations = Location::orderBy('city_name')->get(); 
        return view('welcome', compact('locations')); 
    }

    public function search(Request $request)
    {
        // 1. Validation
        logger('Search request:', $request->all());
        $validated = $request->validate([
            'origin' => 'required|exists:locations,id',
            'destination' => 'required|exists:locations,id|different:origin', // Added 'different:origin' validation
            'date' => 'required|date|after_or_equal:today',
        ]);

        $searchDate = $validated['date'];

        // 2. Fetch Trips and EAGER LOAD the specific schedule for the search date
        // FIX: Removed 'operator' from with() to prevent RelationNotFoundException.
        $trips = Trip::with(['origin', 'destination', 'schedules' => function ($q) use ($searchDate) {
            $q->where('journey_date', $searchDate);
        }])
        ->where('origin_id', $validated['origin'])
        ->where('destination_id', $validated['destination'])
        ->get();

        // Debugging: Log the IDs of the trips found
        logger('Trips found:', [
            'count' => $trips->count(),
            'trip_ids' => $trips->pluck('id')->toArray(),
            'origin' => $validated['origin'],
            'destination' => $validated['destination'],
            'searchDate' => $searchDate,
        ]);

        $locations = Location::orderBy('city_name')->get();

        // 3. CRITICAL FIX: Return the results to the 'searchresult' view, 
        // passing IDs for persistence in the search form.
        return view('welcome', [
            'trips' => $trips,
            'searchDate' => $searchDate,
            'locations' => $locations, // Needed for the search form component
            'originId' => $validated['origin'], // Needed for pre-selecting the origin
            'destinationId' => $validated['destination'], // Needed for pre-selecting the destination
        ]);
    }
}
