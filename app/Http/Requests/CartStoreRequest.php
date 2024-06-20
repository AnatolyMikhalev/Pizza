<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'products' => 'required|array|min:1', // Проверка наличия продуктов и их формата
            'products.*.product_id' => 'required|exists:products,id', // Проверка наличия product_id в таблице Products
            'products.*.quantity' => 'required|integer|min:1', // Проверка количества продуктов
        ];
    }


    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $products = $this->input('products', []);
            $pizzas = 0;
            $beverages = 0;

            foreach ($products as $product) {
                $productModel = \App\Models\Product::find($product['product_id']);
                if ($productModel->type === 'Pizza') {
                    $pizzas += $product['quantity'];
                } elseif ($productModel->type === 'Beverage') {
                    $beverages += $product['quantity'];
                }
            }

            if ($pizzas > 10) {
                $validator->errors()->add('products', 'You cannot order more than 10 Pizza products.');
            }

            if ($beverages > 20) {
                $validator->errors()->add('products', 'You cannot order more than 20 Beverage products.');
            }
        });
    }
}
