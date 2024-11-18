<?php

namespace Database\Seeders;

use App\Models\Product\Coupon;
use App\Models\Product\Product;
use App\Models\Product\ProductCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Sanitary Pads'],
            ['name' => 'Menstrual Cups'],
            ['name' => 'Tampons'],
            ['name' => 'Period Underwear'],
            ['name' => 'Heating Pads'],
            ['name' => 'Pain Relief Patches'],
            ['name' => 'Period Trackers'],
        ];

        foreach ($categories as $category) {
            ProductCategory::create($category);
        }

        $products = [
            [
                'category_id' => ProductCategory::where('name', 'Sanitary Pads')->first()->id,
                'title' => 'Ultra Thin Sanitary Pads',
                'description' => 'Comfortable and leak-proof sanitary pads.',
                'image' => 'media/products/sanitary_pads.png',
                'available_quantity' => 100,
                'price' => 3.99,
                'delivery_fee' => 1.50,
                'zone_id' => 10, // Ensure zones are already populated
                'status' => 'active',
            ],
            [
                'category_id' => ProductCategory::where('name', 'Menstrual Cups')->first()->id,
                'title' => 'Reusable Menstrual Cup',
                'description' => 'Eco-friendly menstrual cup for easy use.',
                'image' => 'media/products/sanitary_pads.png',
                'available_quantity' => 50,
                'price' => 12.99,
                'delivery_fee' => 2.00,
                'zone_id' => 10,
                'status' => 'active',
            ],
            [
                'category_id' => ProductCategory::where('name', 'Tampons')->first()->id,
                'title' => 'Organic Cotton Tampons',
                'description' => 'Hypoallergenic tampons made from organic cotton.',
                'image' => 'media/products/sanitary_pads.png',
                'available_quantity' => 75,
                'price' => 6.49,
                'delivery_fee' => 1.50,
                'zone_id' => 10,
                'status' => 'active',
            ],
            [
                'category_id' => ProductCategory::where('name', 'Period Underwear')->first()->id,
                'title' => 'Leak-Proof Period Underwear',
                'description' => 'Comfortable period underwear with leak-proof technology.',
                'image' => 'media/products/sanitary_pads.png',
                'available_quantity' => 30,
                'price' => 15.99,
                'delivery_fee' => 2.50,
                'zone_id' => 10,
                'status' => 'active',
            ],
            [
                'category_id' => ProductCategory::where('name', 'Heating Pads')->first()->id,
                'title' => 'Electric Heating Pad for Cramps',
                'description' => 'Heating pad to relieve menstrual cramps.',
                'image' => 'media/products/sanitary_pads.png',
                'available_quantity' => 40,
                'price' => 19.99,
                'delivery_fee' => 3.00,
                'zone_id' => 10,
                'status' => 'active',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        $coupons = [
            ['coupon' => 'Femi', 'discount' => 10, 'status' => 'active'],
        ];

        foreach ($coupons as $coupon) {
            Coupon::create($coupon);
        }
    }
}
