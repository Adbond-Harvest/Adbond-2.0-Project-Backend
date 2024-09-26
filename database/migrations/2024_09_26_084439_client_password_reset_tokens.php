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
        Schema::create('client_password_reset_tokens', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('email', 191);
            $table->string('token_signature');
            $table->timestamp('expires_at');
            $table->boolean("verified")->default(false);
            $table->timestamp('created_at')->nullable();
            $table->timestamp("updated_at")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_password_reset_tokens');
    }
};
