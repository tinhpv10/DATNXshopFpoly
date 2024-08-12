<?php

namespace App\Http\Controllers;

use App\Models\AppProductMedia;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopFollower;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    public function index(Request $request, $id)
    {
        $informationShop = Shop::withCount('products')->findOrFail($id);
        $categoryShop = $informationShop->Category()->orderBy('name', 'asc')->get();

        $query = Product::where('shop_id', $id);
        $productShop = $this->filter($request, $query)->paginate(4);

        foreach ($productShop as $productItem) {
            $productMedia = AppProductMedia::where('product_id', $productItem->id)->get();
            $productItem->main_image = $productMedia->isNotEmpty() ? $productMedia->first()->media : null;
        }

        return view('layouts.shop', [
            'informationShop' => $informationShop,
            'categoryShop' => $categoryShop,
            'products' => $productShop,
            'orderBy' => $request->query('orderBy'),
            'page' => $request->query('page'),
        ]);
    }

    public function getProductsByCategory(Request $request, $shopId, $categoryId)
    {
        $informationShop = Shop::withCount('products')->findOrFail($shopId);
        $categoryShop = $informationShop->Category()->orderBy('name', 'asc')->get();

        $query = Product::where('shop_id', $shopId)->where('category_id', $categoryId);
        $productShop = $this->filter($request, $query)->paginate(4);

        foreach ($productShop as $productItem) {
            $productMedia = AppProductMedia::where('product_id', $productItem->id)->get();
            $productItem->main_image = $productMedia->isNotEmpty() ? $productMedia->first()->media : null;
        }

        return view('layouts.shop', [
            'informationShop' => $informationShop,
            'categoryShop' => $categoryShop,
            'orderBy' => $request->query('orderBy'),
            'page' => $request->query('page'),
            'products' => $productShop,
        ]);
    }

    public function filter(Request $request, $query)
    {
        $page = $request->query('page');
        $orderBy = $request->query('orderBy');

        if (!$page) {
            $page = 1;
        }
        if (!$orderBy) {
            $orderBy = 'default';
        }
        $order_column = '';
        $order_value = '';

        switch ($orderBy) {
            case 'price_asc':
                $order_column = 'price';
                $order_value = 'asc';
                break;
            case 'price_desc':
                $order_column = 'price';
                $order_value = 'desc';
                break;
            case 'default':
                $order_column = 'created_at';
                $order_value = 'desc';
                break;
            default:
                $order_column = 'created_at';
                $order_value = 'desc';
        }

        $query->selectRaw('*, IF(sale_price != 0, sale_price, regular_price) AS price')
            ->orderBy($order_column, $order_value);

        return $query;
    }

    public function followShop(Request $request)
    {
        try {
            $shop_id = $request->shop_id;
            $user_id = Auth::id();

            $follow = ShopFollower::where('shop_id', $shop_id)
                ->where('user_id', $user_id)
                ->first();

            if ($follow) {
                $follow->delete();
                Shop::find($shop_id)->decrement('follower');
                $message = 'Bỏ theo dõi cửa hàng!';
            } else {
                ShopFollower::create([
                    'shop_id' => $shop_id,
                    'user_id' => $user_id,
                ]);
                Shop::find($shop_id)->increment('follower');
                $message = 'Đã theo dõi cửa hàng!';
            }

            return response()->json(['status' => 200, 'message' => $message]);
        } catch
        (\Exception $e) {
            return response()->json(['status' => 500, 'message' => $e->getMessage()]);
        }
    }

}
