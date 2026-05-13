<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Create booking_payments table to store payment information
        Schema::create('booking_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->enum('payment_method', ['GCash'])->default('GCash');
            $table->enum('payment_type', ['full', 'partial'])->default('full');
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('remaining_balance', 10, 2)->default(0);
            $table->string('gcash_reference_no')->nullable();
            $table->string('gcash_receipt_path')->nullable();
            $table->enum('payment_status', [
                'Pending Payment',
                'Partial Payment',
                'Payment Confirmed',
                'Completed',
                'Cancelled'
            ])->default('Pending Payment');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_payments');
    }
};
