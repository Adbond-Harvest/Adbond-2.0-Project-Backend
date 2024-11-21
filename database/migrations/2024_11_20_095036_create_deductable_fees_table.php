<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // This table manages all the fees, taxes, penalties
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('deductible_fees', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->double("percentage");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deductible_fees');
    }
};
