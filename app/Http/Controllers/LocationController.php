<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function Index()
    {
        $data = Location::all();
        return view('location.location', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'city_name' => 'required|string|max:100|unique:locations,city_name',
        ]);

        Location::create([
            'city_name' => $request->city_name,
        ]);

        return redirect()->back()->with('success', 'Location created successfully!');
    }

    public function update(Request $request, $id)
    {
        $location = Location::findOrFail($id);

        $request->validate([
            'city_name' => 'required|string|max:100|unique:locations,city_name,' . $id,
        ]);

        $location->city_name = $request->city_name;
        $location->save();

        return redirect()->back()->with('success', 'Location updated successfully!');
    }

    public function destroy($id)
    {
        $location = Location::findOrFail($id);
        $location->delete();

        return redirect()->back()->with('success', 'Location deleted successfully!');
    }
}
