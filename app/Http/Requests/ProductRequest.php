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
        // $rules = [
        //     'title' => ['required', 'min:3'],
        //     'details' => ['required', 'min:10'],
        //     'image' => ['nullable', 'image', 'mimes:jpg,png'],
        // ];
        // if ($this->isMethod('post')) {
        //     $rules['title'][] = Rule::unique('products', 'title');
        // }

        // return $rules;
        $isUpdate = $this->route('id') !== null;

        // Conditional validation rules
        $rules = [
            'name' => $isUpdate ? 'sometimes|required|string|max:255' : 'required|string|max:255',
            'description' => $isUpdate ? 'sometimes|required|string' : 'required|string',
            'price' => 'sometimes|required|numeric|min:0',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // 'category_id' => 'sometimes|exists:categories,id',
        ];
    
        return $rules;
    }
}
