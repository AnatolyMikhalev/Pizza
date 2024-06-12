<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminProductController extends Controller
{
    public function index(ProductStoreRequest $request)
    {
        return Product::all();
        //return collection(Product::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductStoreRequest $request): ProductResource
    {
        $data = $request->validated();

        if(isset($data['image'])){
            $path = $data['image']->store('images', 'public');
            $data['image_url'] = $path;
        }

        unset($data['image']);

        $createdProduct = Product::create($data);

        return new ProductResource($createdProduct);
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
    public function update(Request $request, Product $product)
    {
        $data = $request->all();

        if (isset($data['image'])) {
            if (isset($product['image_url'])) {
                Storage::delete($product['image_url']);
            }

            $path = $data['image']->store('images', 'public');
            $data['image_url'] = $path;
        }

        unset($data['image']);

        $product->update($data);

        return $product;
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
