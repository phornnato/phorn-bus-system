<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
        // Drop foreign key first (if exists)
        $table->dropForeign(['user_id']);

        // Change column to normal unsigned big integer
        $table->unsignedBigInteger('user_id')->nullable()->change();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('bookings', function (Blueprint $table) {
        // Re-add foreign key if you rollback
        $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
    });
    }
};
