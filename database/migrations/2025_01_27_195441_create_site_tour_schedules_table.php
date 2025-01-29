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
        Schema::create('site_tour_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId("project_type_id");
            $table->foreignId("project_id");
            $table->foreignId("package_id");
            $table->date("available_date");
            $table->time("available_time");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_tour_schedules');
    }
};
