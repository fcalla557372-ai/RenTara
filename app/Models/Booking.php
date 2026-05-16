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
        'balance_status',
        'balance_gcash_reference_no',
        'balance_gcash_receipt_path',
        'balance_payment_status',
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

    public function getPaymentModeAttribute()
    {
        return $this->payment_type;
    }

    public function canPayBalance(): bool
    {
        return $this->payment_mode === 'partial'
            && $this->payment_status === 'Pending Balance'
            && $this->remaining_balance > 0
            && ($this->balance_status ?? 'unpaid') === 'unpaid'
            && $this->balance_payment_status === 'not_submitted';
    }

    public function balancePaymentPending(): bool
    {
        return $this->balance_payment_status === 'pending_confirmation';
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

    public function getAmountPaidAttribute()
    {
        return $this->payment?->amount_paid ?? 0;
    }

    public function getRemainingBalanceAttribute()
    {
        return $this->payment?->remaining_balance ?? 0;
    }

    public function getGcashReferenceNoAttribute()
    {
        return $this->payment?->gcash_reference_no;
    }

    public function getGcashReceiptPathAttribute()
    {
        return $this->payment?->gcash_receipt_path;
    }

    public function getValidIdPathAttribute()
    {
        return $this->document?->valid_id_path;
    }

    public function getLicensePhotoPathAttribute()
    {
        return $this->document?->license_photo_path;
    }

    public function getBirthCertPathAttribute()
    {
        return $this->document?->birth_cert_path;
    }

    public function getDriverLicenseNoAttribute()
    {
        return $this->document?->driver_license_no;
    }

    public function getLicenseExpiryAttribute()
    {
        return $this->document?->license_expiry;
    }

    public function getNationalIdNoAttribute()
    {
        return $this->document?->national_id_no;
    }

    public function getIdTypeAttribute()
    {
        return $this->document?->id_type;
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

