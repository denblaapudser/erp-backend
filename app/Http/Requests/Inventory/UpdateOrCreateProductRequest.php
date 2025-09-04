<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrCreateProductRequest extends FormRequest
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
            'id' => 'nullable|exists:inventory_products,id',
            'imageId' => 'nullable|exists:images,id',
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'shouldAlert' => 'required|boolean',
            'alertThreshold' => 'required|integer|min:0',
            'note' => 'nullable|string',
            'restockUrl' => 'nullable|url|max:255',
        ];
    }
}
