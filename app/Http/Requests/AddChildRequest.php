<?php

namespace App\Http\Requests;

use App\Helpers\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class AddChildRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }



    // Failed Validation
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'gender' => 'required|in:male,female',
            'birth_date' => 'required|date',
        ];
    }
}
