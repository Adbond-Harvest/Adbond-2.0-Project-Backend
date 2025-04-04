<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('site_tour_bookings', function (Blueprint $table) {
            $table->foreignId("client_id")->nullable()->change();
            $table->string("firstname")->after("client_id");
            $table->string("lastname")->after("firstname");
            $table->string("email")->after("lastname");
            $table->string("phone_number")->nullable()->after("email");
        });
        Artisan::call("db:seed", ["--class" => "UpdateSiteTourBookings"]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_tour_bookings', function (Blueprint $table) {
            //
        });
    }
};
