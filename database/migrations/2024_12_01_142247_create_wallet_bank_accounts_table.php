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
        Schema::create('wallet_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId("wallet_id");
            $table->foreignId("bank_id");
            $table->string("account_name");
            $table->string("account_number");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_bank_accounts');
    }
};