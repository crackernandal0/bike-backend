<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // Relation to Products
    public function products()
    {
        return $this->hasMany(Product::class,'category_id');
    }
}
