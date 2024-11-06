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
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId("promo_id")->references("id")->on("promos");
            $table->string("code");
            $table->boolean("active")->default(true);
            $table->date("expiry")->nullable();
            $table->integer("usage_count")->default(0);
            $table->integer("max_usage")->nullable();
            $table->boolean("package_limited")->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo_codes');
    }
};
