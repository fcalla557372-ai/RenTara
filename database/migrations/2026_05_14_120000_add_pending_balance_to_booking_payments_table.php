<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE `booking_payments` MODIFY `payment_status` ENUM('Pending Payment', 'Partial Payment', 'Payment Confirmed', 'Pending Balance', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Pending Payment';");
        DB::statement("UPDATE `booking_payments` SET `payment_status` = 'Pending Balance' WHERE `payment_status` = 'Completed' AND `remaining_balance` > 0;");
    }

    public function down(): void
    {
        DB::statement("UPDATE `booking_payments` SET `payment_status` = 'Completed' WHERE `payment_status` = 'Pending Balance';");
        DB::statement("ALTER TABLE `booking_payments` MODIFY `payment_status` ENUM('Pending Payment', 'Partial Payment', 'Payment Confirmed', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Pending Payment';");
    }
};
