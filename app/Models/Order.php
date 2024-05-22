<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Relations\BelongsToMany;

class  Order extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function products(): BelongsToMany
    {
         return $this->BelongsToMany(Product::class, 'order_products');
    }
}
