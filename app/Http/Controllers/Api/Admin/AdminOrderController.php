<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderStoreRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminOrderController extends Controller
{
    public function index(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $user = auth()->user();

        $orders = Order::with('products')->get();

        return OrderResource::collection($orders);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderStoreRequest $request): \Illuminate\Http\JsonResponse
    {
        DB::transaction(function () use ($request) {
            $order = Order::create([
                'user_id' => auth()->user()->id,
                'address' => $request->address,
            ]);

            foreach ($request->products as $product) OrderProduct::create([
                'order_id' => $order->id,
                'product_id' => $product['product_id'],
                'quantity' => $product['quantity'],
            ]);
        });


        // Возврат успешного ответа
        return response()->json(['message' => 'Заказ успешно создан'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id): OrderResource
    {
        return new OrderResource(Order::with('products')->findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $order->update($request->all());

        return $order;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $order->delete();

        return response()->json(null, 204);
    }
}
