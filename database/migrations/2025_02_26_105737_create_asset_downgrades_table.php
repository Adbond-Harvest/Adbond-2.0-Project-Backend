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
        Schema::create('asset_downgrades', function (Blueprint $table) {
            $table->id();
            $table->foreignId("client_id");
            $table->foreignId("request_id");
            $table->foreignId("from_package_id");
            $table->foreignId("to_package_id");
            $table->foreignId("client_package_id");
            $table->double("penalty")->default(0);
            $table->double("penalty_amount")->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_downgrades');
    }
};
