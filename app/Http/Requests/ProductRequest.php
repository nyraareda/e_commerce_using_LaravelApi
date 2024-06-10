<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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

    public function rules()
{
    $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

    // Conditional validation rules
    $rules = [
        "name" => $isUpdate ? ["sometimes", "required", "string", "max:255"] : ["required", "string", "max:255"],
        "description" => ["required", "string", "min:10"],
        "price" => ["sometimes", "required", "numeric", "min:0"],
        "quantity" => ["required", "numeric", "min:1"],
        "image" => ["sometimes", "image", "mimes:jpeg,png,jpg,gif,svg", "max:2048"],
        "category_ids" => ["sometimes", "exists:categories,id"],
    ];
    return $rules;
}
}
