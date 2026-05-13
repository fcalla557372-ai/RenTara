<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::withTrashed()->updateOrCreate([
            'username' => 'admin',
        ], [
            'name' => 'Admin User',
            'email' => 'admin@rentara.com',
            'password' => Hash::make('admin'),
            'phone' => '09111111111',
            'date_of_birth' => '1985-01-15',
            'role' => 'admin',
            'status' => 'active',
            'theme' => 'light',
        ]);

        if ($admin->trashed()) {
            $admin->restore();
        }
    }
}
