<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class QuoteRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'destino' => 'required|string|max:255|in:nacional,americas,europa',
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after_or_equal:data_inicio',
            'viajantes' => 'required|array|min:1',
            'viajantes.*.nome' => 'required|string|max:255',
            'viajantes.*.data_nascimento' => 'required|date',
            'viajantes.*.adicionais' => 'array',
            'viajantes.*.adicionais.*' => 'string|in:bagagem,esportes_aventura'
        ];
    }

    public function messages()
    {
        return [
            'data_fim.after_or_equal' => 'A data de fim deve ser igual ou posterior à data de início.',
            'data_inicio.required' => 'A data de início é obrigatória.',
            'data_fim.required' => 'A data de fim é obrigatória.',
            'viajantes.required' => 'Pelo menos um viajante é obrigatório.',
            'viajantes.*.nome.required' => 'O nome do viajante é obrigatório.',
            'viajantes.*.data_nascimento.required' => 'A data de nascimento do viajante é obrigatória.',
            'destino.in' => 'O destino deve ser um dos seguintes: nacional, americas, europa.',
            'viajantes.*.adicionais.*.in' => 'Os adicionais devem ser bagagem ou esportes_aventura.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Dados inválidos.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
