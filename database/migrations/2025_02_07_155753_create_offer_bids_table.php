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
        Schema::create('offer_bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId("offer_id");
            $table->foreignId("client_id");
            $table->double("price");
            $table->boolean("accepted")->nullable();
            $table->boolean("cancelled")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_bids');
    }
};
