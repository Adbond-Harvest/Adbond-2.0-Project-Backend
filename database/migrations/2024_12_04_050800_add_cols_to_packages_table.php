<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use app\EnumClass;
use app\Enums\ProductCategory;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            // $table->enum("category", EnumClass::productCategories())->default(ProductCategory::PURCHASE->value)->after("name");
            $table->integer("interest_return_duration")->nullable()->after("min_price");
            $table->integer("interest_return_timeline")->nullable()->after("interest_return_duration");
            $table->double("interest_return_percentage")->nullable()->after("interest_return_timeline");
            $table->double("interest_return_amount")->nullable()->after("interest_return_percentage");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            //
        });
    }
};
