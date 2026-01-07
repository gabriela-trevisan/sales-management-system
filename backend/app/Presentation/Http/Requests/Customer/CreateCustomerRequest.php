<?php

namespace App\Presentation\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class CreateCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // TODO: Add authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'document' => ['required', 'string', 'max:20', 'unique:customers,document'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'status' => ['in:active,inactive,prospect,churned'],
            'assigned_to' => ['nullable', 'exists:users,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
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
