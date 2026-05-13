<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'     => 'required|string|max:255',
            'username' => 'required|string|unique:users,username|max:50',
            'email'    => 'required|email|unique:users,email',
            'role'     => 'required|in:staff,admin',
            'password' => 'required|string|min:6',
            'phone'    => 'nullable|string|max:20',
        ];
    }
}