<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>BusFinder Cambodia - Book Your Tickets Online</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Customizing Tailwind colors to use a primary blue/indigo */
        /* You can define custom colors in the Tailwind config if using a build process */
        :root {
            --color-primary: #4f46e5; /* Indigo-600 */
            --color-secondary: #f59e0b; /* Amber-500 */
        }
        
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #ffffff; }
        
        
        @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        .animate-fade-in { animation: fadeIn 0.25s ease-out; }


        .hero-bg {
            background-color: #f7f9fb;
            /* Added subtle top curve or design element */
            clip-path: polygon(0 0, 100% 0, 100% 90%, 0% 100%);
            padding-bottom: 8rem; /* Space for the search card to float over */
        }
        
        .search-card { 
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15); 
            border-radius: 1.5rem; /* Rounded corners */
        }
        
        .trip-card {
            border-left: 5px solid var(--color-primary);
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .trip-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.1);
        }

        /* Style for the swap button animation */
        .swap-button:hover svg {
            transform: rotate(180deg);
        }
    </style>
    <script>

        function openLoginModal() {
            document.getElementById('loginModal').classList.remove('hidden');
        }
        function closeLoginModal() {
            document.getElementById('loginModal').classList.add('hidden');
        }
        // JS for Origin/Destination Swap functionality
        function swapLocations() {
            const originSelect = document.getElementById('origin');
            const destinationSelect = document.getElementById('destination');
            
            const tempValue = originSelect.value;
            originSelect.value = destinationSelect.value;
            destinationSelect.value = tempValue;
        }

        // JS for setting the minimum date on load for robust client-side validation
        document.addEventListener('DOMContentLoaded', () => {
            const dateInput = document.getElementById('date');
            const today = new Date().toISOString().split('T')[0];
            dateInput.min = today;
            // Also ensure it defaults to today if no value is set (good practice)
            if (!dateInput.value) {
                dateInput.value = today;
            }
        });
    </script>
