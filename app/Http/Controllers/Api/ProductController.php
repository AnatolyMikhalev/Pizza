<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        return Product::all();
        //return collection(Product::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductStoreRequest $request): ProductResource
    {
        $created_product = Product::create($request->validated());

        return new ProductResource($created_product);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return new ProductResource(Product::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductStoreRequest $request, Product $product): ProductResource
    {
        $product->update($request->validated());


        return new ProductResource($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->noContent();
    }
}
