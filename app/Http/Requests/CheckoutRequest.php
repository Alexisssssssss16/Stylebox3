<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $esMiDelivery = $this->input('tipo_entrega') === 'mi_delivery';

        return [
            'tipo_entrega' => ['required', 'in:recojo_tienda,mi_delivery'],
            'payment_method_id' => ['required', 'integer', 'exists:payment_methods,id'],

            // Datos del repartidor — obligatorios cuando es "mi_delivery"
            'nombre_repartidor' => [$esMiDelivery ? 'required' : 'nullable', 'string', 'max:120'],
            'dni_repartidor' => [$esMiDelivery ? 'required' : 'nullable', 'string', 'max:20'],
            'telefono_repartidor' => [$esMiDelivery ? 'required' : 'nullable', 'string', 'max:20'],
            'empresa_delivery' => [$esMiDelivery ? 'required' : 'nullable', 'string', 'max:120'],
            'placa_vehiculo' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'tipo_entrega.required' => 'Debes seleccionar un tipo de entrega.',
            'tipo_entrega.in' => 'Tipo de entrega no válido.',
            'payment_method_id.required' => 'Selecciona un método de pago.',
            'payment_method_id.exists' => 'Método de pago no válido.',
            'nombre_repartidor.required' => 'El nombre del repartidor es obligatorio.',
            'dni_repartidor.required' => 'El DNI o identificación del repartidor es obligatorio.',
            'telefono_repartidor.required' => 'El teléfono del repartidor es obligatorio.',
            'empresa_delivery.required' => 'La empresa de delivery es obligatoria.',
        ];
    }
}
