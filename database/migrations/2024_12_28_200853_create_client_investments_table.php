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
        Schema::create('client_investments', function (Blueprint $table) {
            $table->id();
            $table->foreignId("client_id");
            $table->foreignId("package_id");
            $table->foreignId("order_id");
            $table->string("redemption_option");
            $table->foreignId("redemption_package_id")->nullable();
            $table->double("capital");
            $table->integer("duration");
            $table->integer("timeline");
            $table->double("percentage")->nullable();
            $table->double("amount")->nullable();
            $table->date("start_date")->nullable();
            $table->date("next_interest_date")->nullable();
            $table->date("end_date")->nullable();
            $table->boolean("started")->default(false);
            $table->integer("interest_payments_left");
            $table->boolean("ended")->default(false);
            $table->foreignId("memorandum_agreement_file_id")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_investments');
    }
};
