<?php

namespace App\Presentation\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class CreateCustomerRequest extends FormRequest
{
    /**
     * Autoriza a requisição.
     * 
     * Todos os usuários autenticados podem criar clientes.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação para criação de cliente.
     * 
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'document' => ['required', 'string', 'max:20', 'unique:customers,document'],
            'email' => ['required', 'email', 'max:255', 'unique:customers,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'status' => ['in:active,inactive,prospect,churned'],
            'segment_id' => ['nullable', 'exists:customer_segments,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],
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
            'assigned_to.exists' => 'Usuário responsável não encontrado',
        ];
    }
}
