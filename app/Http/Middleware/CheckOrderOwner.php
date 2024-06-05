<?php

namespace App\Http\Middleware;

use App\Models\Order;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckOrderOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $orderId = $request->route('order');

        $user = $request->user();

        $order = Order::findOrFail($orderId);

        if (!$order || (int) $order->user_id != (int) $user->id) {
            if (!$order)
                return response($order . 'doesn\'t exist', Response::HTTP_NOT_FOUND);
            if ($order->user_id != $user->id)
                return response($order->user_id . '!=' . $user->id, Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
