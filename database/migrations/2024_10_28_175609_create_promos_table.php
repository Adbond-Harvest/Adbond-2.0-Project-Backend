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
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->double("discount");
            $table->date("start")->nullable();
            $table->date("end")->nullable();
            $table->boolean("active")->default(true);
            $table->text("description")->nullable();
            $table->boolean("package_limited")->default(false);
            $table->boolean("has_promo_code")->default(false);
            $table->foreignId("user_id");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};
