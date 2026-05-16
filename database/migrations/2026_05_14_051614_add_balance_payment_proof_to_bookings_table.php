<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('balance_status', ['unpaid', 'paid'])->default('unpaid')->after('license_photo_path');
            $table->string('balance_gcash_reference_no')->nullable()->after('balance_status');
            $table->string('balance_gcash_receipt_path')->nullable()->after('balance_gcash_reference_no');
            $table->enum('balance_payment_status', ['not_submitted', 'pending_confirmation', 'confirmed'])
                  ->default('not_submitted')
                  ->after('balance_gcash_receipt_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['balance_status', 'balance_gcash_reference_no', 'balance_gcash_receipt_path', 'balance_payment_status']);
        });
    }
};
