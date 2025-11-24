

<div id="seat-modal-{{ $tripId }}" 
     class="fixed inset-0 bg-gray-900 bg-opacity-80 hidden items-center justify-center z-50 overflow-y-auto" 
     style="display: none;">

    <div class="bg-white rounded-xl shadow-2xl w-full max-w-5xl p-0 my-8">
        
        <div class="p-6 border-b border-gray-200 sticky top-0 bg-white z-10 rounded-t-xl modal-header">
            <div class="flex justify-between items-center mb-4">
                <h2 id="modal-route-title" class="text-xl md:text-2xl font-bold text-gray-800">{{ $routeName }}</h2>
                <button onclick="closeSeatSelectionModal('{{ $tripId }}')" class="text-gray-500 hover:text-gray-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div id="modal-message-area-{{ $tripId }}" class="mt-2 h-0 overflow-hidden transition-all duration-300">
                </div>
            
            <div class="flex justify-between max-w-xl mx-auto text-center font-semibold text-sm">
                <div id="step-1-indicator-{{ $tripId }}" class="step-indicator text-red-600 border-b-2 border-red-600 pb-1 cursor-pointer" data-step="1" onclick="changeStep('{{ $tripId }}', 1)">1. Select seats</div>
                <div id="step-2-indicator-{{ $tripId }}" class="step-indicator text-gray-400 pb-1 cursor-pointer" data-step="2" onclick="changeStep('{{ $tripId }}', 2)">2. Select points</div>
                <div id="step-3-indicator-{{ $tripId }}" class="step-indicator text-gray-400 pb-1 cursor-pointer" data-step="3" onclick="changeStep('{{ $tripId }}', 3)">3. Passenger & Pay</div>
            </div>
        </div>

        <div id="modal-content-area-{{ $tripId }}">
            
            <div id="step-1-{{ $tripId }}" class="modal-step flex flex-col lg:flex-row p-0">
                <div class="lg:w-7/12 p-6 md:p-8 space-y-6">
                    <div id="modal-trip-summary" class="bg-white rounded-lg">
                        <div class="flex justify-between items-center pb-2">
                            <h3 id="modal-operator" class="text-xl font-bold text-gray-800">{{ $operator }}</h3>
                            <div class="flex items-center text-green-600 font-bold text-sm bg-green-100 px-3 py-1 rounded-full">
                                <span id="modal-available-seats">{{ $availableSeats }}</span> seats
                            </div>
                        </div>

                        <p class="text-gray-600 text-sm mb-4">
                            <span id="modal-times">{{ $times }}</span> &bull; <span id="modal-date">{{ $date }}</span>
                        </p>
                        
                        <div class="grid grid-cols-3 gap-2">
                            <img src="https://placehold.co/200x120/80e0a0/white?text=Luxury+Bus+Exterior" onerror="this.onerror=null;this.src='https://placehold.co/200x120/80e0a0/white?text=Exterior';" class="w-full h-auto rounded-lg object-cover shadow" alt="Bus Exterior">
                            <img src="https://placehold.co/200x120/d080e0/white?text=Luxury+Interior" onerror="this.onerror=null;this.src='https://placehold.co/200x120/d080e0/white?text=Interior';" class="w-full h-auto rounded-lg object-cover shadow" alt="Bus Interior">
                            <img src="https://placehold.co/200x120/80d0e0/white?text=Comfort+Seats" onerror="this.onerror=null;this.src='https://placehold.co/200x120/80d0e0/white?text=Seats';" class="w-full h-auto rounded-lg object-cover shadow" alt="Comfort Seats">
                        </div>
                    </div>

                    <div class="mt-6 border-t pt-4">
                        <div class="flex space-x-6 border-b mb-4 text-sm font-semibold text-gray-600">
                            <span class="text-red-600 border-b-2 border-red-600 pb-2 cursor-pointer">Why book this bus?</span>
                            <span class="hover:text-red-600 cursor-pointer pb-2">Dropping point</span>
                            <span class="hover:text-red-600 cursor-pointer pb-2">Amenities</span>
                        </div>
                        <div class="text-gray-500 text-sm">
                            <h4 class="font-bold text-lg mb-2">Why book this bus?</h4>
                            <p>This is a VIP Sleeper Class bus with free Wi-Fi, blanket, and water. Operated by a trusted partner with a high safety rating.</p>
                        </div>
                    </div>
                </div>

                <div class="lg:w-5/12 bg-gray-50 p-6 md:p-8 border-l border-gray-200 flex flex-col justify-between">
                    
                    <div>
                        <div class="flex justify-center mb-8">
                            <div class="p-4 bg-white rounded-lg shadow-inner">
                                <div class="flex justify-center mb-4">
                                    <svg class="w-6 h-6 text-gray-500" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/><path d="M12 2v2m0 16v2m-6-8h-2m16 0h-2m-6 0h-2m4 0h2" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </div>
                                <div id="seat-map-{{ $tripId }}" class="flex flex-col items-center">
                                    <p class="text-gray-400 text-xs py-12">Seat map placeholder for Trip {{ $tripId }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-center space-x-4 mb-8 text-xs font-semibold">
                            <div class="flex items-center">
                                <span class="bg-gray-200 w-3 h-3 mr-1 rounded-sm"></span> Available
                            </div>
                            <div class="flex items-center">
                                <span class="bg-red-500 w-3 h-3 mr-1 rounded-sm"></span> Booked
                            </div>
                            <div class="flex items-center">
                                <span class="bg-green-500 w-3 h-3 mr-1 rounded-sm"></span> Selected
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 p-4 border rounded-lg bg-white shadow-sm">
                        <p class="text-sm text-gray-600">Selected Seats: <span id="selected-seats-list-{{ $tripId }}" class="font-semibold text-gray-800">None</span></p>
                        <p class="text-sm text-gray-600">Total Tickets: <span id="selected-seats-count-{{ $tripId }}" class="font-semibold text-gray-800">0</span></p>
                        <p class="text-xl font-bold mt-3 text-red-600">Total Price: <span id="total-price-{{ $tripId }}">${{ number_format(0, 2) }}</span></p>
                        
                        <button onclick="changeStep('{{ $tripId }}', 2)" id="next-step-1-{{ $tripId }}" disabled
                            class="w-full mt-4 bg-gray-400 text-white font-bold py-3 rounded-lg transition duration-150 shadow-md text-lg disabled:opacity-50 disabled:cursor-not-allowed">
                            Continue to Points (0 Seats Selected)
                        </button>
                    </div>
                </div>
            </div>

            <div id="step-2-{{ $tripId }}" class="modal-step p-6 md:p-8 hidden">
                <h3 class="text-xl font-bold">Step 2: Select Boarding & Dropping Points</h3>
                <p class="mt-4 text-gray-600">Content for selecting points...</p>
                <div class="flex justify-between mt-8">
                    <button onclick="changeStep('{{ $tripId }}', 1)" class="bg-gray-500 text-white py-2 px-4 rounded">Back</button>
                    <button onclick="changeStep('{{ $tripId }}', 3)" class="bg-red-600 text-white py-2 px-4 rounded">Continue to Passenger Details</button>
                </div>
            </div>
            
            <div id="step-3-{{ $tripId }}" class="modal-step p-6 md:p-8 hidden">
                <h3 class="text-xl font-bold">Step 3: Passenger Details & Payment</h3>
                <p class="mt-4 text-gray-600">Content for passenger forms and payment gateway...</p>
                <div class="flex justify-start mt-8">
                    <button onclick="changeStep('{{ $tripId }}', 2)" class="bg-gray-500 text-white py-2 px-4 rounded">Back</button>
                    <button class="bg-green-600 text-white font-bold py-3 px-6 rounded-lg ml-auto">Confirm Booking</button>
                </div>
            </div>

        </div>

    </div>
</div>