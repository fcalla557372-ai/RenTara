<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Remove document columns from bookings table
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'driver_license_no',
                'license_expiry',
                'national_id_no',
                'id_type',
                'valid_id_path',
                'birth_cert_path',
                'payment_method',
                'payment_type',
                'amount_paid',
                'remaining_balance',
                'gcash_reference_no',
                'gcash_receipt_path',
                'payment_status'
            ]);
        });

        // Remove specification columns from cars table
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn(['brand', 'model', 'year']);
        });
    }

    public function down(): void
    {
        // Restore document columns to bookings table
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('driver_license_no')->nullable();
            $table->date('license_expiry')->nullable();
            $table->string('national_id_no')->nullable();
            $table->string('id_type')->nullable();
            $table->string('valid_id_path')->nullable();
            $table->string('birth_cert_path')->nullable();
            $table->enum('payment_method', ['GCash'])->default('GCash')->nullable();
            $table->enum('payment_type', ['full', 'partial'])->default('full')->nullable();
            $table->decimal('amount_paid', 10, 2)->default(0)->nullable();
            $table->decimal('remaining_balance', 10, 2)->default(0)->nullable();
            $table->string('gcash_reference_no')->nullable();
            $table->string('gcash_receipt_path')->nullable();
            $table->enum('payment_status', [
                'Pending Payment',
                'Partial Payment',
                'Payment Confirmed',
                'Completed',
                'Cancelled'
            ])->default('Pending Payment')->nullable();
        });

        // Restore specification columns to cars table
        Schema::table('cars', function (Blueprint $table) {
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->year('year')->nullable();
        });
    }
};
