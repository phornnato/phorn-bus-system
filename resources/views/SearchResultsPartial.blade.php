{{-- Assume this is your searchresult.blade.php --}}

@php
    // Get the search date passed from the BusController
    // Use the variable name you passed (e.g., $searchDate)
    $displayDate = $searchDate ?? date('Y-m-d'); 
@endphp

@foreach ($trips as $trip)
    @php
        // 1. Find the specific trip schedule for the journey date
        // Use 'journey_date' as confirmed from your database image
        $schedule = $trip->schedules->firstWhere('journey_date', $displayDate); 
        
        $isScheduled = $schedule !== null;

        // 2. Determine available seats: use schedule seats first, fall back to main trip capacity
        $availableSeats = $isScheduled 
            ? (int) $schedule->available_seats 
            : (int) optional($trip)->capacity ?? 0; 
            
        // 3. Determine price: use schedule price first, fall back to main trip price
        $price = $isScheduled 
            ? optional($schedule)->price ?? optional($trip)->price ?? 0 
            : optional($trip)->price ?? 0;

        // 4. Set CSS class based on seats
        $seatsColor = $availableSeats <= 5 ? 'text-red-500 font-extrabold' : ($availableSeats < 20 ? 'text-yellow-600 font-semibold' : 'text-green-600 font-semibold');
        
        // 5. Determine if the trip is bookable/clickable (e.g., seats > 0)
        $isBookable = $availableSeats > 0;
    @endphp

    {{-- Trip Card Display --}}
    <div class="trip-card bg-white p-6 rounded-xl shadow-md mb-4 flex justify-between items-center border border-gray-200">
        
        {{-- Left Section: Operator and Bus Type (You can add your existing display here) --}}
        <div>
            <p class="font-bold text-lg">{{ $trip->operator_name ?? 'N/A' }}</p>
            <p class="text-sm text-gray-500">{{ $trip->bus_type ?? 'N/A' }}</p>
        </div>

        {{-- Center Section: Time and Duration --}}
        <div class="flex items-center text-center">
            {{-- Display times here using $trip->departure_time or $schedule->departure_time --}}
            <span class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($trip->departure_time)->format('H:i') }}</span>
            <span class="mx-4 text-xs text-gray-400">...</span>
            <span class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($trip->arrival_time)->format('H:i') }}</span>
        </div>

        {{-- Right Section: Seats, Price, and Button --}}
        <div class="text-right flex flex-col items-end">
            <p class="{{ $seatsColor }} text-sm">{{ $availableSeats }} Seats Left!</p>
            <p class="text-2xl font-bold text-red-600 mt-1">${{ number_format($price, 2) }}</p>
            
            @if ($isBookable)
                {{-- This is the link that must be in the loop and use the current $trip->id --}}
                <a href="{{ route('booking.show', ['trip_id' => $trip->id, 'date' => $displayDate]) }}" 
                    class="mt-2 bg-red-600 text-white font-semibold py-2 px-6 rounded-lg hover:bg-red-700 transition duration-150 shadow-md text-sm inline-block">
                    View Seats 
                </a>
            @else
                <button disabled
                    class="mt-2 bg-gray-400 text-white font-semibold py-2 px-6 rounded-lg shadow-md text-sm cursor-not-allowed">
                    Sold Out
                </button>
            @endif
        </div>
    </div>
@endforeach