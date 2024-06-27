<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductMedia;
use Illuminate\Http\Request;

class HomeController extends Controller
{
 public function home(){
     $categories = Category::all();
     $products = Product::all();
     $brands = Brand::all();

     foreach ($products as $product) {
         $productMedia = ProductMedia::where('product_id', $product->id)->where('is_main', 1)->first();

         $product->main_image = $productMedia ? $productMedia->media : null;

         // Định dạng giá tiền cho sản phẩm
         $product->formattedRegularPrice = number_format($product->regular_price, 0, ',', '.');
         $product->formattedSalePrice = number_format($product->sale_price, 0, ',', '.');
     }
     return view('layouts.home', [
         'categories' => $categories,
         'products' => $products,
         'brands' => $brands,
     ]);
 }



}
