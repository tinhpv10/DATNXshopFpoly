<?php

namespace App\Http\Controllers;

use App\Models\AppProductMedia;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        try {
            $user_id = Auth::user()->id;
            $products = Product::whereIn('id', function ($query) use ($user_id) {
                $query->select('product_id')
                    ->from('wishlist')
                    ->where('user_id', $user_id);
            })->paginate(12);

            foreach ($products as $productItem) {
                $productMedia = AppProductMedia::where('product_id', $productItem->id)->first();
                $productItem->main_image = $productMedia ? $productMedia->media : null;
            }

            return view('layouts.wishlist', [
                'products' => $products,
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => $e->getMessage()]);
        }
    }


    public function insertWishlist(Request $request)
    {
        try {
            $wishlistItem = Wishlist::where('user_id', Auth::user()->id)
                ->where('product_id', $request->id)
                ->first();

            if ($wishlistItem) {
                $wishlistItem->delete();
                $message = 'Xóa khỏi danh sách yêu thích thành công!';
            } else {
                Wishlist::create([
                    'name' => $request->name,
                    'user_id' => Auth::user()->id,
                    'product_id' => $request->id
                ]);
                $message = 'Thêm vào danh sách yêu thích thành công!';
            }
            return response()->json(['status' => 200, 'message' => $message]);

        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function countWishlist()
    {
        try {
            if (!Auth::check()) {
                $count = 0;
            } else {
                $user_id = Auth::user()->id;
                $count = Wishlist::where('user_id', $user_id)->count();
            }

            return response()->json(['status' => 200, 'count' => $count]);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'count' => 0]);
        }
    }

    public function toggle($productId)
    {
        $userId = Auth::id();
        $product = Product::findOrFail($productId);

        $wishlistItem = Wishlist::where('user_id', $userId)->where('product_id', $productId)->first();

        if ($wishlistItem) {
            $wishlistItem->delete();
            return redirect()->back()->with('success', 'Product removed from wishlist');
        } else {
            Wishlist::create([
                'name' => $product->name,
                'user_id' => $userId,
                'product_id' => $productId,
            ]);
            return redirect()->back()->with('success', 'Product added to wishlist');
        }
    }


}
