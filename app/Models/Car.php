<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Car extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'car_specification_id',
        'type', 'daily_rate', 'status', 'image_path',
    ];

    public function specification()
    {
        return $this->belongsTo(CarSpecification::class, 'car_specification_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }
}
