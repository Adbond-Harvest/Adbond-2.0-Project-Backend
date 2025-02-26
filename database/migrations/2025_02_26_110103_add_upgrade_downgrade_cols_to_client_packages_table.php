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
            $table->foreignId("upgrade_id")->nullable()->after("purchase_completed_at");
            $table->foreignId("downgrade_id")->nullable()->after("upgrade_id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_packages', function (Blueprint $table) {
            $table->dropColumn("upgrade_id");
            $table->dropColumn("downgrade_id");
        });
    }
};