</head>
<body class="min-h-screen antialiased">

    <div class="hero-bg bg-indigo-600 text-white">
        <header class="shadow-md sticky top-0 z-20 bg-indigo-600/95 backdrop-blur-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
                <div class="text-3xl font-extrabold flex items-center">
                    <span class="mr-2 text-yellow-400">🚌</span> BusFinder <span class="text-yellow-300 ml-1 text-base">🇰🇭</span>
                </div>
                <nav class="space-x-4 hidden sm:flex items-center">
                    {{-- PHP/Blade Logic (replace with static for pure HTML if needed) --}}
                    @if(session('user'))
                        <div class="flex items-center space-x-3">
                            <span class="font-medium px-3 py-1 bg-white/10 rounded-full text-sm">
                                Hi, <strong>{{ session('user')->name }}</strong>
                            </span>
                            <a href="{{ route('user.dashboard') }}" class="font-medium hover:text-indigo-200 transition">
                                Dashboard
                            </a>
                            <a href="{{ route('user.logout') }}" class="bg-yellow-400 text-indigo-800 px-4 py-2 rounded-full text-sm font-bold hover:bg-yellow-300 transition">
                                Logout
                            </a>
                        </div>
                    @else
                        <a href="{{ route('user.login') }}" class="bg-yellow-400 text-indigo-800 px-4 py-2 rounded-full text-sm font-bold hover:bg-yellow-300 transition">
                            Login / Sign Up
                        </a>
                    @endif
                </nav>
            </div>
        </header>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-16">
            <h1 class="text-4xl md:text-5xl font-extrabold text-center mb-4">
                Bus Tickets in Cambodia
            </h1>
            <p class="text-center text-indigo-200 text-xl font-light">
                Find the best trips from Phnom Penh, Siem Reap, Sihanoukville and more.
            </p>
        </div>
    </div>


    <main class="-mt-20 relative z-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="search-card bg-white p-6 md:p-8 border border-gray-100">
                <form action="{{ route('search') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end relative">
                    
                    <div class="relative md:col-span-2 flex items-stretch">
                        <div class="flex-1 pr-1">
                            <label for="origin" class="block text-xs font-semibold text-gray-500 mb-1">DEPARTING FROM</label>
                            <select id="origin" name="origin" required 
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3 text-base text-gray-700">
                                <option value="" disabled {{ old('origin') ? '' : 'selected' }}>Select Origin City</option>
                                {{-- PHP/Blade Logic --}}
                                @foreach ($locations as $location)
                                    <option value="{{ $location->id }}" {{ (old('origin') == $location->id || (isset($origin) && $origin == $location->id)) ? 'selected' : '' }}>
                                        {{ $location->city_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="button" onclick="swapLocations()" title="Swap Origin and Destination"
                            class="swap-button absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 mt-3 w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center shadow-lg transition duration-200 z-20 border-4 border-white hover:bg-indigo-700">
                            <svg class="w-4 h-4 transition duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        </button>

                        <div class="flex-1 pl-1">
                            <label for="destination" class="block text-xs font-semibold text-gray-500 mb-1">ARRIVING AT</label>
                            <select id="destination" name="destination" required 
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3 text-base text-gray-700">
                                <option value="" disabled {{ old('destination') ? '' : 'selected' }}>Select Destination City</option>
                                {{-- PHP/Blade Logic --}}
                                @foreach ($locations as $location)
                                    <option value="{{ $location->id }}" {{ (old('destination') == $location->id || (isset($destination) && $destination == $location->id)) ? 'selected' : '' }}>
                                        {{ $location->city_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="date" class="block text-xs font-semibold text-gray-500 mb-1">JOURNEY DATE</label>
                        <input type="date" id="date" name="date" required 
                                value="{{ old('date', $searchDate ?? date('Y-m-d')) }}" min="{{ date('Y-m-d') }}"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3 text-base text-gray-700">
                    </div>

                    <div class="col-span-1">
                        <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-3.5 rounded-lg hover:bg-indigo-700 transition duration-150 shadow-lg shadow-indigo-500/50 text-base">
                            <span class="hidden sm:inline">Search Buses</span>
                            <span class="sm:hidden">Search</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- PHP/Blade Logic Block --}}
            @php
                $isSearching = isset($trips);
                $tripCount = $isSearching ? $trips->count() : 0;
                $displayDate = $searchDate ?? old('date') ?? date('Y-m-d');
                $originNameDisplay = 'Origin';
                $destinationNameDisplay = 'Destination';

                // Safely extract location names for the header
                if ($tripCount > 0) {
                    $firstTrip = $trips->first();
                    $originNameDisplay = optional(optional($firstTrip)->origin)->city_name ?? (isset($originName) ? $originName : 'Origin');
                    $destinationNameDisplay = optional(optional($firstTrip)->destination)->city_name ?? (isset($destinationName) ? $destinationName : 'Destination');
                }
                
                // Function to format time safely, using a standard format like 14:30
                function formatTimeSafe($timeStr) {
                    if (empty($timeStr)) return 'TBD';
                    try {
                        // Using 'H:i' for 24-hour time or 'g:i A' for 12-hour time
                        return date('H:i', strtotime($timeStr)); 
                    } catch (\Exception $e) {
                        return $timeStr; // Fallback
                    }
                }
                // Placeholder function for calculating duration (needs real data/logic)
                function calculateDuration($departureTime) {
                    if ($departureTime == 'TBD') return 'N/A';
                    // For demo: Assume a fixed 6-hour trip
                    return '6h 00m'; 
                }
            @endphp
            {{-- End PHP/Blade Logic Block --}}
            
            <div class="mt-10 mb-6">
                <h2 class="text-2xl font-extrabold text-gray-800">
                    @if($tripCount > 0)
                        {{ $tripCount }} Bus{{ $tripCount > 1 ? 'es' : '' }} found: <span class="text-indigo-600">{{ $originNameDisplay }} → {{ $destinationNameDisplay }}</span>
                    @elseif($isSearching)
                        No buses found for this route.
                    @else
                        Welcome! Search to find your bus trip in Cambodia.
                    @endif
                </h2>
                @if($isSearching)
                    <p class="text-gray-500 text-sm mt-1">
                        Journey Date: **{{ \Carbon\Carbon::parse($displayDate)->format('l, F d, Y') }}**
                    </p>
                @endif
            </div>
            
            <div class="space-y-4">
                @if($isSearching)
                    @if($tripCount > 0)
                        @foreach ($trips as $trip)
                            {{-- PHP/Blade Logic for trip data --}}
                            @php
                                $schedule = optional($trip->schedules)->firstWhere('date', $displayDate) 
                                                        ?? optional($trip->schedules)->firstWhere('schedule_date', $displayDate) 
                                                        ?? optional($trip->schedules)->first();

                                $availableSeats = (int) (optional($schedule)->available_seats ?? optional($trip)->capacity ?? 0);
                                $seatsColor = $availableSeats <= 5 ? 'text-red-600 font-extrabold' : ($availableSeats < 20 ? 'text-amber-600 font-semibold' : 'text-green-600 font-semibold');
                                $price = optional($schedule)->price ?? optional($trip)->price ?? 0;
                                $departureTime = formatTimeSafe(optional($schedule)->departure_time ?? optional($trip)->departure_time);
                                $arrivalTime = optional($schedule)->arrival_time ?? optional($trip)->arrival_time; // Assuming arrival time exists
                                $duration = calculateDuration($departureTime); 
                            @endphp
                            {{-- End PHP/Blade Logic --}}

                            @if ($availableSeats > 0)
                                <div class="trip-card bg-white p-4 sm:p-6 rounded-xl shadow-lg flex flex-col md:flex-row justify-between items-start md:items-center border-t-0 border-r-0 border-b-0">
                                    
                                    <div class="flex-1 min-w-0 mb-4 md:mb-0 md:w-1/3">
                                        <div class="flex items-center space-x-4">
                                            <div class="bus-operator-logo w-14 h-14 flex items-center justify-center bg-indigo-100 rounded-lg font-extrabold text-xl text-indigo-700 border border-indigo-300 flex-shrink-0">
                                                {{ \Illuminate\Support\Str::upper(substr($trip->operator_name ?? 'OP', 0, 2)) }}
                                            </div>
                                            <div>
                                                <h4 class="text-xl font-extrabold text-gray-900">{{ $trip->operator_name ?? 'Operator Name' }}</h4>
                                                <p class="text-sm text-gray-500">{{ $trip->bus_type ?? 'Standard Bus' }}</p>
                                                <p class="text-xs text-gray-400 mt-1">Wifi, AC, Water</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-6 mx-4 flex-shrink-0 md:w-1/3 justify-center">
                                        
                                        <div class="text-center">
                                            <p class="text-3xl font-extrabold text-indigo-600">{{ $departureTime }}</p>
                                            <p class="text-sm text-gray-600 font-semibold mt-1">Departure</p>
                                        </div>

                                        <div class="text-center text-gray-400 text-sm w-32 relative">
                                            <p class="font-semibold text-gray-700 mb-1">{{ $duration }}</p>
                                            <div class="h-0.5 bg-gray-300 w-full relative">
                                                <div class="absolute w-2.5 h-2.5 bg-indigo-600 rounded-full top-1/2 left-0 -translate-y-1/2 -ml-1"></div>
                                                <div class="absolute w-2.5 h-2.5 bg-indigo-600 rounded-full top-1/2 right-0 -translate-y-1/2 -mr-1"></div>
                                            </div>
                                        </div>
                                        
                                        <div class="text-center">
                                            <p class="text-2xl font-bold text-gray-800">{{ formatTimeSafe($arrivalTime) }}</p>
                                            <p class="text-sm text-gray-600 font-semibold mt-1">Arrival</p>
                                        </div>
                                    </div>

                                    <div class="flex-shrink-0 text-right space-y-2 md:pl-8 mt-4 md:mt-0 md:w-1/3">
                                        <p class="text-sm {{ $seatsColor }} text-right">
                                            <span class="inline-block px-2 py-1 bg-white border rounded-full text-xs font-bold shadow-sm">
                                                {{ $availableSeats }} Seats Left!
                                            </span>
                                        </p>
                                        <span class="text-4xl font-extrabold text-red-600 block leading-none">
                                            ${{ number_format((float)$price, 2) }}
                                        </span>

                                       @if(Session::has('user'))
                                            <a href="{{ route('booking.show', ['trip_id' => $trip->id]) }}?date={{ $displayDate }}"
                                            class="bg-red-600 text-white font-bold py-2.5 px-8 rounded-full hover:bg-red-700 transition duration-150 shadow-lg text-lg inline-block mt-2">
                                                Book Now
                                            </a>
                                        @else
                                        <a href="javascript:void(0)"
                                            onclick="openLoginModal()"
                                            class="bg-red-500 text-white font-bold py-2.5 px-8 rounded-full hover:bg-red-600 transition duration-200 shadow-lg text-lg inline-block mt-2">
                                                Login to Book
                                            </a>
                                        @endif

                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @else
                        <div class="bg-gray-50 border border-gray-200 text-gray-700 p-8 rounded-xl text-center mt-8 shadow-inner">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 19.172A4 4 0 018 17.586V13a4 4 0 00-4-4H1a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v2a2 2 0 002 2h4a2 2 0 012 2v4a2 2 0 01-2 2h-4a2 2 0 00-2 2z"></path></svg>
                            <p class="text-xl font-bold mb-2">No Buses Found</p>
                            <p>We couldn't find any available buses for the selected route on **{{ \Carbon\Carbon::parse($displayDate)->format('F d, Y') }}**. Please check a different date or route.</p>
                        </div>
                    @endif
                @endif
            </div>
            <!-- Modal -->
            <div id="loginModal"
                class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-sm w-full text-center animate-fade-in">
                    <h2 class="text-2xl font-bold text-gray-800 mb-3">Login Required</h2>
                    <p class="text-gray-600 mb-6">You must be logged in to book a trip. Please log in to continue.</p>
                    <div class="flex justify-center gap-4">
                        <button onclick="closeLoginModal()"
                                class="bg-gray-300 text-gray-700 px-5 py-2 rounded-full hover:bg-gray-400 transition">
                            Cancel
                        </button>
                        <a href="{{ route('user.login') }}"
                        class="bg-red-600 text-white px-5 py-2 rounded-full hover:bg-red-700 transition">
                            Go to Login
                        </a>
                    </div>
                </div>
            </div>

            <div class="mt-20 bg-white p-8 rounded-xl border border-gray-100 shadow-xl">
                <h3 class="text-2xl font-bold text-gray-800 mb-6 text-center">Your Trusted Bus Booking Platform</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                    <div class="p-4">
                        <svg class="w-10 h-10 text-indigo-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c1.657 0 3 .895 3 2s-1.343 2-3 2h-1a1 1 0 00-1 1v3m4-3h-1m-4 0h-1m-4 0h-1m-4 0h-1"></path></svg>
                        <span class="text-indigo-600 text-3xl font-extrabold block">BEST PRICES</span>
                        <p class="text-gray-500 mt-2 text-sm">No booking fees, transparent pricing.</p>
                    </div>
                    <div class="p-4 border-l border-r border-gray-200">
                        <svg class="w-10 h-10 text-indigo-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2l2 2v-2h2a2 2 0 002-2v-2"></path></svg>
                        <span class="text-indigo-600 text-3xl font-extrabold block">24/7 SUPPORT</span>
                        <p class="text-gray-500 mt-2 text-sm">Dedicated customer service via chat and phone.</p>
                    </div>
                    <div class="p-4">
                        <svg class="w-10 h-10 text-indigo-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.009 12.009 0 001 12c0 3.072 1.815 5.751 4.532 7.152l3.4 1.487m2.73-3.21l1.597 1.597"></path></svg>
                        <span class="text-indigo-600 text-3xl font-extrabold block">SECURE BOOKING</span>
                        <p class="text-gray-500 mt-2 text-sm">Protected payment gateway for peace of mind.</p>
                    </div>
                </div>
            </div>

        </div>
    </main>
    
    <footer class="bg-gray-100 mt-16 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm text-gray-500">
            <p>&copy; 2025 BusFinder Cambodia. All rights reserved.</p>
            <div class="mt-2 space-x-4">
                <a href="#" class="hover:text-indigo-600 transition">Privacy Policy</a>
                <span class="text-gray-400">|</span>
                <a href="#" class="hover:text-indigo-600 transition">Terms of Use</a>
            </div>
        </div>
    </footer>

</body>
</html>