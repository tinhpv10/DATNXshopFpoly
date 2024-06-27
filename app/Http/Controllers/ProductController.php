<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductMedia;
use App\Models\ProductVariation;
use App\Models\ProductVariationValue;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $itemsPerPage = $request->input('items_per_page', 9);
        $products = Product::paginate($itemsPerPage);
        $Categories = Category::all();
        $Brands = Brand::all();
        $productVariations = ProductVariation::all();
        $productProductVariationValue = ProductVariationValue::all();
        foreach ($products as $product) {
            $productMedia = ProductMedia::where('product_id', $product->id)->where('is_main', 1)->first();
            $product->main_image = $productMedia ? $productMedia->media : null;
            $product->formattedRegularPrice = number_format($product->regular_price, 0, ',', '.');
            $product->formattedSalePrice = number_format($product->sale_price, 0, ',', '.');
        }
        return view('layouts.product', [
            'products' => $products,
            'Brands' => $Brands,
            'Categories' => $Categories,

        ]);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $Categories = Category::where('name', 'like', '%' . $query . '%')->get();
        $products = Product::where('name', 'like', '%' . $query . '%')->get();
        $Brands = Brand::all();
        foreach ($products as $product) {
            $productMedia = ProductMedia::where('product_id', $product->id)->where('is_main', 1)->first();
            $product->main_image = $productMedia ? $productMedia->media : null;
            $product->formattedRegularPrice = number_format($product->regular_price, 0, ',', '.');
            $product->formattedSalePrice = number_format($product->sale_price, 0, ',', '.');
        }

        return view('layouts.product', [
            'products' => $products,
            'Brands' => $Brands,
            'Categories' => $Categories,
            'searchQuery' => $query,
        ]);
    }

    public function showByCategory($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        $products = Product::where('category_id', $category->id)->get();
        $Brands = Brand::all();
        foreach ($products as $product) {
            $productMedia = ProductMedia::where('product_id', $product->id)->where('is_main', 1)->first();
            $product->main_image = $productMedia ? $productMedia->media : null;
            $product->formattedRegularPrice = number_format($product->regular_price, 0, ',', '.');
            $product->formattedSalePrice = number_format($product->sale_price, 0, ',', '.');
        }
        return view('layouts.product', [
            'Categories' => Category::all(),
            'products' => $products,
            'Brands' => $Brands,
            'selectedCategory' => $category
        ]);
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        $product->view_count++;
        $product->save();
        $productMedia = ProductMedia::where('product_id', $product->id)->get();
        $product->main_image = $productMedia->isNotEmpty() ? $productMedia->first()->media : null;
        $formattedRegularPrice = number_format($product->regular_price, 0, ',', '.');
        $formattedSalePrice = number_format($product->sale_price, 0, ',', '.');
        $productVariations = ProductVariation::where('product_id', $product->id)->with('productVariationValue')->get();


        $favoriteProductIds = Wishlist::where('user_id', auth()->id())->pluck('product_id');
        $favoriteProducts = Product::whereIn('id', $favoriteProductIds)->get();
        foreach ($favoriteProducts as $favoriteProduct) {
            $favoriteProductMedia = ProductMedia::where('product_id', $favoriteProduct->id)->first();
            $favoriteProduct->main_image = $favoriteProductMedia ? $favoriteProductMedia->media : null;
            $favoriteProduct->formattedRegularPrice = number_format($favoriteProduct->regular_price, 0, ',', '.');
            $favoriteProduct->formattedSalePrice = number_format($favoriteProduct->sale_price, 0, ',', '.');
        }
        return view('layouts.product', [
            'products' => $products,
            'Brands' => $Brands,
            'Categories' => $Categories,
            'productVariations' => $productVariations],
            compact('products', 'itemsPerPage'));
    }

    public function filter(Request $request)
    {
        $categoryId = $request->input('category_id');
        $brandId = $request->input('brand_id');

        $query = Product::query();

        if ($categoryId) {
            $query->whereHas('category', function ($q) use ($categoryId) {
                $q->where('id', $categoryId);
            });
        }

        if ($brandId) {
            $query->whereHas('brand', function ($q) use ($brandId) {
                $q->where('id', $brandId);
            });
        }

        $products = $query->paginate(9);

        return view('layouts.product', compact('products', 'Categories', 'Brands'));

        return view('layouts.detail', [
            'product' => $product,
            'productMedia' => $productMedia,
            'productVariations' => $productVariations,
            'formattedRegularPrice' => $formattedRegularPrice,
            'formattedSalePrice' => $formattedSalePrice,
            'favoriteProducts' => $favoriteProducts,

        ]);

    }


    public function addToCart(Request $request)
    {
        $productId = $request->input('product_id');
        $product = Product::findOrFail($productId);
        $productMedia = ProductMedia::where('product_id', $product->id)->where('is_main', 1)->first();
        $productImage = $productMedia ? $productMedia->media : null;

        $productVariation = ProductVariation::where('product_id', $productId)->first();
        $variationName = null;
        $variationValueName = null;
        if ($productVariation) {
            $variationName = $productVariation->variation_name;
            $variationValue = ProductVariationValue::where('product_variation_id', $productVariation->id)->first();
            if ($variationValue) {
                $variationValueName = $variationValue->variation_value_name;
            }
        }
        $cartItems = Session::get('cartItems', []);
        $found = false;
        foreach ($cartItems as &$cartItem) {
            if ($cartItem['id'] == $product->id) {
                $cartItem['quantity'] += 1;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $cartItems[] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->sale_price,
                'quantity' => 1,
                'image' => $productImage,
                'variation_name' => $variationName,
                'variation_value_name' => $variationValueName,
            ];
        }
        Session::put('cartItems', $cartItems);
        return redirect()->route('cart.view');
    }

