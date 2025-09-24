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
        Schema::create('table_migrations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('migrated')->default(false);
            $table->timestamps();
        });

        Artisan::call("db:seed", ["--class" => "V1Tables"]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_migrations');
    }
};
