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
        Schema::create('site_tour_booked_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId("site_tour_schedule_id");
            $table->date("booked_date");
            $table->boolean("cancelled")->default(false);
            $table->boolean("visited")->default(false);
            $table->integer("total");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_tour_booked_schedules');
    }
};
