<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Fix car_specifications unique constraints - should be composite, not individual
        Schema::table('car_specifications', function (Blueprint $table) {
            $table->dropUnique(['brand']);
            $table->dropUnique(['model']);
            $table->dropUnique(['year']);
            $table->unique(['brand', 'model', 'year'], 'unique_brand_model_year');
        });
    }

    public function down(): void
    {
        Schema::table('car_specifications', function (Blueprint $table) {
            $table->dropUnique('unique_brand_model_year');
            $table->unique('brand');
            $table->unique('model');
            $table->unique('year');
        });
    }
};
