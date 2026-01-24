<?php

namespace App\Presentation\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
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
        $productId = $this->route('product');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'sku' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('products', 'sku')->ignore($productId)],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'integer', 'exists:product_categories,id'],
            'base_price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'unit' => ['sometimes', 'required', Rule::in(['unit', 'kg', 'liter', 'meter', 'hour', 'month'])],
            'is_active' => ['nullable', 'boolean'],
            'requires_approval' => ['nullable', 'boolean'],
            'specifications' => ['nullable', 'array'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome do produto é obrigatório',
            'sku.required' => 'O SKU é obrigatório',
            'sku.unique' => 'Este SKU já está sendo utilizado',
            'base_price.required' => 'O preço base é obrigatório',
            'base_price.numeric' => 'O preço base deve ser um número',
            'base_price.min' => 'O preço base deve ser maior ou igual a zero',
            'category_id.exists' => 'Categoria inválida',
            'unit.required' => 'A unidade é obrigatória',
            'unit.in' => 'Unidade inválida',
        ];
    }
}
