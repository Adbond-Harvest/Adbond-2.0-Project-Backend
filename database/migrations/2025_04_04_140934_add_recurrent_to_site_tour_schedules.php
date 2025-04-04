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
        Schema::table('site_tour_schedules', function (Blueprint $table) {
            $table->date("available_date")->nullable()->change();
            $table->boolean("recurrent")->default(false)->after("available_time");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_tour_schedules', function (Blueprint $table) {
            //
        });
    }
};
