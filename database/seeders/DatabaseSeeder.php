<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Production seed: keep only the default admin account.
        $this->call([
            UserSeeder::class,
        ]);
    }
}
