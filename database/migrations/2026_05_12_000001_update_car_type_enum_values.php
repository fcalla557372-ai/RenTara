<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE `cars` MODIFY `type` ENUM('Sedan','SUV','Luxury','Electric Car','Van','MPV','Hatchback','Crossover') NOT NULL;");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `cars` MODIFY `type` ENUM('Sedan','SUV','Luxury','Electric Car') NOT NULL;");
    }
};
