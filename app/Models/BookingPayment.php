<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'payment_method',
        'payment_type',
        'amount_paid',
        'remaining_balance',
        'gcash_reference_no',
        'gcash_receipt_path',
        'payment_status',
        'balance_gcash_reference_no',
        'balance_gcash_receipt_path',
        'balance_payment_status',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function statusBadgeClass(): string
    {
        return match($this->payment_status) {
            'Pending Payment'   => 'bg-orange-100 text-orange-700',
            'Partial Payment'   => 'bg-yellow-100 text-amber-700',
            'Payment Confirmed' => 'bg-blue-100 text-blue-700',
            'Pending Balance'   => 'bg-slate-100 text-slate-800',
            'Completed'         => 'bg-green-100 text-green-700',
            'Cancelled'         => 'bg-red-100 text-red-700',
            default             => 'bg-gray-100 text-gray-700',
        };
    }
}
