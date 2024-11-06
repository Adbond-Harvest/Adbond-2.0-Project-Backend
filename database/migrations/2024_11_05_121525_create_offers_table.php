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
            $table->foreignId("client_id")->references("id")->on("clients");
            $table->foreignId("package_id")->references("id")->on("packages");
            $table->double("units")->nullable();
            $table->foreignId("project_id")->references("id")->on("projects");
            $table->double("price");
            $table->boolean("active")->default(false);
            $table->boolean("approved")->default(false);
            $table->text("rejected_reason")->nullable();
            $table->boolean("completed");
            $table->foreignId("payment_status_id")->references("id")->on("payment_statuses");
            $table->foreignId("user_id")->nullable()->references("id")->on("users");
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
