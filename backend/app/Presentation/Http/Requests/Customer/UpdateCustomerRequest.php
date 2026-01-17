<?php

namespace App\Presentation\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    /**
     * Autoriza a requisição.
     * 
     * Todos os usuários autenticados podem atualizar clientes.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação para atualização de cliente.
     * 
     * Nota: O campo assigned_to não pode ser alterado via update.
     * O responsável é auto-atribuído apenas na criação.
     * 
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        $customerId = $this->route('customer') ?? $this->route('id');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'document' => ['sometimes', 'required', 'string', 'max:20', "unique:customers,document,{$customerId}"],
            'email' => ['sometimes', 'required', 'email', 'max:255', "unique:customers,email,{$customerId}"],
            'phone' => ['nullable', 'string', 'max:20'],
            'status' => ['in:active,inactive,prospect,churned'],
            'segment_id' => ['nullable', 'exists:customer_segments,id'],
        ];
    }

    /**
     * Mensagens customizadas de erro de validação.
     * 
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome do cliente é obrigatório',
            'document.required' => 'O documento (CPF/CNPJ) é obrigatório',
            'document.unique' => 'Já existe um cliente com este documento',
            'email.required' => 'O email é obrigatório',
            'email.email' => 'Digite um email válido',
        ];
    }
}
