<?php

namespace App\Presentation\Http\Requests\Proposal;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Create Proposal Request
 * 
 * Valida os dados para criação de uma nova proposta.
 */
class CreateProposalRequest extends FormRequest
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
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'opportunity_id' => ['nullable', 'integer'], // TODO: Adicionar 'exists:opportunities,id' quando Module 4 implementado
            'issue_date' => ['required', 'date', 'date_format:Y-m-d'],
            'expiration_date' => ['required', 'date', 'date_format:Y-m-d', 'after:issue_date'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', 'string', Rule::in(['draft', 'sent', 'approved', 'rejected', 'expired'])],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.description' => ['nullable', 'string', 'max:1000'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'customer_id' => 'cliente',
            'opportunity_id' => 'oportunidade',
            'issue_date' => 'data de emissão',
            'expiration_date' => 'data de validade',
            'notes' => 'observações',
            'status' => 'status',
            'items' => 'itens',
            'items.*.product_id' => 'produto',
            'items.*.description' => 'descrição',
            'items.*.quantity' => 'quantidade',
            'items.*.unit_price' => 'preço unitário',
            'items.*.discount_percentage' => 'desconto percentual',
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
            'customer_id.required' => 'O :attribute é obrigatório.',
            'customer_id.exists' => 'O :attribute selecionado não existe.',
            'opportunity_id.exists' => 'A :attribute selecionada não existe.',
            'issue_date.required' => 'A :attribute é obrigatória.',
            'issue_date.date' => 'A :attribute deve ser uma data válida.',
            'expiration_date.required' => 'A :attribute é obrigatória.',
            'expiration_date.after' => 'A :attribute deve ser posterior à data de emissão.',
            'status.required' => 'O :attribute é obrigatório.',
            'status.in' => 'O :attribute selecionado é inválido.',
            'items.required' => 'É necessário adicionar pelo menos um item.',
            'items.min' => 'É necessário adicionar pelo menos um item.',
            'items.*.product_id.required' => 'O :attribute é obrigatório.',
            'items.*.product_id.exists' => 'O :attribute selecionado não existe.',
            'items.*.quantity.required' => 'A :attribute é obrigatória.',
            'items.*.quantity.min' => 'A :attribute deve ser no mínimo 1.',
            'items.*.unit_price.required' => 'O :attribute é obrigatório.',
            'items.*.unit_price.min' => 'O :attribute deve ser no mínimo 0.',
            'items.*.discount_percentage.max' => 'O :attribute não pode ser maior que 100.',
        ];
    }
}
