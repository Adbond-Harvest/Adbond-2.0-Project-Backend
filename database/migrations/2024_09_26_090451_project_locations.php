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
        // Schema::create('project_locations', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->foreignId('project_id')->references("id")->on("projects");
        //     $table->foreignId('state_id')->references("id")->on("states");
        //     $table->string('address')->nullable();
        //     $table->boolean('active')->default(1);
        //     $table->dateTime('deactivated_at')->nullable();
        //     $table->timestamps();
        // });
        Schema::dropIfExists('project_locations');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_locations');
    }
};
