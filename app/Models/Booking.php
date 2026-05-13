<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'car_id', 'pickup_date', 'return_date',
        'total_amount',
    ];

    protected $casts = [
        'pickup_date'       => 'date',
        'return_date'       => 'date',
        'total_amount'      => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function car()
    {
        return $this->belongsTo(Car::class)->withTrashed();
    }

    public function payment()
    {
        return $this->hasOne(BookingPayment::class);
    }

    public function document()
    {
        return $this->hasOneThrough(UserDocument::class, User::class, 'id', 'user_id', 'user_id', 'id');
    }

    public function getPaymentStatusAttribute()
    {
        return $this->payment?->payment_status;
    }

    public function getPaymentMethodAttribute()
    {
        return $this->payment?->payment_method;
    }

    public function getPaymentTypeAttribute()
    {
        return $this->payment?->payment_type;
    }

    public function getAmountPaidAttribute()
    {
        return $this->payment?->amount_paid ?? 0;
    }

    public function getRemainingBalanceAttribute()
    {
        return $this->payment?->remaining_balance ?? 0;
    }

    public function scopeWherePaymentStatus($query, string $status)
    {
        return $query->whereHas('payment', fn($q) => $q->where('payment_status', $status));
    }

    public function scopeWherePaymentMethod($query, string $method)
    {
        return $query->whereHas('payment', fn($q) => $q->where('payment_method', $method));
    }
}

