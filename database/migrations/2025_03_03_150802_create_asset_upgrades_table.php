<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use app\Enums\UpgradeType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asset_upgrades', function (Blueprint $table) {
            $table->id();
            $table->string("type")->default(UpgradeType::ORDER->value);
            $table->foreignId("client_id");
            $table->foreignId("request_id");
            $table->foreignId("from_package_id");
            $table->foreignId("to_package_id");
            $table->foreignId("client_package_id");
            $table->foreignId("order_id")->nullable();
            $table->boolean("complete")->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_upgrades');
    }
};
