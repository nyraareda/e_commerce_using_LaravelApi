<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateCategoryRequest extends FormRequest
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
    $categoryId = $this->route('id');

        $rules = [
            'name' => ["required", "min:3",Rule::unique('categories', 'name')->ignore($categoryId)],
            "description" => ["required", "min:10"],
            ];
            if ($this->isMethod('post')) {
                $rules['name'][] = Rule::unique('categories', 'name');
            }
            return $rules;
    }
        

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    // public function messages(): array
    //     {

    //     return [
    //         'name.required' => 'The category name is required.',
    //         'name.min' => 'The category name must be at least :min characters.',
    //         'name.unique' => 'The category name must be unique.',
    //         'description.required' => 'The category description is required.',
    //         'description.min' => 'The category description must be at least :min characters.',
    //         'product_ids.array' => 'The product IDs must be provided as an array.',
    //     ];
    // }
}
