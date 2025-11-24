<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // 1. Locations Table (Phnom Penh, Siem Reap, etc.)
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('city_name')->unique();
            $table->timestamps();
        });

        // 2. Trips Table (The bus journey itself)
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('origin_id')->constrained('locations');
            $table->foreignId('destination_id')->constrained('locations');
            $table->string('operator_name');
            $table->string('bus_type'); // VIP Sleeper, Express Bus
            $table->decimal('price', 8, 2);
            $table->integer('capacity');
            $table->time('departure_time');
            $table->time('arrival_time'); // Estimated arrival
            $table->timestamps();
        });

        // 3. Trip Schedule Table (For specific dates, linking to seats/availability)
        Schema::create('trip_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained()->onDelete('cascade');
            $table->date('journey_date');
            $table->integer('available_seats');
            $table->unique(['trip_id', 'journey_date']);
            $table->timestamps();
        });
        
        // 4. Boarding and Dropping Points
        Schema::create('trip_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['boarding', 'dropping']);
            $table->time('time');
            $table->string('name');
            $table->string('address');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->timestamps();
        });

        // 5. Bookings Table
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Optional if anonymous booking
            $table->foreignId('schedule_id')->constrained('trip_schedules');
            $table->string('booking_reference')->unique();
            $table->string('contact_email');
            $table->string('contact_phone');
            $table->decimal('total_price', 8, 2);
            $table->enum('payment_method', ['ABA Pay', 'Credit/Debit Card', 'Cash']);
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'paid']);
            $table->timestamps();
        });

        // 6. Passengers (Details for each booked seat)
        Schema::create('passengers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->integer('seat_number');
            $table->string('full_name');
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->integer('age');
            $table->string('nationality');
            $table->timestamps();

            $table->unique(['booking_id', 'seat_number']);
        });

        // 7. Booked Seats (To track seat status on a specific schedule)
        Schema::create('booked_seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('trip_schedules')->onDelete('cascade');
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->integer('seat_number');
            $table->unique(['schedule_id', 'seat_number']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booked_seats');
        Schema::dropIfExists('passengers');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('trip_points');
        Schema::dropIfExists('trip_schedules');
        Schema::dropIfExists('trips');
        Schema::dropIfExists('locations');
    }
};
