<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ParcelaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100'],
            'ubicacion' => ['nullable', 'string', 'max:150'],
            'area_m2' => ['nullable', 'numeric', 'gt:0'],
            'tipo_cultivo' => ['nullable', 'string', 'max:80'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'Ingresa el nombre de la parcela.',
            'nombre.max' => 'El nombre admite hasta 100 caracteres.',
            'ubicacion.max' => 'La ubicación admite hasta 150 caracteres.',
            'area_m2.numeric' => 'El área debe ser un número.',
            'area_m2.gt' => 'El área debe ser mayor a 0.',
            'tipo_cultivo.max' => 'El cultivo admite hasta 80 caracteres.',
        ];
    }
}
