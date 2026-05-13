<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Create car_specifications table to store car brand, model, and year
        Schema::create('car_specifications', function (Blueprint $table) {
            $table->id();
            $table->string('brand')->unique();
            $table->string('model')->unique();
            $table->year('year')->unique();
            $table->timestamps();
        });

        // Add foreign key to cars table
        Schema::table('cars', function (Blueprint $table) {
            $table->foreignId('car_specification_id')->nullable()->after('name')->constrained('car_specifications')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropConstrainedForeignId('car_specification_id');
        });

        Schema::dropIfExists('car_specifications');
    }
};
