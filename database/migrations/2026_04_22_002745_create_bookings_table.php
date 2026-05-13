<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('car_id')->constrained()->onDelete('cascade');
            $table->date('pickup_date');
            $table->date('return_date');
            $table->decimal('total_amount', 10, 2);
            $table->enum('payment_method', ['GCash']);
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
            $table->string('driver_license_no');
            $table->date('license_expiry');
            $table->string('national_id_no');
            $table->string('id_type');
            $table->string('valid_id_path')->nullable();
            $table->string('birth_cert_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};