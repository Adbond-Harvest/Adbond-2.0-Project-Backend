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
        Schema::create('site_tour_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId("booked_schedules_id");
            $table->foreignId("client_id")->nullable();
            $table->string("firstname");
            $table->string("lastname");
            $table->string("email");
            $table->string("phone_number")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_tour_bookings');
    }
};
