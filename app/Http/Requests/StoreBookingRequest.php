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
            'pickup_date'       => 'required|date|after_or_equal:today',
            'return_date'       => 'required|date|after_or_equal:pickup_date',
            'payment_method'    => 'required|in:GCash',
            'payment_type'      => 'required|in:full,partial',
            'amount_paid'       => 'required|numeric|min:0',
            'gcash_reference_no'=> 'required|string|max:50',
            'gcash_receipt'     => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'driver_license_no' => 'required|string|max:50',
            'license_expiry'    => 'required|date|after:today',
            'national_id_no'    => 'required|string|max:50',
            'id_type'           => 'required|string',
            'valid_id'          => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'license_photo'     => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'birth_cert'        => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($validator->fails()) {
                return;
            }

            $car = \App\Models\Car::find($this->input('car_id'));
            if (! $car) {
                return;
            }

            $pickup = \Carbon\Carbon::parse($this->input('pickup_date'));
            $return = \Carbon\Carbon::parse($this->input('return_date'));
            $days = max(1, $pickup->diffInDays($return) + 1);
            $total = $car->daily_rate * $days;
            $amountPaid = $this->input('amount_paid');
            $paymentType = $this->input('payment_type');

            if ($paymentType === 'full') {
                if (abs($amountPaid - $total) > 0.01) {
                    $validator->errors()->add('amount_paid', 'Amount paid must equal the total amount when paying in full.');
                }
            }

            if ($paymentType === 'partial') {
                $expected = round($total * 0.3, 2);
                if (abs($amountPaid - $expected) > 0.01) {
                    $validator->errors()->add('amount_paid', "Partial payment must equal 30% of the total amount (₱{$expected}).");
                }
            }
        });
    }
}
