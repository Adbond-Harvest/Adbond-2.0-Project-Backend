<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use app\EnumClass;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('staff_commission_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id");
            $table->foreignId("transaction_id");
            $table->string("transaction_type", 100);
            $table->double("balance");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_commission_transactions');
    }
};
