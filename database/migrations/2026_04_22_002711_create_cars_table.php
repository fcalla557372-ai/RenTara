<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->year('year')->nullable();
            $table->enum('type', ['Sedan', 'SUV', 'Luxury', 'Electric Car', 'Van', 'MPV', 'Hatchback', 'Crossover']);
            $table->decimal('daily_rate', 10, 2);
            $table->enum('status', ['available', 'rented'])->default('available');
            $table->string('image_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};