<?php

namespace App\Http\Controllers;

use App\Models\AppProductMedia;
use App\Models\AppProductStock;
use App\Models\AppProductVariation;
use App\Models\AppProductVariationValue;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\Review;
use App\Models\Shop;
use App\Models\Wishlist;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
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
        $view = $request->input('view', 'grid'); // Thêm tham số view, mặc định là 'grid'
        $queryText = $request->input('query'); // Thêm tham số query để tìm kiếm

        $query = Product::query()->where('pause', 0);
        $query->select('*')->selectRaw('IF(sale_price IS NOT NULL, sale_price, regular_price) AS displayedPrice');

        $imageProducts = $this->handleImageSearch($request->file('image'));

        // Thêm điều kiện từ tìm kiếm bằng hình ảnh
        if ($imageProducts->isNotEmpty()) {
            $query->whereIn('id', $imageProducts->pluck('id'));
        }

        // Điều kiện tìm kiếm
        if ($queryText) {
            $query->where(function ($q) use ($queryText) {
                $q->where('name', 'like', '%' . $queryText . '%')
                    ->orWhereHas('shop', function ($q) use ($queryText) {
                        $q->where('name', 'like', '%' . $queryText . '%');
                    });
            });
        }

        // Điều kiện lọc danh mục
        if ($categoryId) {
            // Lấy danh mục
            $category = Category::find($categoryId);
            if ($category) {
                // Lấy tất cả ID danh mục con và danh mục cha
                $categoryIds = $category->getAllDescendantIds();
                $query->whereIn('category_id', $categoryIds);
            }
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

        // Điều kiện lọc xếp hạng
        if (!empty($ratings)) {
            $integerRatings = array_map(function ($rating) {
                return floor((float)$rating); // Lấy phần nguyên
            }, $ratings);

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

        // Tìm kiếm shop
        $shops = Shop::where('name', 'like', '%' . $queryText . '%')->get(['id', 'name', 'avatar']);


        // Dữ liệu cần thiết khác
        $Categories = Category::with('children')->whereNull('parent_id')->get();
        $Brands = Brand::all();
        $productVariations = AppProductVariation::all();
        $productProductVariationValue = AppProductVariationValue::all();
        $maxProductPrice = Product::max('sale_price');

        // Format lại dữ liệu sản phẩm
        foreach ($products as $product) {
            $productMedia = AppProductMedia::where('product_id', $product->id)->where('is_main', 1)->first();
            $product->main_image = $productMedia ? $productMedia->media : null;
            $product->formattedRegularPrice = number_format($product->regular_price, 0, ',', '.');
            $product->formattedSalePrice = number_format($product->sale_price, 0, ',', '.');
            $product->displayedPrice = $product->sale_price ? $product->sale_price : $product->regular_price;
            $product->formattedDisplayedPrice = number_format($product->displayedPrice, 0, ',', '.');
            $product->sold_count = $product->sold_count;
        }

        // Trả về view
        return view('layouts.product', [
            'products' => $products,
            'shops' => $shops, // Truyền danh sách shop vào view
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
            'view' => $view, // Truyền giá trị view vào view
            'searchQuery' => $queryText, // Truyền giá trị query vào view
        ]);
    }


    protected function handleImageSearch($image)
    {
        if (!$image) {
            return collect(); // Trả về collection rỗng nếu không có ảnh
        }

        // Lưu trữ ảnh tạm thời
        $imagePath = $image->store('uploads', 'public');

        // Gọi hàm nhận diện sản phẩm bằng hình ảnh
        return $this->identifyProductsByImage($imagePath);
    }

    protected function identifyProductsByImage($imagePath)
    {
        // Tạo client Google Vision
        $client = new ImageAnnotatorClient();
        $image = file_get_contents($imagePath);
        $response = $client->labelDetection(['image' => ['content' => $image]]);
        $labels = $response->getLabelAnnotations();

        // Tạo mảng chứa ID sản phẩm
        $productIds = [];
        foreach ($labels as $label) {
            $productName = $label->getDescription();
            $products = Product::where('name', 'like', '%' . $productName . '%')->get();

            // Lặp qua các sản phẩm tìm được và kiểm tra ảnh trong ProductMedia
            foreach ($products as $product) {
                $productMedia = AppProductMedia::where('product_id', $product->id)->first();
                if ($productMedia) {
                    $productIds[] = $product->id; // Thêm ID sản phẩm vào mảng nếu có ảnh
                }
            }
        }

        return Product::whereIn('id', $productIds)->get();
    }


    public function showByCategory($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        $products = Product::where('category_id', $category->id)->get();
        $Brands = Brand::all();
        foreach ($products as $product) {
            $productMedia = AppProductMedia::where('product_id', $product->id)->where('is_main', 1)->first();
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
        $productMedia = AppProductMedia::where('product_id', $products->id)->get();
        $products->main_image = $productMedia->isNotEmpty() ? $productMedia->first()->media : null;
        $formattedRegularPrice = number_format($products->regular_price, 0, ',', '.');
        $formattedSalePrice = number_format($products->sale_price, 0, ',', '.');
        $productVariations = AppProductVariation::where('product_id', $products->id)->with('appProductVariationValue')->get();


        $favoriteProductIds = Wishlist::where('user_id', auth()->id())->pluck('product_id');
        $favoriteProducts = Product::whereIn('id', $favoriteProductIds)->get();
        foreach ($favoriteProducts as $favoriteProduct) {
            $favoriteProductMedia = AppProductMedia::where('product_id', $favoriteProduct->id)->first();
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
            ->where('processing', false)
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

    public function showPost()
    {
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
            ->whereHas('appProductVariationValue', function ($query) use ($variationValues) {
                $query->whereIn('variation_value_name', $variationValues);
            })
            ->get();

        // Tạo một mảng để lưu trữ các product_stock_id phù hợp với tất cả các biến thể
        $matchedStockIds = [];

        foreach ($productAttributes as $productAttribute) {
            $stockId = $productAttribute->app_product_stock_id;

            if (!isset($matchedStockIds[$stockId])) {
                $matchedStockIds[$stockId] = 0;
            }

            $matchedStockIds[$stockId]++;
        }

        // Tìm product_stock_id xuất hiện đúng số lần bằng với số lượng biến thể đã chọn
        foreach ($matchedStockIds as $stockId => $count) {
            if ($count == count($selectedVariations)) {
                $productStock = AppProductStock::find($stockId);

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

