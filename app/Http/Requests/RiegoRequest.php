<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RiegoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha' => ['required', 'date', 'before_or_equal:today'],
            'duracion_min' => ['required', 'integer', 'min:1'],
            'cantidad_litros' => ['required', 'numeric', 'gt:0'],
            'observaciones' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'fecha.required' => 'Ingresa la fecha.',
            'fecha.date' => 'La fecha no es válida.',
            'fecha.before_or_equal' => 'La fecha no puede ser futura.',
            'duracion_min.required' => 'Ingresa minutos enteros mayores a 0.',
            'duracion_min.integer' => 'Ingresa minutos enteros mayores a 0.',
            'duracion_min.min' => 'Ingresa minutos enteros mayores a 0.',
            'cantidad_litros.required' => 'Ingresa litros mayores a 0.',
            'cantidad_litros.numeric' => 'Ingresa litros mayores a 0.',
            'cantidad_litros.gt' => 'Ingresa litros mayores a 0.',
            'observaciones.max' => 'Las observaciones admiten hasta 255 caracteres.',
        ];
    }
}
