<?php

namespace App\Models\Product;

use App\Models\Product\ProductCategory;
use App\Models\Service\Zone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'image',
        'description',
        'available_quantity',
        'used_quantity',
        'price',
        'delivery_fee',
        'zone_id',
        'status'
    ];

    // Relation to Product Category
    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    // Relation to Zone
    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
}
