<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Add balance payment fields to booking_payments table (3NF compliance)
        Schema::table('booking_payments', function (Blueprint $table) {
            $table->string('balance_gcash_reference_no')->nullable()->after('gcash_receipt_path');
            $table->string('balance_gcash_receipt_path')->nullable()->after('balance_gcash_reference_no');
            $table->enum('balance_payment_status', ['not_submitted', 'pending_confirmation', 'confirmed'])
                  ->default('not_submitted')
                  ->after('balance_gcash_receipt_path');
        });

        // Migrate balance payment data from bookings to booking_payments
        DB::statement('
            UPDATE booking_payments bp
            INNER JOIN bookings b ON bp.booking_id = b.id
            SET 
                bp.balance_gcash_reference_no = b.balance_gcash_reference_no,
                bp.balance_gcash_receipt_path = b.balance_gcash_receipt_path,
                bp.balance_payment_status = b.balance_payment_status
            WHERE b.balance_gcash_reference_no IS NOT NULL
               OR b.balance_gcash_receipt_path IS NOT NULL
               OR b.balance_payment_status IS NOT NULL
        ');

        // Remove balance fields from bookings table
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'balance_status',
                'balance_gcash_reference_no',
                'balance_gcash_receipt_path',
                'balance_payment_status'
            ]);
        });

        // Remove license_photo_path from bookings (should be in user_documents)
        if (Schema::hasColumn('bookings', 'license_photo_path')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropColumn('license_photo_path');
            });
        }
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('balance_status', ['unpaid', 'paid'])->default('unpaid')->after('id');
            $table->string('balance_gcash_reference_no')->nullable();
            $table->string('balance_gcash_receipt_path')->nullable();
            $table->enum('balance_payment_status', ['not_submitted', 'pending_confirmation', 'confirmed'])
                  ->default('not_submitted');
            $table->string('license_photo_path')->nullable();
        });

        // Reverse data migration
        DB::statement('
            UPDATE bookings b
            INNER JOIN booking_payments bp ON b.id = bp.booking_id
            SET 
                b.balance_gcash_reference_no = bp.balance_gcash_reference_no,
                b.balance_gcash_receipt_path = bp.balance_gcash_receipt_path,
                b.balance_payment_status = bp.balance_payment_status
            WHERE bp.balance_gcash_reference_no IS NOT NULL
               OR bp.balance_gcash_receipt_path IS NOT NULL
               OR bp.balance_payment_status IS NOT NULL
        ');

        Schema::table('booking_payments', function (Blueprint $table) {
            $table->dropColumn([
                'balance_gcash_reference_no',
                'balance_gcash_receipt_path',
                'balance_payment_status'
            ]);
        });
    }
};
