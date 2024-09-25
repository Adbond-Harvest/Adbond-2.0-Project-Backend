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
        Schema::table('client_password_reset_tokens', function (Blueprint $table) {
            $table->boolean("verified")->default(false)->after("expires_at");
            $table->dateTime("updated_at")->nullable()->after("created_at");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_password_reset_tokens', function (Blueprint $table) {
            $table->dropColumn("verified");
        });
    }
};
