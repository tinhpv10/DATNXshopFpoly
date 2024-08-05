<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductMedia;
use App\Models\ProductStock;
use App\Models\ProductVariation;
use App\Models\ProductVariationValue;
use App\Models\Review;
use App\Models\Shop;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Các tham số khác
        $itemsPerPage = $request->input('items_per_page', 9);
        $categoryId = $request->input('category_id');
        $brandIds = $request->input('brand_ids', []);
        $minPrice = $request->input('min_price', 0);
        $maxPrice = $request->input('max_price', 500000000); // Giá tối đa mặc định
        $sortBy = $request->input('sort', '0'); // Mặc định sắp xếp
        $ratings = $request->input('ratings', []); // Thêm tham số ratings

        $query = Product::query()->where('pause', 0);
        $query->select('*')->selectRaw('IF(sale_price IS NOT NULL, sale_price, regular_price) AS displayedPrice');

        // Điều kiện lọc danh mục
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Điều kiện lọc thương hiệu
        if (!empty($brandIds)) {
            $query->whereIn('brand_id', $brandIds);
        }

        // Điều kiện lọc giá
        if ($minPrice !== null && $maxPrice !== null) {
            $query->havingBetween('displayedPrice', [(float)$minPrice, (float)$maxPrice]);
        } elseif ($minPrice !== null) {
            $query->having('displayedPrice', '>=', (float)$minPrice);
        } elseif ($maxPrice !== null) {
            $query->having('displayedPrice', '<=', (float)$maxPrice);
        }

        if (!empty($ratings)) {
            // Lấy phần nguyên của xếp hạng
            $integerRatings = array_map(function ($rating) {
                return floor((float)$rating); // Lấy phần nguyên
            }, $ratings);

            // Lọc sản phẩm có xếp hạng lớn hơn hoặc bằng các xếp hạng đã chọn
            $query->where(function ($q) use ($integerRatings) {
                foreach ($integerRatings as $rating) {
                    $q->orWhere('rating', '>=', $rating);
                }
            });
        }

        // Điều kiện sắp xếp
        switch ($sortBy) {
            case '1':
                $query->orderBy('displayedPrice', 'asc');
                break;
            case '2':
                $query->orderBy('displayedPrice', 'desc');
                break;
            case '3':
                $query->whereNotNull('sale_price')
                    ->orderByRaw('(100 - (sale_price * 100 / regular_price)) DESC');
                break;
            default:
                $query->latest();
                break;
        }

        // Phân trang
        $products = $query->paginate($itemsPerPage)->appends($request->except('page'));

        // Dữ liệu cần thiết khác
        $Categories = Category::with('children')->whereNull('parent_id')->get();
        $Brands = Brand::all();
        $productVariations = ProductVariation::all();
        $productProductVariationValue = ProductVariationValue::all();
        $maxProductPrice = Product::max('sale_price');

        // Format lại dữ liệu sản phẩm
        foreach ($products as $product) {
            $productMedia = ProductMedia::where('product_id', $product->id)->where('is_main', 1)->first();
            $product->main_image = $productMedia ? $productMedia->media : null;
            $product->formattedRegularPrice = number_format($product->regular_price, 0, ',', '.');
            $product->formattedSalePrice = number_format($product->sale_price, 0, ',', '.');

            $product->displayedPrice = $product->sale_price ? $product->sale_price : $product->regular_price;
            $product->formattedDisplayedPrice = number_format($product->displayedPrice, 0, ',', '.');
        }

        // Trả về view
        return view('layouts.product', [
            'products' => $products,
            'Brands' => $Brands,
            'Categories' => $Categories,
            'productVariations' => $productVariations,
            'itemsPerPage' => $itemsPerPage,
            'selectedCategory' => $categoryId,
            'selectedBrands' => $brandIds,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'maxProductPrice' => $maxProductPrice,
            'sortBy' => $sortBy,
            'ratings' => $ratings, // Truyền giá trị ratings vào view
        ]);
    }


    public function search(Request $request)
    {
        $query = $request->input('query');
        $itemsPerPage = $request->input('items_per_page', 9);
        $sortBy = $request->input('sort', '0'); // Default sorting option
        $Categories = Category::with('children')->whereNull('parent_id')->get(); // Thay đổi dòng này
        $queryBuilder = Product::query();
        $queryBuilder->where('name', 'like', '%' . $query . '%')
            ->select('*')
            ->selectRaw('IF(sale_price IS NOT NULL, sale_price, regular_price) AS displayedPrice');

        switch ($sortBy) {
            case '1':
                $queryBuilder->orderBy('displayedPrice', 'asc');
                break;
            case '2':
                $queryBuilder->orderBy('displayedPrice', 'desc');
                break;
            case '3':
                $queryBuilder->whereNotNull('sale_price')
                    ->orderByRaw('(100 - (sale_price * 100 / regular_price)) DESC');
                break;
            default:
                $queryBuilder->latest();
                break;
        }

        $products = $queryBuilder->paginate($itemsPerPage)->appends($request->except('page'));

        foreach ($products as $product) {
            $productMedia = ProductMedia::where('product_id', $product->id)->where('is_main', 1)->first();
            $product->main_image = $productMedia ? $productMedia->media : null;
            $product->formattedRegularPrice = number_format($product->regular_price, 0, ',', '.');
            $product->formattedSalePrice = number_format($product->sale_price, 0, ',', '.');
            $product->displayedPrice = $product->sale_price ? $product->sale_price : $product->regular_price;
            $product->formattedDisplayedPrice = number_format($product->displayedPrice, 0, ',', '.');
        }

        $Brands = Brand::all();

        return view('layouts.product', [
            'products' => $products,
            'Brands' => $Brands,
            'Categories' => $Categories,
            'searchQuery' => $query,
            'sortBy' => $sortBy,
            'itemsPerPage' => $itemsPerPage,
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
        $products = Product::findOrFail($id);
        $products->view_count++;
        $products->save();
        $productMedia = ProductMedia::where('product_id', $products->id)->get();
        $products->main_image = $productMedia->isNotEmpty() ? $productMedia->first()->media : null;
        $formattedRegularPrice = number_format($products->regular_price, 0, ',', '.');
        $formattedSalePrice = number_format($products->sale_price, 0, ',', '.');
        $productVariations = ProductVariation::where('product_id', $products->id)->with('productVariationValue')->get();


        $favoriteProductIds = Wishlist::where('user_id', auth()->id())->pluck('product_id');
        $favoriteProducts = Product::whereIn('id', $favoriteProductIds)->get();
        foreach ($favoriteProducts as $favoriteProduct) {
            $favoriteProductMedia = ProductMedia::where('product_id', $favoriteProduct->id)->first();
            $favoriteProduct->main_image = $favoriteProductMedia ? $favoriteProductMedia->media : null;
            $favoriteProduct->formattedRegularPrice = number_format($favoriteProduct->regular_price, 0, ',', '.');
            $favoriteProduct->formattedSalePrice = number_format($favoriteProduct->sale_price, 0, ',', '.');
        }
        $isFavorite = Wishlist::where('user_id', auth()->id())->where('product_id', $id)->exists();
        $selected_variation_id = $productVariations->isEmpty() ? null : $productVariations->first()->id;
        $shop = Shop::findOrFail($products->shop_id);
        Session::forget('uploaded_files');
        //show comment
        $listComment = Review::with('user')
            ->with('reviewMedia')
            ->where('product_id', $id)
            ->paginate(5);
        return view('layouts.detail', [
            'product' => $products,
            'productMedia' => $productMedia,
            'productVariations' => $productVariations,
            'formattedRegularPrice' => $formattedRegularPrice,
            'formattedSalePrice' => $formattedSalePrice,
            'favoriteProducts' => $favoriteProducts,
            'selected_variation_id' => $selected_variation_id,
            'shop' => $shop,
            'isFavorite' => $isFavorite,
            'listComment' => $listComment,
        ]);

    }

    public function getRetailPrice(Request $request)
    {
        $selectedVariations = $request->input('selectedVariations');

        if (count($selectedVariations) == 0) {
            return response()->json(['error' => 'Cần chọn ít nhất một biến thể.'], 400);
        }

        $variationIds = array_keys($selectedVariations);
        $variationValues = array_values($selectedVariations);

        // Tìm kiếm tất cả các ProductAttribute phù hợp với biến thể đã chọn
        $productAttributes = ProductAttribute::whereIn('variation_id', $variationIds)
            ->whereHas('productVariationValue', function ($query) use ($variationValues) {
                $query->whereIn('variation_value_name', $variationValues);
            })
            ->get();

        // Tạo một mảng để lưu trữ các product_stock_id phù hợp với tất cả các biến thể
        $matchedStockIds = [];

        foreach ($productAttributes as $productAttribute) {
            $stockId = $productAttribute->product_stock_id;

            if (!isset($matchedStockIds[$stockId])) {
                $matchedStockIds[$stockId] = 0;
            }

            $matchedStockIds[$stockId]++;
        }

        // Tìm product_stock_id xuất hiện đúng số lần bằng với số lượng biến thể đã chọn
        foreach ($matchedStockIds as $stockId => $count) {
            if ($count == count($selectedVariations)) {
                $productStock = ProductStock::find($stockId);

                if ($productStock) {
                    $retailPriceFormatted = number_format($productStock->retail_price, 0, ',', '.');
                    $media = $productStock->media;

                    return response()->json([
                        'retailPriceFormatted' => $retailPriceFormatted,
                        'media' => $media,
                    ]);
                }
            }
        }

        // Nếu không tìm thấy giá và hình ảnh cho bất kỳ biến thể sản phẩm nào
        return response()->json(['error' => 'Không tìm thấy giá và hình ảnh cho biến thể sản phẩm này.'], 404);
    }


}

