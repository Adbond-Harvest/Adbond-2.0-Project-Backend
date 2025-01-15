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
        Schema::table('client_packages', function (Blueprint $table) {
            $table->boolean("purchase_complete")->default(false)->after("origin");
            $table->double("amount")->after("doa_file_id");
            $table->double("unit_price")->after("amount")->nullable();
            $table->integer("units")->after("amount")->nullable();
            // $table->string("package_type")->after("amount");
            // $table->foreignId("purchase_id")->nullable()->change();
            // $table->string("purchase_type")->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_packages', function (Blueprint $table) {
            //
        });
    }
};
