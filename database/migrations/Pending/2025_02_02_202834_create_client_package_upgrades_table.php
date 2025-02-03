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
        Schema::create('client_package_upgrades', function (Blueprint $table) {
            $table->id();
            $table->foreignId("client_id");
            $table->foreignId("client_package_id");
            $table->foreignId("origin_package_id");
            $table->foreignId("upgrade_package_id");
            $table->boolean("approved")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_package_upgrades');
    }
};
