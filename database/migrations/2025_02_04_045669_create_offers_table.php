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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId("client_id");
            $table->foreignId("package_id");
            $table->foreignId("client_package_id");
            $table->double("units")->nullable();
            $table->foreignId("project_id");
            $table->double("price");
            $table->double("package_price");
            $table->foreignId("resell_order_id")->nullable();
            $table->foreignId("accepted_bid_id")->nullable();
            $table->boolean("active")->default(true);
            $table->boolean("approved")->nullable();
            $table->text("rejected_reason")->nullable();
            $table->boolean("completed")->default(false);
            $table->foreignId("payment_status_id")->nullable();
            $table->foreignId("user_id")->nullable();
            $table->date("approval_date")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
