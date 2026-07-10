<?php
// app/Http/Requests/StoreProductRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null; // any authenticated user can sell
    }

    public function rules(): array
    {
        return [
            'name'             => ['required', 'string', 'max:150'],
            'category'         => ['required', 'in:seeds,fertilizer,produce,livestock,tools,equipment,chemicals,other'],
            'description'      => ['required', 'string', 'max:2000'],
            'price'            => ['required', 'numeric', 'min:0'],
            'unit'             => ['required', 'string', 'max:30'],
            'stock_quantity'   => ['required', 'integer', 'min:1'],
            'minimum_order'    => ['nullable', 'integer', 'min:1'],
            'district'         => ['required', 'string'],
            'price_negotiable' => ['boolean'],
            'images'           => ['nullable', 'array', 'max:5'],
            'images.*'         => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }

    public function messages(): array
    {
        return [
            'images.*.max' => 'Each image must be under 4MB.',
            'stock_quantity.min' => 'Stock quantity must be at least 1.',
        ];
    }
}
