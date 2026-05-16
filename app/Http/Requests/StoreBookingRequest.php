<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'car_id'            => [
                'required',
                Rule::exists('cars', 'id')->where('status', 'available'),
            ],
            'pickup_date'       => 'required|date_format:Y-m-d|after_or_equal:today',
            'return_date'       => 'required|date_format:Y-m-d|after_or_equal:pickup_date',
            'payment_method'    => 'required|in:GCash',
            'payment_type'      => 'required|in:full,partial',
            'amount_paid'       => 'required|numeric|min:0',
            'gcash_reference_no'=> 'required|string|max:50',
            'gcash_receipt'     => 'required|file|extensions:jpg,jpeg,png,pdf|max:10240',
            'driver_license_no' => 'required|string|max:50',
            'license_expiry'    => 'required|date_format:Y-m-d|after:today',
            'national_id_no'    => 'required|string|max:50',
            'id_type'           => 'required|string',
            'valid_id'          => 'required|file|extensions:jpg,jpeg,png,pdf|max:10240',
            'license_photo'     => 'required|file|extensions:jpg,jpeg,png,pdf|max:10240',
            'birth_cert'        => 'nullable|file|extensions:jpg,jpeg,png,pdf|max:10240',
        ];
    }
}
