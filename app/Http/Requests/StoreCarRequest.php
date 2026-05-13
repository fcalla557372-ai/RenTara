<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCarRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'       => 'required|string|max:255',
            'type'       => 'required|in:Sedan,SUV,Luxury,Electric Car,Van,MPV,Hatchback,Crossover',
            'daily_rate' => 'required|numeric|min:1',
            'image'      => 'nullable|file|extensions:jpg,jpeg,png,webp|max:10240',
            'brand'      => 'nullable|string|max:100',
            'model'      => 'nullable|string|max:100',
            'year'       => 'nullable|integer|min:2000|max:2030',
        ];
    }
}
