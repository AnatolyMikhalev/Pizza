<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $guarded = [];

    use HasFactory;

    public function products()
    {
        return $this->BelongsToMany(Product::class, 'cart_products');
    }
}
