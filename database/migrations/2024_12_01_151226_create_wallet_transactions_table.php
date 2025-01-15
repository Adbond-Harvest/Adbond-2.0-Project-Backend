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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("reference_no")->index("reference_no_index");
            $table->foreignId("wallet_id");
            $table->foreignId("wallet_bank_account_id")->nullable();
            $table->double("amount");
            $table->double("balance");
            $table->string("transaction_type");
            $table->string("source_type")->nullable();
            $table->foreignId("source_id")->nullable();
            // $table->foreignId("package_id")->nullable();
            $table->boolean("confirmed")->nullable();
            $table->foreignId("withdrawal_request_id")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
