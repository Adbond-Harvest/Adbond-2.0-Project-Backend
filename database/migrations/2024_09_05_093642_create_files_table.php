<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use app\EnumClass;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->string('user_type')->default('App\Models\User')->nullable();
            $table->enum('file_type', EnumClass::fileTypes());
            $table->string('mime_type');
            $table->string('filename');
            $table->string('original_filename');
            $table->string('extension');
            $table->bigInteger('size');
            $table->string('formatted_size');
            $table->string('url');
            $table->foreignId("belongs_id")->nullable();
            $table->string("belongs_type")->nullable();
            $table->string("purpose")->nullable();
            $table->string('public_id')->nullable();
            $table->mediumInteger('width')->nullable();
            $table->mediumInteger('height')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
