<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'login' => ['required', 'string', 'regex:/^[A-ZĄĆĘŁŃÓŚŹŻ][a-ząćęłńóśźż]+[A-ZĄĆĘŁŃÓŚŹŻ][a-ząćęłńóśźż]+$/u'],
            'password' => ['required', 'string', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'login.regex' => 'Invalid username format',
            'password.date' => 'Invalid date format',
        ];
    }
}
