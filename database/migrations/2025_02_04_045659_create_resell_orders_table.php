<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\Artisan;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('resell_orders', function (Blueprint $table) {
            $table->id();
            $table->double("percentage");
            $table->string("duration_text");
            $table->integer("duration");
            $table->string("duration_type");
            $table->timestamps();
        });

        Artisan::call('db:seed', array('--class' => 'ResellOrders'));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resell_orders');
    }
};
