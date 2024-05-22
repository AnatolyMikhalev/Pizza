<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'address_id' => 'required', // Проверка наличия адреса
            'user_id' => 'required', // Проверка наличия пользователя
            'products' => 'required|array|min:1', // Проверка наличия продуктов и их формата
            'products.*.product_id' => 'required|exists:products,id', // Проверка наличия product_id в таблице Products
            'products.*.quantity' => 'required|integer|min:1', // Проверка количества продуктов
        ];
    }
}
