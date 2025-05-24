<?php

namespace App\Http\Requests;

use App\Helpers\ApiResponse;
use Illuminate\Validation\Rules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UserRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }



    // If Validations Errors:
    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = ApiResponse::sendResponse(422, 'Validation Errors', $validator->errors());
        throw new ValidationException($validator, $response);
    }




    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => ['required' , Rules\Password::defaults()],
        ];
    }
}
