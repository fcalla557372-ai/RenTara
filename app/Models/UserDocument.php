<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'driver_license_no',
        'license_expiry',
        'national_id_no',
        'id_type',
        'valid_id_path',
        'license_photo_path',
        'birth_cert_path',
    ];

    protected $casts = [
        'license_expiry' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
