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
        Schema::create('asset_metrics', function (Blueprint $table) {
            $table->id();
            $table->integer("total")->nullable();
            $table->integer("previous_total")->nullable();
            $table->integer("active_total")->nullable();
            $table->integer("previous_active_total")->nullable();
            $table->timestamps();
        });

        Artisan::call('db:seed', array('--class' => 'InitialAssetMetrics'));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_metrics');
    }
};
