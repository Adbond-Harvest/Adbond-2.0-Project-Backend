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
        Schema::create('company_info', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->foreignId('logo_file_id')->nullable();
            $table->integer('year_founded');
            $table->text('about');
            $table->double('virtual_staff_assessment_cut_off_mark', 3, 1)->default(60);
            $table->integer('virtual_staff_assessment_time_limit')->default(60);
            $table->double('commission_tax', 3, 1)->default(10.0);
            $table->double('loyalty_discount', 3, 1)->default(10.0);
            $table->timestamps();
        });
        Artisan::call("db:seed", ["--class" => "CompanyInfo"]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_infos');
    }
};
