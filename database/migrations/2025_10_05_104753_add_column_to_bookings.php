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
        Schema::table('bookings', function (Blueprint $table) {
            // Add boarding_point_id and dropping_point_id columns
            $table->unsignedBigInteger('boarding_point_id')->nullable()->after('contact_phone');
            $table->unsignedBigInteger('dropping_point_id')->nullable()->after('boarding_point_id');
            
            // Add foreign key constraints
            $table->foreign('boarding_point_id')->references('id')->on('trip_points')->onDelete('cascade');
            $table->foreign('dropping_point_id')->references('id')->on('trip_points')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['boarding_point_id']);
            $table->dropForeign(['dropping_point_id']);
            $table->dropColumn(['boarding_point_id', 'dropping_point_id']);
        });
    }
};
