<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarSpecification extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand',
        'model',
        'year',
    ];

    public function cars()
    {
        return $this->hasMany(Car::class);
    }
}
