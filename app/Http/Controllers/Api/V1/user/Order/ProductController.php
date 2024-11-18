<?php

namespace App\Http\Controllers\Api\V1\User\Order;

use App\Http\Controllers\Controller;
use App\Models\Product\Coupon;
use App\Models\Product\Product;
use App\Models\Product\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function getAllCategoriesAndProducts()
    {
        try {
            // Fetch all categories with their products sorted by latest
            $categories = ProductCategory::with(['products' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }])->latest()->get();

            // Return response using the helper function
            return jsonResponseWithData(true, 'Categories and products fetched successfully.', $categories);
        } catch (\Exception $e) {
            return jsonResponse(false, 'Failed to fetch categories and products.', 500);
        }
    }

    public function filterProducts(Request $request)
    {
        try {
            $searchText = $request->input('search');
            $categoryId = $request->input('category_id');

            // Fetch products with filters
            $products = Product::with('category')
                ->when($categoryId, function ($query) use ($categoryId) {
                    $query->where('category_id', $categoryId);
                })
                ->when($searchText, function ($query) use ($searchText) {
                    $query->where(function ($query) use ($searchText) {
                        $query->where('title', 'LIKE', "%$searchText%")
                            ->orWhere('description', 'LIKE', "%$searchText%")
                            ->orWhereHas('category', function ($query) use ($searchText) {
                                $query->where('name', 'LIKE', "%$searchText%");
                            });
                    });
                })
                ->where('status','active')
                ->latest()
                ->get();

            // Return response with filtered data
            return jsonResponseWithData(true, 'Filtered products fetched successfully.', $products);
        } catch (\Exception $e) {
            return jsonResponse(false, 'Failed to filter products.', 500);
        }
    }

    public function applyCoupon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coupon' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        try {
            $coupon = Coupon::where('coupon', $request->coupon)
                ->where('status', 'active')
                ->first();

            // Check if the coupon exists
            if (!$coupon) {
                return jsonResponse(false, 'Invalid or inactive coupon.', 400);
            }

            // Calculate the discount
            $discountAmount = ($request->amount * $coupon->discount) / 100;
            $finalAmount = $request->amount - $discountAmount;

            $response = [
                'original_amount' => $request->amount,
                'discount' => $coupon->discount,
                'discount_amount' => $discountAmount,
                'final_amount' => $finalAmount,
            ];

            // Return the amount with the discount
            return jsonResponseWithData(true, 'Coupon applied successfully.', $response);
        } catch (\Exception $e) {
            return jsonResponse(false, 'Failed to apply coupon.', 500);
        }
    }
}
