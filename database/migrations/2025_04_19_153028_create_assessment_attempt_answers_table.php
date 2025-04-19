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
        Schema::create('assessment_attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId("attempt_id");
            $table->string("question");
            $table->foreignId("question_id");
            $table->string("answer")->nullable();
            $table->string("correct_answer");
            $table->boolean("correct");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_attempt_answers');
    }
};
