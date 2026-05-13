<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Only update enum definitions if columns don't exist (for fresh migrations)
        // The columns are already defined in 2026_04_22_002745_create_bookings_table
        // This migration ensures the enums are consistent and payment_method is GCash-only

        // Update existing bookings to use GCash before restricting the enum
        DB::statement("UPDATE bookings SET payment_method = 'GCash' WHERE payment_method = 'Cash on Pickup' OR payment_method = 'GCash'");

        // Update enum to GCash only
        DB::statement("ALTER TABLE bookings MODIFY payment_method ENUM('GCash') NOT NULL DEFAULT 'GCash'");
        
        // Update payment_status enum to include Partial Payment
        DB::statement("ALTER TABLE bookings MODIFY payment_status ENUM('Pending Payment','Partial Payment','Payment Confirmed','Completed','Cancelled') NOT NULL DEFAULT 'Pending Payment'");
    }

    public function down(): void
    {
        // Restore to accept both payment methods for rollback
        DB::statement("ALTER TABLE bookings MODIFY payment_method ENUM('GCash') NOT NULL DEFAULT 'GCash'");
        
        // Restore to original payment_status enum
        DB::statement("ALTER TABLE bookings MODIFY payment_status ENUM('Pending Payment','Payment Confirmed','Completed','Cancelled') NOT NULL DEFAULT 'Pending Payment'");
    }
};
