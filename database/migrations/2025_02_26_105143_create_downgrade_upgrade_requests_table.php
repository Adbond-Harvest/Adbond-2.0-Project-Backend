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
        Schema::create('downgrade_upgrade_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId("client_id");
            $table->string("type");
            $table->foreignId("from_package_id");
            $table->foreignId("to_package_id");
            $table->foreignId("client_package_id");
            $table->boolean("approved")->nullable();
            $table->text("rejected_reason")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('downgrade_upgrade_requests');
    }
};
