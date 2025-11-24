<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking: {{ $trip->origin->city_name }} to {{ $trip->destination->city_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #f7f9fb; }
        .booking-card { background-color: #fff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); }
        .step-indicator { 
            transition: all 0.2s; 
            padding: 4px 0;
            border-bottom: 2px solid transparent;
            color: #9ca3af;
        }
        .step-indicator.active { color: #e53e3e; border-bottom: 2px solid #e53e3e; font-weight: 700; }
        .seat { cursor: pointer; display: flex; align-items: center; justify-content: center; width: 30px; height: 40px; margin: 4px; border-radius: 4px; font-size: 10px; font-weight: 600; transition: transform 0.1s, background-color 0.2s; }
        .seat-available { background-color: #e2e8f0; color: #4a5568; }
        .seat-booked { background-color: #f56565; color: white; cursor: not-allowed; }
        .seat-selected { background-color: #38a169; color: white; transform: scale(1.1); box-shadow: 0 2px 8px rgba(56, 161, 105, 0.5); }
        .seat-legend-icon { width: 24px; height: 24px; border-radius: 4px; }
        
        /* Modal Styles */
        .modal-overlay { 
            position: fixed; top: 0; left: 0; right: 0; bottom: 0; 
            background-color: rgba(0, 0, 0, 0.75); 
            z-index: 50; 
            display: flex; justify-content: center; align-items: center; 
        }
        .modal-content { 
            max-width: 450px; 
            width: 95%; 
            background-color: white; 
            border-radius: 12px; 
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2); 
            transform: scale(0.95); 
            opacity: 0; 
            transition: all 0.2s ease-in-out; 
        }
        .modal-content.show { transform: scale(1); opacity: 1; }
        
        /* Print Styles */
        @media print {
            body * { visibility: hidden; }
            #receipt-print-area, #receipt-print-area * { visibility: visible; }
            #receipt-print-area { 
                position: absolute; 
                left: 50%; 
                top: 0; 
                transform: translateX(-50%);
                width: 148mm;
                height: 210mm;
                padding: 20px; 
                box-sizing: border-box; 
                background: white; 
                margin: 0; 
                box-shadow: none;
            }
            .modal-overlay { display: none !important; }
            .no-print { display: none !important; }
            #receipt-print-area p, #receipt-print-area h1, #receipt-print-area h2, #receipt-print-area span { color: #000 !important; }
        }

        /* User Account Section */
        .user-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        /* Bus Layout */
        .bus-layout {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            max-width: 200px;
            margin: 0 auto;
        }
        .driver-area {
            grid-column: 1 / -1;
            text-align: center;
            padding: 10px;
            background: #4a5568;
            color: white;
            border-radius: 4px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body class="min-h-screen">
    <header class="bg-red-600 shadow-lg no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 text-white font-extrabold text-2xl">
            🚌 BusFinder 🇰🇭
        </div>
    </header>

    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
        <!-- User Account Section -->
        <div class="user-section no-print">
            <div class="flex justify-between items-center">
                <div>
                    @auth
                    <h2 class="text-xl font-bold">Welcome, {{ Auth::user()->name }}!</h2>
                    <p class="text-blue-100">You're booking as a registered user</p>
                    @else
                    <h2 class="text-xl font-bold">Welcome, Guest!</h2>
                    <p class="text-blue-100">
                        <a href="{{ route('user.login') }}" class="underline hover:text-white">Login</a> or 
                        <a href="{{ route('user.register') }}" class="underline hover:text-white">Register</a> for faster booking
                    </p>
                    @endauth
                </div>
                @auth
                <div class="text-right">
                    <p class="text-sm text-blue-100">Account: {{ Auth::user()->email }}</p>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm underline hover:text-white mt-1">Logout</button>
                    </form>
                </div>
                @endauth
            </div>
        </div>

        <!-- Navigation Breadcrumbs -->
        <div class="flex items-center space-x-2 text-sm text-gray-500 mb-6 no-print">
            <a href="{{ route('search', ['origin' => $trip->origin_id, 'destination' => $trip->destination_id, 'date' => $searchDate]) }}" class="hover:text-red-500">
                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Results
            </a>
            <span class="text-gray-400">/</span>
            <span class="font-bold text-gray-800">{{ $trip->origin->city_name }} → {{ $trip->destination->city_name }}</span>
        </div>

        <!-- Step Indicators -->
        <div id="booking-steps" class="bg-white p-4 rounded-t-xl shadow-md border-b-2 border-gray-100 flex justify-center space-x-8 md:space-x-12 mb-8 no-print">
            <div id="step-1-indicator" class="step-indicator active cursor-pointer" data-step="1" onclick="showStep(1)">1. Select seats</div>
            <div id="step-2-indicator" class="step-indicator text-gray-400 cursor-pointer" data-step="2" onclick="showStep(2)">2. Select points</div>
            <div id="step-3-indicator" class="step-indicator text-gray-400 cursor-pointer" data-step="3" onclick="showStep(3)">3. Passenger Info</div>
            <div id="step-4-indicator" class="step-indicator text-gray-400 cursor-pointer" data-step="4" onclick="showStep(4)">4. Payment</div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <!-- Step 1: Seat Selection -->
                <div id="step-1" class="booking-card p-6 block">
                    <div class="mb-6 pb-4 border-b">
                        <h3 class="text-2xl font-bold text-gray-800">{{ $trip->operator_name }}</h3>
                        <p class="text-sm text-gray-500">{{ $trip->departure_time->format('H:i') }} - {{ $trip->arrival_time->format('H:i') }} | {{ $trip->bus_type }}</p>
                    </div>

                    <h4 class="text-lg font-bold text-gray-700 mb-4">Select Your Seats</h4>
                    <p class="text-gray-500 text-sm mb-4">Click on available seats to select them. Green seats are already selected by you.</p>
                    
                    <!-- Seat Selection Status -->
                    <div id="seat-selection-status" class="mb-4 p-3 bg-blue-50 rounded-lg">
                        <p class="text-sm text-blue-700">You have selected <span id="selected-seats-count">0</span> seat(s): <span id="selected-seats-list" class="font-bold"></span></p>
                    </div>
                </div>

                <!-- Step 2: Points Selection -->
                <div id="step-2" class="booking-card p-6 hidden">
                    <h2 class="text-xl font-bold mb-4">2. Select boarding & dropping points</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-700">Boarding points</h3>
                            @foreach ($boardingPoints as $point)    
                            <label for="boarding-{{ $point->id }}" class="flex items-center justify-between p-4 booking-card border cursor-pointer hover:bg-red-50 transition">
                                <div>
                                    <p class="font-bold text-gray-800">{{ $point->time->format('H:i') }} {{ $point->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $point->address }}</p>
                                    @if($point->map)
                                    <div class="map-container mt-2" style="width: 200px; height: 150px; overflow: hidden; border-radius: 8px;">
                                        {!! $point->map !!}
                                    </div>
                                    @endif
                                </div>
                                <input type="radio" id="boarding-{{ $point->id }}" name="boarding_point" value="{{ $point->id }}" 
                                    data-name="{{ $point->name }}" data-time="{{ $point->time->format('H:i') }}" 
                                    data-address="{{ $point->address }}" class="text-red-500 focus:ring-red-500">
                            </label>
                            @endforeach
                        </div>
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-700">Dropping points</h3>
                            @foreach ($droppingPoints as $point)
                            <label for="dropping-{{ $point->id }}" class="flex items-center justify-between p-4 booking-card border cursor-pointer hover:bg-red-50 transition">
                                <div>
                                    <p class="font-bold text-gray-800">{{ $point->time->format('H:i') }} {{ $point->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $point->address }}</p>
                                    @if($point->map)
                                    <div class="map-container mt-2" style="width: 200px; height: 150px; overflow: hidden; border-radius: 8px;">
                                        {!! $point->map !!}
                                    </div>
                                    @endif
                                </div>
                                <input type="radio" id="dropping-{{ $point->id }}" name="dropping_point" value="{{ $point->id }}" 
                                    data-name="{{ $point->name }}" data-time="{{ $point->time->format('H:i') }}" 
                                    data-address="{{ $point->address }}" class="text-red-500 focus:ring-red-500">
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Step 3: Passenger Information -->
                <div id="step-3" class="booking-card p-6 hidden">
                    <h2 class="text-xl font-bold mb-6">3. Passenger Information</h2>
                    
                    <div class="mb-8 p-4 booking-card border">
                        <h3 class="font-bold mb-3">Contact details (E-Ticket will be sent here)</h3>
                        @auth
                        <input type="email" id="contact-email" placeholder="Email ID" value="{{ Auth::user()->email }}" class="w-full border-gray-300 rounded-lg p-3 mb-4" required>
                        <div class="flex space-x-2">
                            <select id="contact-country-code" class="border-gray-300 rounded-lg p-3 w-1/4">
                                <option value="+855">+855 (KHM)</option>
                                <option value="+66">+66 (THL)</option>
                            </select>
                            <input type="tel" id="contact-phone" placeholder="Phone" value="{{ Auth::user()->phone ?? '' }}" class="w-3/4 border-gray-300 rounded-lg p-3" required>
                        </div>
                        @else
                        <input type="email" id="contact-email" placeholder="Email ID" class="w-full border-gray-300 rounded-lg p-3 mb-4" required>
                        <div class="flex space-x-2">
                            <select id="contact-country-code" class="border-gray-300 rounded-lg p-3 w-1/4">
                                <option value="+855">+855 (KHM)</option>
                                <option value="+66">+66 (THL)</option>
                            </select>
                            <input type="tel" id="contact-phone" placeholder="Phone" class="w-3/4 border-gray-300 rounded-lg p-3" required>
                        </div>
                        @endauth
                    </div>

                    <div id="passenger-forms-container">
                        <p class="text-gray-500 p-4 border rounded-lg">Please select seats in Step 1 to fill passenger details.</p>
                    </div>
                </div>

                <!-- Step 4: Payment Selection -->
                <div id="step-4" class="booking-card p-6 hidden">
                    <h2 class="text-xl font-bold mb-6">4. Select Payment Method</h2>
                    
                    <div class="p-6 bg-red-50 rounded-lg border-2 border-red-200 mb-6">
                        <h3 class="text-lg font-bold text-red-700">Total Due: <span id="payment-total-price-display" class="ml-2">${{ number_format(0, 2) }}</span></h3>
                        <p class="text-sm text-red-600">Please choose a secure payment method below.</p>
                    </div>

                    <div id="payment-options-container" class="space-y-4">
                       
                       <!-- ABA Payment Option -->
                    <label for="pay-aba" class="flex items-center justify-between p-5 booking-card border-2 cursor-pointer hover:border-red-500 transition border-gray-200 rounded-lg">
                        <div class="flex items-center space-x-4">
                            <img src="https://placehold.co/40x40/004a8e/ffffff?text=ABA" alt="ABA Bank Logo" class="h-10 w-10 object-contain rounded-md">
                            <p class="font-bold text-gray-800">ABA Pay (Scan QR)</p>
                        </div>
                        <input type="radio" id="pay-aba" name="payment_method" value="ABA Pay" class="text-red-600 focus:ring-red-600 h-5 w-5">
                    </label>

                    <!-- Credit Card Payment Option -->
                    <label for="pay-credit" class="flex items-center justify-between p-5 booking-card border-2 cursor-pointer hover:border-red-500 transition border-gray-200 rounded-lg">
                        <div class="flex items-center space-x-4">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8 text-blue-600">
                                <path d="M4.5 3.75a3 3 3 0 0 0-3 3v10.5a3 3 3 0 0 0 3 3h15a3 3 3 0 0 0 3-3V6.75a3 3 3 0 0 0-3-3h-15ZM4.5 6a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-.75.75H5.25a.75.75 0 0 1-.75-.75V6Zm0 3.75a.75.75 0 0 1 .75-.75h4.5a.75.75 0 0 1 0 1.5h-4.5a.75.75 0 0 1-.75-.75Zm0 3.75a.75.75 0 0 1 .75-.75h6.75a.75.75 0 0 1 0 1.5H5.25a.75.75 0 0 1-.75-.75Zm11.25 0a.75.75 0 1 0 0 1.5h1.5a.75.75 0 0 0 0-1.5h-1.5Z" />
                            </svg>
                            <p class="font-bold text-gray-800">Credit/Debit Card (Visa)</p>
                        </div>
                        <input type="radio" id="pay-credit" name="payment_method" value="Credit/Debit Card" class="text-red-600 focus:ring-red-600 h-5 w-5">
                    </label>
                    </div>

                    <!-- Credit Card Input Form -->
                    <div id="credit-card-form" class="mt-6 p-6 booking-card border-2 border-blue-100 hidden">
                        <h3 class="font-bold mb-4 text-gray-700 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 mr-2 text-blue-500"><path fill-rule="evenodd" d="M16.5 5.75c-.328-.328-.78-.5-1.25-.5H5.25c-.47 0-.922.172-1.25.5C3.79 6.13 3.5 6.58 3.5 7.05v9.5c0 .47.172.92.5 1.25.328.328.78.5 1.25.5H14c.47 0 .922-.172 1.25-.5.328-.328.5-.78.5-1.25V7.05c0-.47-.172-.92-.5-1.25Zm-3.15 3.3a.75.75 0 0 1 0 1.5h-7a.75.75 0 0 1 0-1.5h7ZM12 14.25a.75.75 0 0 0-.75-.75H5.25a.75.75 0 0 0 0 1.5h6a.75.75 0 0 0 .75-.75Z" clip-rule="evenodd" /><path d="M17.5 7.75c.47 0 .922-.172 1.25-.5.328-.328.5-.78.5-1.25V5.25c0-.47-.172-.92-.5-1.25C18.42 3.67 17.97 3.5 17.5 3.5h-12c-.328 0-.643.085-.922.25l1.248 1.248a4.5 4.5 0 0 1 3.203 1.139l1.697 1.697a.75.75 0 0 0 1.06 0l1.697-1.697a4.5 4.5 0 0 1 3.203-1.139l1.248-1.248c-.279-.165-.594-.25-.922-.25h-12c-1.24 0-2.348.548-3.12 1.41l.004.004c.772.862 1.88 1.41 3.12 1.41h12Z" /></svg>
                            Enter Visa/MasterCard Details
                        </h3>
                        <label class="block mb-4">
                            <span class="text-sm font-medium text-gray-700">Card Number (16 digits)</span>
                            <input type="text" id="card-number" placeholder="XXXX XXXX XXXX XXXX" maxlength="19" class="w-full border-gray-300 rounded-lg p-3 mt-1 focus:border-red-500 focus:ring-1 focus:ring-red-500 transition" required>
                        </label>
                        
                        <div class="flex space-x-4 mb-4">
                            <label class="w-1/2">
                                <span class="text-sm font-medium text-gray-700">Expiry (MM/YY)</span>
                                <input type="text" id="card-expiry" placeholder="MM/YY" maxlength="5" class="w-full border-gray-300 rounded-lg p-3 mt-1 focus:border-red-500 focus:ring-1 focus:ring-red-500 transition" required>
                            </label>
                            <label class="w-1/2">
                                <span class="text-sm font-medium text-gray-700">CVC (3 or 4 digits)</span>
                                <input type="text" id="card-cvc" placeholder="CVC" maxlength="4" class="w-full border-gray-300 rounded-lg p-3 mt-1 focus:border-red-500 focus:ring-1 focus:ring-red-500 transition" required>
                            </label>
                        </div>
                        
                        <label class="block mb-4">
                            <span class="text-sm font-medium text-gray-700">Card Holder Name</span>
                            <input type="text" id="card-name" placeholder="Name on card" class="w-full border-gray-300 rounded-lg p-3 mt-1 focus:border-red-500 focus:ring-1 focus:ring-red-500 transition" required>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <div id="seat-map-card" class="booking-card p-6 text-center">
                    <h3 class="font-bold mb-4 text-gray-700">Select Seats</h3>
                    
                    <div class="flex justify-center mb-6">
                        <div class="p-4 bg-gray-100 rounded-lg shadow-inner">
                            <div class="driver-area">
                                <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                Driver
                            </div>
                            <div class="bus-layout">
                                @php
                                    $bookedSeatNumbers = \App\Models\BookingSeat::where('schedule_id', $trip->id)
                                        ->pluck('seat_number')
                                        ->toArray();
                                @endphp
                                
                                @for($i = 1; $i <= $trip->capacity; $i++)
                                    @if(in_array($i, $bookedSeatNumbers))
                                        <div class="seat seat-booked" data-seat="{{ $i }}">{{ $i }}</div>
                                    @else
                                        <div class="seat seat-available" data-seat="{{ $i }}" onclick="toggleSeat(this, {{ $i }})">{{ $i }}</div>
                                    @endif
                                @endfor
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                        <h4 class="text-sm font-semibold mb-2">Seat Legend</h4>
                        <div class="flex justify-around text-xs text-gray-600">
                            <div class="text-center">
                                <div class="seat seat-available seat-legend-icon mx-auto mb-1"></div>
                                Available
                            </div>
                            <div class="text-center">
                                <div class="seat seat-booked seat-legend-icon mx-auto mb-1">X</div>
                                Booked
                            </div>
                            <div class="text-center">
                                <div class="seat seat-selected seat-legend-icon mx-auto mb-1"></div>
                                Selected
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Booking Summary Card -->
                <div id="booking-summary-card" class="booking-card p-6 sticky top-4">
                    <h3 class="text-lg font-bold mb-4">Your Trip Summary</h3>
                    
                    <div id="step-2-summary" class="hidden">
                        <p class="text-sm font-semibold mb-2">Trip: {{ $trip->operator_name }} ({{ $trip->bus_type }})</p>
                        <p class="text-sm">Route: {{ $trip->origin->city_name }} &rarr; {{ $trip->destination->city_name }}</p>
                        <p class="text-sm mb-4">Date: {{ \Carbon\Carbon::parse($searchDate)->format('D d M') }}</p>
                        
                        <div class="p-3 bg-red-50 rounded-lg mt-4">
                            <p class="text-sm font-bold text-red-700">Selected Seats:</p>
                            <span id="summary-seats" class="text-red-600 font-bold">None</span>
                            <p class="text-xs text-red-600">({{ $trip->bus_type }} Class)</p>
                        </div>
                    </div>

                    <div id="step-3-summary" class="hidden">
                        <div class="space-y-3 text-sm">
                            <h4 class="font-bold">Journey Details</h4>
                            <p><strong>Operator:</strong> {{ $trip->operator_name }}</p>
                            <p><strong>Route:</strong> {{ $trip->origin->city_name }} &rarr; {{ $trip->destination->city_name }}</p>
                            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($searchDate)->format('D d M') }}</p>
                            
                            <h4 class="font-bold pt-2">Seat details</h4>
                            <p id="summary-seats-final">0 seat(s): None</p>
                            
                            <h4 class="font-bold pt-2">Points</h4>
                            <p><strong>Boarding:</strong> <span id="summary-boarding-name">Not selected</span></p>
                            <p><strong>Dropping:</strong> <span id="summary-dropping-name">Not selected</span></p>
                        </div>
                    </div>
                    
                    <div id="step-4-summary" class="hidden">
                        <div class="space-y-3 text-sm">
                            <h4 class="font-bold">Confirmation & Payment</h4>
                            <p><strong>Contact Email:</strong> <span id="summary-contact-email">N/A</span></p>
                            <p><strong>Seats:</strong> <span id="summary-seats-final-payment">0 seat(s)</span></p>
                            <p><strong>Payment Method:</strong> <span id="summary-payment-method">Not selected</span></p>
                            <p class="mt-4 text-lg font-bold">Total Payable: <span class="text-red-600" id="summary-total-price-final"></span></p>
                        </div>
                    </div>

                    <div id="summary-price-box" class="mt-6 border-t pt-4">
                        <p class="text-sm text-gray-600">Total Seats: <span id="summary-count" class="font-bold">0</span></p>
                        <p class="text-xl font-bold mt-2 text-red-600">Total Price: <span id="summary-total-price">${{ number_format(0, 2) }}</span></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Footer -->
        <div id="action-footer" class="fixed bottom-0 left-0 right-0 bg-white shadow-xl p-4 border-t z-20 no-print">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <div class="flex items-center">
                    <span id="footer-count" class="font-bold text-lg mr-2">0 seat</span>
                    <span id="footer-price" class="text-2xl font-extrabold text-red-600">USD {{ number_format(0, 2) }}</span>
                </div>
                
                <button id="next-step-button" onclick="goToNextStep()" disabled
                    class="bg-gray-400 text-white font-bold py-3 px-8 rounded-lg transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                    Select Seats to Continue
                </button>
            </div>
        </div>
    </div>

    <!-- Custom Message Box -->
    <div id="custom-message-box-container"></div>
    
    <!-- Custom Modal -->
    <div id="custom-modal" class="modal-overlay hidden" onclick="closeModal()">
        <div id="modal-content" class="modal-content" onclick="event.stopPropagation()">
            <div class="p-5 border-b flex justify-between items-center no-print">
                <h3 id="modal-title" class="text-xl font-bold text-gray-800">Payment / Receipt</h3>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-800 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div id="modal-body" class="p-6">
                <!-- Content will be injected here -->
            </div>
            
            <div id="modal-footer" class="p-5 border-t flex justify-end space-x-3 no-print">
                <button id="modal-action-button" onclick="handleModalAction()" class="bg-green-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-green-700 transition hidden">
                    Confirm Payment
                </button>
                <button id="modal-close-button" onclick="closeModal()" class="bg-gray-200 text-gray-800 font-bold py-2 px-6 rounded-lg hover:bg-gray-300 transition">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        let currentStep = 1;
        let selectedSeats = [];
        let selectedPaymentMethod = '';
        const basePrice = parseFloat("{{ $trip->base_price ?? $trip->price }}");

        // Core Functions
        function calculateTotalPrice() {
            return selectedSeats.length * basePrice;
        }

        function updatePriceAndSummary() {
            const count = selectedSeats.length;
            const totalPrice = calculateTotalPrice();
            const formattedPrice = '$' + totalPrice.toFixed(2);
            const formattedFooterPrice = 'USD ' + totalPrice.toFixed(2);
            const seatsList = selectedSeats.sort((a, b) => a - b).join(', ');

            // Update all price/count displays
            document.getElementById('summary-count').textContent = count;
            document.getElementById('footer-count').textContent = count + (count === 1 ? ' seat' : ' seats');
            document.getElementById('summary-total-price').textContent = formattedPrice;
            document.getElementById('footer-price').textContent = formattedFooterPrice;
            document.getElementById('summary-seats').textContent = seatsList || 'None';
            document.getElementById('summary-seats-final').textContent = count + ' seat(s): ' + (seatsList || 'None');
            document.getElementById('summary-seats-final-payment').textContent = count + ' seat(s): ' + (seatsList || 'None');
            document.getElementById('payment-total-price-display').textContent = formattedPrice;
            document.getElementById('summary-total-price-final').textContent = formattedPrice;

            // Update seat selection status
            document.getElementById('selected-seats-count').textContent = count;
            document.getElementById('selected-seats-list').textContent = seatsList || 'None';

            // Update button state
            const nextButton = document.getElementById('next-step-button');
            const boardingSelected = document.querySelector('input[name="boarding_point"]:checked');
            const droppingSelected = document.querySelector('input[name="dropping_point"]:checked');
            const paymentSelected = document.querySelector('input[name="payment_method"]:checked');

            nextButton.classList.remove('bg-gray-400', 'bg-red-600', 'bg-green-600', 'hover:bg-red-700', 'hover:bg-green-700');
            nextButton.classList.add('bg-gray-400');
            nextButton.disabled = true;

            if (count > 0) {
                if (currentStep === 1) {
                    nextButton.textContent = 'Continue to Points';
                    nextButton.classList.add('bg-red-600', 'hover:bg-red-700');
                    nextButton.classList.remove('bg-gray-400');
                    nextButton.disabled = false;
                } else if (currentStep === 2) {
                    updateSummaryPoints();
                    if (boardingSelected && droppingSelected) {
                        nextButton.textContent = 'Fill Passenger Details';
                        nextButton.classList.add('bg-red-600', 'hover:bg-red-700');
                        nextButton.classList.remove('bg-gray-400');
                        nextButton.disabled = false;
                    } else {
                        nextButton.textContent = 'Select Points to Continue';
                    }
                } else if (currentStep === 3) {
                    if (validateStep3()) {
                        updateSummaryContact();
                        nextButton.textContent = 'Continue to Payment';
                        nextButton.classList.add('bg-red-600', 'hover:bg-red-700');
                        nextButton.classList.remove('bg-gray-400');
                        nextButton.disabled = false;
                    } else {
                        nextButton.textContent = 'Fill all details to Continue';
                    }
                } else if (currentStep === 4) {
                    if (paymentSelected) {
                        selectedPaymentMethod = paymentSelected.value;
                        document.getElementById('summary-payment-method').textContent = selectedPaymentMethod === 'ABA Pay' ? 'ABA Bank (QR)' : 'Credit/Debit Card';
                        
                        let isReadyForPayment = true;
                        if (selectedPaymentMethod === 'CREDIT' && !validateCreditCardForm()) {
                            isReadyForPayment = false;
                            nextButton.textContent = 'Fill Card Details to Pay';
                        }
                        
                        if (isReadyForPayment) {
                            nextButton.textContent = `Pay ${formattedPrice}`;
                            nextButton.classList.add('bg-green-600', 'hover:bg-green-700');
                            nextButton.classList.remove('bg-gray-400');
                            nextButton.disabled = false;
                        } else {
                             nextButton.disabled = true;
                        }
                    } else {
                        nextButton.textContent = 'Select Payment Method';
                    }
                }
            } else {
                nextButton.textContent = 'Select Seats to Continue';
            }
        }


        // Function to prepare booking data for database
        function prepareBookingData() {
            const boardingInput = document.querySelector('input[name="boarding_point"]:checked');
            const droppingInput = document.querySelector('input[name="dropping_point"]:checked');
            
            // Get passenger details from forms - FIXED STRUCTURE
            const passengers = [];
            const forms = document.getElementById('passenger-forms-container').querySelectorAll('div[data-seat-form]');
            
            forms.forEach((form, index) => {
                const seatNumber = form.getAttribute('data-seat-form');
                const nameInput = form.querySelector('input[name*="[full_name]"]');
                const ageInput = form.querySelector('input[name*="[age]"]');
                const nationalityInput = form.querySelector('input[name*="[nationality]"]');
                const genderInput = form.querySelector('input[name*="[gender]"]:checked');
                
                // Use array index format instead of seat number format
                passengers.push({
                    full_name: nameInput ? nameInput.value : '',
                    age: ageInput ? parseInt(ageInput.value) : 0,
                    nationality: nationalityInput ? nationalityInput.value : '',
                    gender: genderInput ? genderInput.value : ''
                });
            });

            return {
                schedule_id: {{ $trip->id }},
                seats: selectedSeats,
                boarding_point_id: boardingInput ? parseInt(boardingInput.value) : null,
                dropping_point_id: droppingInput ? parseInt(droppingInput.value) : null,
                contact_email: document.getElementById('contact-email').value,
                contact_phone: document.getElementById('contact-country-code').value + document.getElementById('contact-phone').value,
                total_price: calculateTotalPrice(),
                payment_method: selectedPaymentMethod,
                passengers: passengers // This should now be in correct format
            };
        }

            // Update summary points with TripPoint data
            function updateSummaryPoints() {
                const boardingInput = document.querySelector('input[name="boarding_point"]:checked');
                const droppingInput = document.querySelector('input[name="dropping_point"]:checked');

                document.getElementById('summary-boarding-name').textContent = boardingInput ? 
                    `${boardingInput.dataset.time} ${boardingInput.dataset.name} - ${boardingInput.dataset.address}` : 'Not selected';
                
                document.getElementById('summary-dropping-name').textContent = droppingInput ? 
                    `${droppingInput.dataset.time} ${droppingInput.dataset.name} - ${droppingInput.dataset.address}` : 'Not selected';
            }

        

        function toggleSeat(seatElement, seatNumber) {
            if (seatElement.classList.contains('seat-booked')) {
                displayCustomMessage('Seat ' + seatNumber + ' is already booked and cannot be selected.', 'error');
                return;
            }

            if (selectedSeats.includes(seatNumber)) {
                // Deselect
                selectedSeats = selectedSeats.filter(s => s !== seatNumber);
                seatElement.classList.remove('seat-selected');
                seatElement.classList.add('seat-available');
            } else {
                // Select
                selectedSeats.push(seatNumber);
                seatElement.classList.add('seat-selected');
                seatElement.classList.remove('seat-available');
            }
            updatePassengerForms(selectedSeats.length);
            updatePriceAndSummary();
        }
        
        function validateStep3() {
            const email = document.getElementById('contact-email').value;
            const phone = document.getElementById('contact-phone').value;
            
            if (!email || !phone) {
                return false;
            }

            const forms = document.getElementById('passenger-forms-container').querySelectorAll('div[data-seat-form]');
            let allPassengersValid = true;
            
            forms.forEach((form, index) => {
                const nameInput = form.querySelector('input[name="passengers[' + index + '][full_name]"]');
                const ageInput = form.querySelector('input[name="passengers[' + index + '][age]"]');
                const nationalityInput = form.querySelector('input[name="passengers[' + index + '][nationality]"]');
                const genderInput = form.querySelector('input[name="passengers[' + index + '][gender]"]:checked');
                
                if (!nameInput?.value || !ageInput?.value || !nationalityInput?.value || !genderInput) {
                    allPassengersValid = false;
                }
            });
            
            return allPassengersValid;
        }

        function updateSummaryPoints() {
            const boardingInput = document.querySelector('input[name="boarding_point"]:checked');
            const droppingInput = document.querySelector('input[name="dropping_point"]:checked');

            document.getElementById('summary-boarding-name').textContent = boardingInput ? `${boardingInput.dataset.time} ${boardingInput.dataset.name}` : 'Not selected';
            document.getElementById('summary-dropping-name').textContent = droppingInput ? `${droppingInput.dataset.time} ${droppingInput.dataset.name}` : 'Not selected';
        }
        
        function updateSummaryContact() {
            document.getElementById('summary-contact-email').textContent = document.getElementById('contact-email').value;
        }

        function updatePassengerForms(count) {  // This should match what you're calling
    const container = document.getElementById('passenger-forms-container');
    container.innerHTML = '';
    
    if (count === 0) {
        container.innerHTML = '<p class="text-gray-500 p-4 border rounded-lg">Please select seats in Step 1 to fill passenger details.</p>';
        return;
    }

    const sortedSeats = selectedSeats.sort((a, b) => a - b);
    
    for (let i = 0; i < count; i++) {
        const seatNumber = sortedSeats[i];
        const formHtml = `
            <div class="mb-6 p-4 booking-card border" data-seat-form="${seatNumber}">
                <details open>
                    <summary class="font-bold text-lg cursor-pointer flex justify-between items-center">
                        Passenger ${i + 1} Details - Seat <span class="text-red-600">${seatNumber}</span>
                    </summary>
                    <div class="mt-4 space-y-3">
                        <label class="block">
                            <span class="text-sm font-medium text-gray-700">Full Name (Required)</span>
                            <input type="text" name="passengers[${i}][full_name]" placeholder="Full Name" class="w-full border-gray-300 rounded-lg p-3 mt-1" required oninput="updatePriceAndSummary()">
                        </label>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <label class="block">
                                <span class="text-sm font-medium text-gray-700">Age</span>
                                <input type="number" name="passengers[${i}][age]" placeholder="Age" min="1" max="120" class="w-full border-gray-300 rounded-lg p-3 mt-1" required oninput="updatePriceAndSummary()">
                            </label>
                            
                            <label class="block">
                                <span class="text-sm font-medium text-gray-700">Nationality</span>
                                <input type="text" name="passengers[${i}][nationality]" placeholder="Nationality" class="w-full border-gray-300 rounded-lg p-3 mt-1" required oninput="updatePriceAndSummary()">
                            </label>
                        </div>
                        
                        <div>
                            <span class="text-sm font-medium text-gray-700 block mb-1">Gender (Required)</span>
                            <div class="flex space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="passengers[${i}][gender]" value="Male" class="text-red-500 focus:ring-red-500" required onchange="updatePriceAndSummary()">
                                    <span class="ml-2">Male</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="passengers[${i}][gender]" value="Female" class="text-red-500 focus:ring-red-500" required onchange="updatePriceAndSummary()">
                                    <span class="ml-2">Female</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </details>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', formHtml);
    }
    
    
}

        function goToNextStep() {
            if (currentStep === 1) {
                showStep(2);
            } else if (currentStep === 2) {
                showStep(3);
            } else if (currentStep === 3) {
                if (validateStep3()) {
                    updateSummaryContact();
                    showStep(4);
                } else {
                    displayCustomMessage('Please ensure all required passenger and contact fields are filled.', 'error');
                }
            } else if (currentStep === 4) {
                handlePaymentAction();
            }
        }
        
        function showStep(step) {
            if (selectedSeats.length === 0 && step > 1) {
                displayCustomMessage('Please select at least one seat first.', 'warning');
                return;
            }
            if (step >= 3) {
                const boardingSelected = document.querySelector('input[name="boarding_point"]:checked');
                const droppingSelected = document.querySelector('input[name="dropping_point"]:checked');
                if (!boardingSelected || !droppingSelected) {
                    displayCustomMessage('Please select both boarding and dropping points before proceeding.', 'warning');
                    if (step > 2) showStep(2);
                    return;
                }
            }
            if (step >= 4 && !validateStep3()) {
                displayCustomMessage('Please fill out all contact and passenger details first.', 'warning');
                if (step > 3) showStep(3);
                return;
            }

            currentStep = step;
            const steps = [1, 2, 3, 4];
            
            steps.forEach(s => {
                const stepEl = document.getElementById(`step-${s}`);
                const indicatorEl = document.getElementById(`step-${s}-indicator`);
                const summaryEl = document.getElementById(`step-${s}-summary`);

                if (stepEl) stepEl.classList.add('hidden');
                if (indicatorEl) {
                    indicatorEl.classList.remove('active', 'text-gray-800');
                    indicatorEl.classList.add('text-gray-400');
                    if (s < currentStep) { indicatorEl.classList.add('text-gray-800'); }
                }
                if (summaryEl) summaryEl.classList.add('hidden');
            });

            document.getElementById(`step-${step}`).classList.remove('hidden');
            document.getElementById(`step-${step}-indicator`).classList.add('active', 'text-gray-800');
            
            document.getElementById('seat-map-card').classList.toggle('hidden', step !== 1);
            document.getElementById('booking-summary-card').classList.toggle('hidden', step === 1);
            if (step !== 1) {
                document.getElementById(`step-${step}-summary`).classList.remove('hidden');
            }
            
            if (step === 4) {
                const creditCardForm = document.getElementById('credit-card-form');
                const paymentRadio = document.querySelector('input[name="payment_method"]:checked');
                
                if (paymentRadio?.value === 'CREDIT') {
                    creditCardForm.classList.remove('hidden');
                    selectedPaymentMethod = 'CREDIT';
                } else {
                    creditCardForm.classList.add('hidden');
                    selectedPaymentMethod = paymentRadio?.value || '';
                }
            }

            updatePriceAndSummary();
        }

        // Message and Modal Functions
        function displayCustomMessage(message, type = 'info') {
            const container = document.getElementById('custom-message-box-container');
            const color = type === 'error' ? 'bg-red-600' : type === 'warning' ? 'bg-yellow-600' : 'bg-blue-600';
            
            const messageBox = document.createElement('div');
            messageBox.className = `fixed bottom-20 right-4 p-4 rounded-lg shadow-xl text-white font-bold transition-opacity duration-300 ${color} z-50`;
            messageBox.textContent = message;
            
            container.appendChild(messageBox);
            
            setTimeout(() => {
                messageBox.classList.add('opacity-0');
                messageBox.addEventListener('transitionend', () => messageBox.remove());
            }, 3000);
        }

        const customModal = document.getElementById('custom-modal');
        const modalContent = document.getElementById('modal-content');
        const modalTitle = document.getElementById('modal-title');
        const modalBody = document.getElementById('modal-body');
        const modalActionButton = document.getElementById('modal-action-button');
        const modalCloseButton = document.getElementById('modal-close-button');

        function openModal() {
            customModal.classList.remove('hidden');
            setTimeout(() => { modalContent.classList.add('show'); }, 10);
        }

        function closeModal() {
            modalContent.classList.remove('show');
            modalCloseButton.textContent = 'Close';
            modalCloseButton.onclick = closeModal;
            setTimeout(() => { customModal.classList.add('hidden'); }, 200);
        }
        
        // Payment Handling
       function handlePaymentAction() {
        console.log('handlePaymentAction - selectedPaymentMethod:', selectedPaymentMethod);
        
        if (selectedPaymentMethod === 'ABA Pay') {
            showAbaQrModal();
        } else if (selectedPaymentMethod === 'Credit/Debit Card') {
            if (validateCreditCardForm()) {
                processCreditCardPayment();
            } else {
                displayCustomMessage('Please check all credit card details.', 'error');
            }
        } else {
            displayCustomMessage('Please select a payment method.', 'warning');
        }
    }   
        
        function showAbaQrModal() {
            modalTitle.textContent = 'ABA Bank QR Payment';
            modalBody.innerHTML = `
                <div class="text-center space-y-4">
                    <p class="text-gray-700 font-semibold text-lg">Scan the QR code below using your ABA Mobile App.</p>
                    <p class="text-red-600 font-extrabold text-3xl">${document.getElementById('summary-total-price').textContent}</p>
                    <img src="{{ asset('image/qrcode.png') }}" 
                         class="mx-auto w-64 h-64 border-4 border-gray-100 rounded-lg">
                    <p class="text-sm text-gray-500">Please complete the transaction in your banking app. Click 'Confirm Payment' once done.</p>
                </div>
            `;
            
            modalActionButton.textContent = 'Confirm Payment Received';
            modalActionButton.onclick = () => {
                closeModal();
                displayCustomMessage('Processing ABA payment confirmation...', 'info');
                setTimeout(() => {
                    showReceiptModal();
                }, 1000);
            };
            modalActionButton.classList.remove('hidden');
            openModal();
        }

        function validateCreditCardForm() {
            const cardNumber = document.getElementById('card-number').value.replace(/\s/g, '');
            const expiry = document.getElementById('card-expiry').value;
            const cvc = document.getElementById('card-cvc').value;
            const name = document.getElementById('card-name').value;
            
            const isCardValid = cardNumber.length === 16;
            const isCVCValid = cvc.length >= 3 && cvc.length <= 4 && /^\d+$/.test(cvc);
            const isExpiryValid = /^\d{2}\/\d{2}$/.test(expiry);
            const isNameValid = name.trim() !== '';

            return isCardValid && isExpiryValid && isCVCValid && isNameValid;
        }

      // Updated Payment Processing with Database Integration
async function processCreditCardPayment() {
    const nextButton = document.getElementById('next-step-button');
    nextButton.disabled = true;
    nextButton.textContent = 'Processing...';

    try {
        // Prepare booking data for database
        const bookingData = prepareBookingData();
        
        // Send to backend
        const response = await fetch('{{ route("bookings.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(bookingData)
        });

        const result = await response.json();

        if (result.success) {
            displayCustomMessage('Payment successful! Booking confirmed.', 'success');
            showReceiptModal(result.booking_reference, result.booking_id);
            
            nextButton.disabled = true;
            nextButton.textContent = 'Booking Complete!';
            nextButton.classList.remove('bg-green-600', 'hover:bg-green-700');
            nextButton.classList.add('bg-gray-400');
        } else {
            throw new Error(result.message);
        }

    } catch (error) {
        console.error('Booking error:', error);
        displayCustomMessage('Booking failed: ' + error.message, 'error');
        
        nextButton.disabled = false;
        nextButton.textContent = `Pay ${document.getElementById('summary-total-price').textContent}`;
        nextButton.classList.add('bg-green-600', 'hover:bg-green-700');
    }
}

// Updated ABA Payment with Database Integration
function showAbaQrModal() {
    modalTitle.textContent = 'ABA Bank QR Payment';
    modalBody.innerHTML = `
        <div class="text-center space-y-4">
            <p class="text-gray-700 font-semibold text-lg">Scan the QR code below using your ABA Mobile App.</p>
            <p class="text-red-600 font-extrabold text-3xl">${document.getElementById('summary-total-price').textContent}</p>
            <img src="{{ asset('image/qrcode.png') }}" 
                 class="mx-auto w-64 h-64 border-4 border-gray-100 rounded-lg">
            <p class="text-sm text-gray-500">Please complete the transaction in your banking app. Click 'Confirm Payment' once done.</p>
        </div>
    `;
    
    modalActionButton.textContent = 'Confirm Payment Received';
    modalActionButton.onclick = async () => {
        modalActionButton.disabled = true;
        modalActionButton.textContent = 'Processing...';
        
        try {
            const bookingData = prepareBookingData();
            
            const response = await fetch('{{ route("bookings.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(bookingData)
            });

            const result = await response.json();

            if (result.success) {
                closeModal();
                displayCustomMessage('ABA payment confirmed! Booking saved.', 'success');
                showReceiptModal(result.booking_reference, result.booking_id);
                const message = encodeURIComponent('Booking confirmed! ABA payment received.');
                window.location.href = `/user/dashboard?msg=${message}`;
            } else {
                throw new Error(result.message);
            }

        } catch (error) {
            console.error('Booking error:', error);
            displayCustomMessage('Booking failed: ' + error.message, 'error');
            modalActionButton.disabled = false;
            modalActionButton.textContent = 'Confirm Payment Received';
        }
    };
    modalActionButton.classList.remove('hidden');
    openModal();
}

// Updated receipt modal to show actual booking reference
function showReceiptModal(bookingReference = null, bookingId = null) {
    const bookingData = getBookingDataForReceipt();
    
    // Use actual booking reference from database if available
    if (bookingReference) {
        bookingData.bookingId = bookingReference;
    }
    if (bookingId) {
        bookingData.databaseId = bookingId;
    }

    modalTitle.textContent = 'Booking Confirmed! (Receipt)';
    modalBody.innerHTML = generateReceiptHTML(bookingData);

    modalActionButton.classList.add('hidden');
    
    modalCloseButton.textContent = 'Print Receipt (A5)';
    modalCloseButton.classList.remove('text-gray-500', 'hover:text-gray-800');
    modalCloseButton.classList.add('bg-red-600', 'hover:bg-red-700', 'text-white', 'px-6', 'py-2', 'rounded-lg', 'font-bold', 'no-print');
    modalCloseButton.onclick = () => {
        window.print(); 
        modalCloseButton.textContent = 'Close';
        modalCloseButton.classList.remove('bg-red-600', 'hover:bg-red-700', 'text-white', 'px-6', 'py-2', 'rounded-lg', 'font-bold', 'no-print');
        modalCloseButton.classList.add('text-gray-500', 'hover:text-gray-800');
        modalCloseButton.onclick = closeModal;
    };
    
    openModal();
}

// Updated receipt generation to show database info
function generateReceiptHTML(data) {
    let passengerList = '';
    data.passengers.forEach(p => {
        passengerList += `
            <p class="flex justify-between border-b border-dashed border-gray-300 py-1 text-sm">
                <span>Passenger: ${p.name} (${p.gender})</span>
                <span class="font-bold">Seat: ${p.seat}</span>
            </p>
        `;
    });
    
    return `
        <div id="receipt-print-area" class="w-full bg-white text-gray-900 mx-auto p-4 sm:p-6 lg:p-8">
            <div class="text-center mb-6">
                <h1 class="text-3xl font-extrabold text-red-600 mb-1">🚌 BusFinder 🇰🇭</h1>
                <p class="text-xs text-gray-500">E-Ticket / Booking Confirmation</p>
                <p class="text-sm font-semibold mt-4">Booking ID: <span class="text-red-700 font-extrabold">${data.bookingId}</span></p>
                ${data.databaseId ? `<p class="text-xs text-gray-500">Database ID: ${data.databaseId}</p>` : ''}
            </div>

            <div class="border-b border-gray-300 pb-4 mb-4">
                <h2 class="text-lg font-bold mb-2 border-b pb-1">Trip Details</h2>
                <div class="space-y-1 text-sm">
                    <p class="flex justify-between"><span>Operator:</span> <span class="font-semibold">${data.tripName} (${data.busType})</span></p>
                    <p class="flex justify-between"><span>Route:</span> <span class="font-semibold">${data.route}</span></p>
                    <p class="flex justify-between"><span>Date:</span> <span class="font-semibold">${data.date}</span></p>
                    <p class="flex justify-between"><span>Time:</span> <span class="font-semibold">${data.departure} &rarr; ${data.arrival}</span></p>
                </div>
            </div>
            
            <div class="border-b border-gray-300 pb-4 mb-4">
                <h2 class="text-lg font-bold mb-2 border-b pb-1">Ticket & Passenger Info (${data.seatCount} Seat(s))</h2>
                <div class="space-y-1 text-sm">
                    ${passengerList}
                    <p class="pt-2"><strong>Seats Booked:</strong> <span class="font-mono text-lg text-red-600">${data.seats}</span></p>
                </div>
            </div>
            
            <div class="border-b border-gray-300 pb-4 mb-4">
                <h2 class="text-lg font-bold mb-2 border-b pb-1">Contact & Stops</h2>
                <div class="space-y-1 text-sm">
                    <p class="flex justify-between"><span>Boarding:</span> <span class="font-semibold text-right">${data.boardingPoint}</span></p>
                    <p class="flex justify-between"><span>Dropping:</span> <span class="font-semibold text-right">${data.droppingPoint}</span></p>
                    <p class="flex justify-between pt-2"><span>Email:</span> <span class="font-semibold">${data.contactEmail}</span></p>
                    <p class="flex justify-between"><span>Phone:</span> <span class="font-semibold">${data.contactPhone}</span></p>
                </div>
            </div>

            <div class="mt-4 p-4 bg-gray-100 rounded-lg">
                <p class="flex justify-between text-base font-medium"><span>Price per seat:</span> <span class="font-semibold">$${basePrice.toFixed(2)}</span></p>
                <p class="flex justify-between text-base font-medium"><span>Payment Method:</span> <span class="font-semibold">${data.paymentMethod}</span></p>
                <div class="border-t border-gray-300 mt-3 pt-3 flex justify-between">
                     <span class="text-xl font-extrabold text-gray-800">TOTAL PAID:</span>
                     <span class="text-2xl font-extrabold text-red-600">$${data.totalPrice}</span>
                </div>
            </div>
            
            <div class="mt-6 text-center text-xs text-gray-500">
                <p>Thank you for booking. Please arrive at the boarding point 30 minutes before departure.</p>
                <p class="mt-1">Generated: ${new Date().toLocaleString()}</p>
                <p class="mt-2 text-green-600 font-semibold">✅ Booking saved to database</p>
            </div>
        </div>
    `;
}

        function showReceiptModal() {
            const bookingData = getBookingDataForReceipt();
            
            modalTitle.textContent = 'Booking Confirmed! (Receipt)';
            modalBody.innerHTML = generateReceiptHTML(bookingData);

            document.getElementById('modal-header')?.classList.remove('no-print');
            
            modalActionButton.classList.add('hidden');
            
            modalCloseButton.textContent = 'Print Receipt (A5)';
            modalCloseButton.classList.remove('text-gray-500', 'hover:text-gray-800');
            modalCloseButton.classList.add('bg-red-600', 'hover:bg-red-700', 'text-white', 'px-6', 'py-2', 'rounded-lg', 'font-bold', 'no-print');
            modalCloseButton.onclick = () => {
                window.print(); 
                modalCloseButton.textContent = 'Close';
                modalCloseButton.classList.remove('bg-red-600', 'hover:bg-red-700', 'text-white', 'px-6', 'py-2', 'rounded-lg', 'font-bold', 'no-print');
                modalCloseButton.classList.add('text-gray-500', 'hover:text-gray-800');
                modalCloseButton.onclick = closeModal;
            };
            
            openModal();
        }

       function getBookingDataForReceipt() {
    const boardingInput = document.querySelector('input[name="boarding_point"]:checked');
    const droppingInput = document.querySelector('input[name="dropping_point"]:checked');

    return {
        bookingId: 'BK' + Math.floor(Math.random() * 900000 + 100000),
        date: '{{ \Carbon\Carbon::parse($searchDate)->format('D, d M Y') }}',
        tripName: '{{ $trip->operator_name }}',
        busType: '{{ $trip->bus_type }}',
        route: '{{ $trip->origin->city_name }} to {{ $trip->destination->city_name }}',
        departure: '{{ $trip->departure_time->format('H:i') }}',
        arrival: '{{ $trip->arrival_time->format('H:i') }}',
        seats: selectedSeats.join(', '),
        seatCount: selectedSeats.length,
        totalPrice: calculateTotalPrice().toFixed(2),
        contactEmail: document.getElementById('contact-email').value,
        contactPhone: document.getElementById('contact-country-code').value + document.getElementById('contact-phone').value,
        paymentMethod: selectedPaymentMethod, // ← CHANGED: Now uses the actual value ('ABA Pay' or 'Credit/Debit Card')
        boardingPoint: boardingInput ? boardingInput.closest('label').querySelector('p:not(.text-xs)').textContent.trim() : 'N/A',
        droppingPoint: droppingInput ? droppingInput.closest('label').querySelector('p:not(.text-xs)').textContent.trim() : 'N/A',
        passengers: getPassengerDetails(),
    };
}
        
        function getPassengerDetails() {
            const passengerDetails = [];
            const forms = document.getElementById('passenger-forms-container').querySelectorAll('div[data-seat-form]');
            forms.forEach(form => {
                const seatNumber = form.getAttribute('data-seat-form');
                const nameInput = form.querySelector('input[name*="[name]"]');
                const genderInputChecked = form.querySelector('input[name*="[gender]"]:checked');
                
                passengerDetails.push({
                    seat: seatNumber,
                    name: nameInput ? nameInput.value : 'N/A',
                    gender: genderInputChecked ? genderInputChecked.value : 'N/A'
                });
            });
            return passengerDetails;
        }

        function generateReceiptHTML(data) {
            let passengerList = '';
            data.passengers.forEach(p => {
                passengerList += `
                    <p class="flex justify-between border-b border-dashed border-gray-300 py-1 text-sm">
                        <span>Passenger: ${p.name} (${p.gender})</span>
                        <span class="font-bold">Seat: ${p.seat}</span>
                    </p>
                `;
            });
            
            return `
                <div id="receipt-print-area" class="w-full bg-white text-gray-900 mx-auto p-4 sm:p-6 lg:p-8">
                    <div class="text-center mb-6">
                        <h1 class="text-3xl font-extrabold text-red-600 mb-1">🚌 BusFinder 🇰🇭</h1>
                        <p class="text-xs text-gray-500">E-Ticket / Booking Confirmation</p>
                        <p class="text-sm font-semibold mt-4">Booking ID: <span class="text-red-700 font-extrabold">${data.bookingId}</span></p>
                    </div>

                    <div class="border-b border-gray-300 pb-4 mb-4">
                        <h2 class="text-lg font-bold mb-2 border-b pb-1">Trip Details</h2>
                        <div class="space-y-1 text-sm">
                            <p class="flex justify-between"><span>Operator:</span> <span class="font-semibold">${data.tripName} (${data.busType})</span></p>
                            <p class="flex justify-between"><span>Route:</span> <span class="font-semibold">${data.route}</span></p>
                            <p class="flex justify-between"><span>Date:</span> <span class="font-semibold">${data.date}</span></p>
                            <p class="flex justify-between"><span>Time:</span> <span class="font-semibold">${data.departure} &rarr; ${data.arrival}</span></p>
                        </div>
                    </div>
                    
                    <div class="border-b border-gray-300 pb-4 mb-4">
                        <h2 class="text-lg font-bold mb-2 border-b pb-1">Ticket & Passenger Info (${data.seatCount} Seat(s))</h2>
                        <div class="space-y-1 text-sm">
                            ${passengerList}
                            <p class="pt-2"><strong>Seats Booked:</strong> <span class="font-mono text-lg text-red-600">${data.seats}</span></p>
                        </div>
                    </div>
                    
                    <div class="border-b border-gray-300 pb-4 mb-4">
                        <h2 class="text-lg font-bold mb-2 border-b pb-1">Contact & Stops</h2>
                        <div class="space-y-1 text-sm">
                            <p class="flex justify-between"><span>Boarding:</span> <span class="font-semibold text-right">${data.boardingPoint}</span></p>
                            <p class="flex justify-between"><span>Dropping:</span> <span class="font-semibold text-right">${data.droppingPoint}</span></p>
                            <p class="flex justify-between pt-2"><span>Email:</span> <span class="font-semibold">${data.contactEmail}</span></p>
                            <p class="flex justify-between"><span>Phone:</span> <span class="font-semibold">${data.contactPhone}</span></p>
                        </div>
                    </div>

                    <div class="mt-4 p-4 bg-gray-100 rounded-lg">
                        <p class="flex justify-between text-base font-medium"><span>Price per seat:</span> <span class="font-semibold">$${basePrice.toFixed(2)}</span></p>
                        <p class="flex justify-between text-base font-medium"><span>Payment Method:</span> <span class="font-semibold">${data.paymentMethod}</span></p>
                        <div class="border-t border-gray-300 mt-3 pt-3 flex justify-between">
                             <span class="text-xl font-extrabold text-gray-800">TOTAL PAID:</span>
                             <span class="text-2xl font-extrabold text-red-600">$${data.totalPrice}</span>
                        </div>
                    </div>
                    
                    <div class="mt-6 text-center text-xs text-gray-500">
                        <p>Thank you for booking. Please arrive at the boarding point 30 minutes before departure.</p>
                        <p class="mt-1">Generated: ${new Date().toLocaleString()}</p>
                    </div>
                </div>
            `;
        }
        
       // Event Listeners for payment methods
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                const creditCardForm = document.getElementById('credit-card-form');
                
                // Update selectedPaymentMethod to match the radio value
                selectedPaymentMethod = e.target.value;
                
                if (selectedPaymentMethod === 'Credit/Debit Card') {
                    creditCardForm.classList.remove('hidden');
                } else {
                    creditCardForm.classList.add('hidden');
                }
                updatePriceAndSummary();
            });
        });

        const creditCardInputs = document.getElementById('credit-card-form')?.querySelectorAll('input');
        if (creditCardInputs) {
             creditCardInputs.forEach(input => {
                input.addEventListener('input', updatePriceAndSummary);
            });
        }
        
        document.getElementById('card-number')?.addEventListener('input', function (e) {
            e.target.value = e.target.value.replace(/\D/g, '').replace(/(\d{4})(?=\d)/g, '$1 ');
        });

        document.getElementById('card-expiry')?.addEventListener('input', function (e) {
            let input = e.target.value.replace(/\D/g, '');
            if (input.length > 2) {
                input = input.substring(0, 2) + '/' + input.substring(2, 4);
            }
            e.target.value = input;
        });

        document.addEventListener('DOMContentLoaded', () => {
            // Initialize seat selection
            document.querySelectorAll('.seat-available').forEach(seat => {
                const seatNumber = seat.getAttribute('data-seat');
                seat.onclick = () => toggleSeat(seat, parseInt(seatNumber));
            });
            
            document.querySelectorAll('input[name="boarding_point"], input[name="dropping_point"]').forEach(input => {
                input.addEventListener('change', updatePriceAndSummary);
            });
            
            document.getElementById('contact-email')?.addEventListener('input', updatePriceAndSummary);
            document.getElementById('contact-phone')?.addEventListener('input', updatePriceAndSummary);
            document.getElementById('passenger-forms-container')?.addEventListener('input', updatePriceAndSummary);

            updatePriceAndSummary();
        });



    </script>
</body>
</html>