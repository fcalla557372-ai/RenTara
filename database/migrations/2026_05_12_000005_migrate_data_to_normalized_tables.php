<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Migrate user documents from bookings table to user_documents table
        DB::statement('
            INSERT INTO user_documents (user_id, driver_license_no, license_expiry, national_id_no, id_type, valid_id_path, birth_cert_path, created_at, updated_at)
            SELECT DISTINCT user_id, driver_license_no, license_expiry, national_id_no, id_type, valid_id_path, birth_cert_path, NOW(), NOW()
            FROM bookings
            WHERE driver_license_no IS NOT NULL
            ON DUPLICATE KEY UPDATE updated_at = NOW()
        ');

        // Migrate car specifications from cars table to car_specifications table
        DB::statement('
            INSERT IGNORE INTO car_specifications (brand, model, year, created_at, updated_at)
            SELECT DISTINCT brand, model, year, NOW(), NOW()
            FROM cars
            WHERE brand IS NOT NULL OR model IS NOT NULL OR year IS NOT NULL
        ');

        // Update cars table with car_specification_id
        DB::statement('
            UPDATE cars c
            INNER JOIN car_specifications cs ON c.brand = cs.brand AND c.model = cs.model AND c.year = cs.year
            SET c.car_specification_id = cs.id
        ');

        // Migrate booking payments from bookings table to booking_payments table
        DB::statement('
            INSERT INTO booking_payments (booking_id, payment_method, payment_type, amount_paid, remaining_balance, gcash_reference_no, gcash_receipt_path, payment_status, created_at, updated_at)
            SELECT id, payment_method, payment_type, amount_paid, remaining_balance, gcash_reference_no, gcash_receipt_path, payment_status, NOW(), NOW()
            FROM bookings
        ');
    }

    public function down(): void
    {
        // Revert migrations in reverse order
        DB::statement('DELETE FROM booking_payments');
        DB::statement('UPDATE cars SET car_specification_id = NULL');
        DB::statement('DELETE FROM car_specifications');
        DB::statement('DELETE FROM user_documents');
    }
};
