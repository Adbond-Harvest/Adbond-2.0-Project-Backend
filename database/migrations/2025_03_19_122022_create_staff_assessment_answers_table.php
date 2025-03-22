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
        Schema::create('staff_assessment_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_assessment_attempt_id');
            $table->foreignId("question_id");
            $table->foreignId("selected_option_id");
            $table->text('question');
            $table->text('answer');
            $table->text('correct_answer');
            $table->boolean('correct');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_assessment_answers');
    }
};
