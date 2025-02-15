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
        Schema::create('client_commission_earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId("client_id");
            $table->foreignId("order_id");
            $table->double("amount");
            $table->double("commission");
            $table->double("commission_amount");
            $table->double("tax");
            $table->double("amount_after_tax");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_commission_earnings');
    }
};
