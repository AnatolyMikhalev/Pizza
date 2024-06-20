<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartStoreRequest;
use App\Models\Cart;
use App\Models\CartProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //Получаем текущего пользователя
        $user = auth()->user();

        //Получаем все продукты, лежащие в корзине пользователя
        $cart = Cart::where('user_id', $user->id)->with('products')->get();

        return $cart;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CartStoreRequest $request)
    {

        DB::transaction(function () use ($request) {
            $user = $request->user();

            // Найти или создать корзину для пользователя
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);

            // Добавить продукт в корзину
            foreach ($request->products as $product) CartProduct::create([
                'cart_id' => $cart->id,
                'product_id' => $product['product_id'],
                'quantity' => $product['quantity'],
            ]);

        });

        // Возврат успешного ответа
        return response()->json(['message' => 'Корзина успешно пополнена'], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        $user = auth()->user();
        $cart = Cart::where('user_id', $user->id);

        $cart->delete();

        return response()->json(['message' => 'Корзина успешно очищена.'], 204);
    }
}
