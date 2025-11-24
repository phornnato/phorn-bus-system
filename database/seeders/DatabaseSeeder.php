<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Location;
use App\Models\Trip;
use App\Models\TripPoint;
use App\Models\TripSchedule;

class InitialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Create Locations
        $pp = Location::create(['city_name' => 'Phnom Penh']);
        $sr = Location::create(['city_name' => 'Siem Reap']);
        $shv = Location::create(['city_name' => 'Sihanoukville']);

        // 2. Create Trips
        $tripsData = [
            [
                'origin_id' => $pp->id, 'destination_id' => $sr->id, 'operator_name' => 'Giant Ibis', 'bus_type' => 'VIP Sleeper',
                'price' => 25.00, 'capacity' => 40, 'departure_time' => '07:30:00', 'arrival_time' => '14:00:00',
                'points' => [
                    ['type' => 'boarding', 'time' => '07:30:00', 'name' => 'Phnom Penh Fantasy Park', 'address' => 'Road 106, Khan Doun Penh', 'lat' => 11.5670, 'lng' => 104.9220],
                    ['type' => 'dropping', 'time' => '14:00:00', 'name' => 'Pub Street', 'address' => 'Khmer Pub Street, Krong Siem Reap', 'lat' => 13.3540, 'lng' => 103.8565],
                ]
            ],
            [
                'origin_id' => $pp->id, 'destination_id' => $sr->id, 'operator_name' => 'Virak Buntham', 'bus_type' => 'Express Bus',
                'price' => 20.00, 'capacity' => 40, 'departure_time' => '09:00:00', 'arrival_time' => '15:30:00',
                'points' => [
                    ['type' => 'boarding', 'time' => '09:00:00', 'name' => 'Virak Buntham Office (PP)', 'address' => 'Monivong Blvd', 'lat' => 11.5542, 'lng' => 104.9210],
                    ['type' => 'dropping', 'time' => '15:30:00', 'name' => 'Siem Reap Tourist Area', 'address' => 'Sok San Road', 'lat' => 13.3500, 'lng' => 103.8580],
                ]
            ],
            // Add more trips here...
        ];

        foreach ($tripsData as $data) {
            $points = $data['points'];
            unset($data['points']);

            $trip = Trip::create($data);

            // Create Trip Points
            foreach ($points as $point) {
                $trip->points()->create($point);
            }

            // Create a default schedule for today and tomorrow
            TripSchedule::create([
                'trip_id' => $trip->id,
                'journey_date' => now()->format('Y-m-d'),
                'available_seats' => $trip->capacity - rand(1, 15) // Mock booked seats
            ]);
            
            TripSchedule::create([
                'trip_id' => $trip->id,
                'journey_date' => now()->addDay()->format('Y-m-d'),
                'available_seats' => $trip->capacity - rand(1, 15)
            ]);
        }
    }
}
