<?php

namespace App\Http\Requests;

use App\Helpers\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UserLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    // If Validation Errors
    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $respones = ApiResponse::sendResponse(422, "Validation Errors", $validator->errors());
        throw new ValidationException($validator, $respones);
    }



    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email'     => 'required|email',
            'password'  => 'required',
        ];
    }
}
