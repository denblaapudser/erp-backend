<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class BulkUpdateProductsRequest extends FormRequest
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
            'productIds' => 'required|array',
            'productIds.*' => 'exists:inventory_products,id',
            'quantity' => 'nullable|integer|min:0',
            'alertThreshold' => 'nullable|integer|min:0',
            'shouldAlert' => 'nullable|string',
        ];
    }
}
