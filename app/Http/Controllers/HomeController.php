<?php

namespace App\Http\Controllers;

use App\Models\AppProductMedia;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function home()
    {
        // Lấy tất cả danh mục và thương hiệu
        $categories = Category::all();
        $brands = Brand::all();

        // Lấy tất cả sản phẩm và định dạng giá
        $products = Product::all()->where('pause', 0);;
        foreach ($products as $product) {
            $productMedia = AppProductMedia::where('product_id', $product->id)->where('is_main', 1)->first();
            // Sử dụng tên cột chính xác ở đây, nếu là 'image' thay vì 'main_image'
            $product->main_image = $productMedia ? $productMedia->media : null;

            // Định dạng giá tiền cho sản phẩm
            $product->formattedRegularPrice = number_format($product->regular_price, 0, ',', '.');
            $product->formattedSalePrice = number_format($product->sale_price, 0, ',', '.');
        }

        // Lấy giá trị sold_count cao nhất
        $maxSoldCount = Product::max('sold_count');

        // Lấy danh sách sản phẩm đề xuất
        $recommendedProducts = Product::select('id', 'name', 'description', 'regular_price', 'sale_price', 'sold_count', 'rating')->where('pause', 0)
            ->selectRaw('((rating * 100) / 5) as rating_percentage')
            ->selectRaw('((sold_count * 100) / ?) as sold_percentage', [$maxSoldCount])
            ->selectRaw('((rating * 100) / 5) + ((sold_count * 100) / ?) as total_percentage', [$maxSoldCount])
            ->orderBy('total_percentage', 'DESC') // Sắp xếp theo tổng phần trăm
            ->take(20) // Lấy 10 sản phẩm
            ->get();

        // Định dạng giá cho sản phẩm đề xuất
        foreach ($recommendedProducts as $product) {
            $productMedia = AppProductMedia::where('product_id', $product->id)->where('is_main', 1)->first();
            // Sử dụng tên cột chính xác ở đây
            $product->main_image = $productMedia ? $productMedia->media : null;

            // Định dạng giá tiền cho sản phẩm
            $product->formattedRegularPrice = number_format($product->regular_price, 0, ',', '.');
            $product->formattedSalePrice = number_format($product->sale_price, 0, ',', '.');
        }
        $categoryBanners = Category::whereNull('parent_id')
            ->withCount('products')
            ->orderBy('products_count', 'desc')
            ->take(9)
            ->get();
        // Trả về view với các biến đã chuẩn bị
        return view('layouts.home', [
            'categories' => $categories,
            'products' => $products,
            'brands' => $brands,
            'recommendedProducts' => $recommendedProducts,
            'categoryBanners' => $categoryBanners,// Thêm sản phẩm đề xuất vào view
        ]);
    }
}
