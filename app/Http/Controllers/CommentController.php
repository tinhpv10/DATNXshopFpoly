<?php

namespace App\Http\Controllers;

use App\Events\UploadImage;
use App\Http\Requests\CommentRequest;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class CommentController extends Controller
{
    public function uploadImage(Request $request)
    {
        try {
            $filename = $request->input('filename');
            $filepath = $request->input('filepath');

            if (!Session::has('uploaded_files')) {
                Session::put('uploaded_files', []);
            }

            $uploaded_files = Session::get('uploaded_files');
            $uploaded_files[] = [
                'filename' => $filename,
                'filepath' => $filepath,
            ];
            Session::put('uploaded_files', $uploaded_files);
            Log::info('Uploaded files:', $uploaded_files);

            return response()->json(['success' => true, 'message' => 'File information saved successfully.', 'files' => $uploaded_files]);
        } catch (\Exception $e) {
            Log::error('Error uploading image:', ['message' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error uploading image.'], 500);
        }
    }

    public function deleteImage(Request $request)
    {
        try {
            $filename = $request->input('filename');
            // Lấy danh sách các tệp đã tải lên từ session
            $uploaded_files = session('uploaded_files', []);
            // Log danh sách các tệp hiện tại trong session trước khi xóa
            Log::info('Current uploaded files:', $uploaded_files);
            // Tìm tệp cần xóa bằng cách lọc mảng
            $fileToDelete = array_filter($uploaded_files, function ($file) use ($filename) {
                return $file['filename'] === $filename;
            });
            // Log thông tin về tệp sẽ được xóa
            Log::info('File to be deleted:', $fileToDelete);

            // Lọc ra các tệp không khớp với tên tệp cần xóa
            $uploaded_files = array_filter($uploaded_files, function ($file) use ($filename) {
                return $file['filename'] !== $filename;
            });

            $uploaded_files = array_values($uploaded_files);
            session(['uploaded_files' => $uploaded_files]);
            Log::info('Updated uploaded files:', $uploaded_files);

            return response()->json(['success' => true, 'message' => 'File removed from session.', 'files' => $uploaded_files]);
        } catch (\Exception $e) {
            Log::error('Error deleting image:', ['message' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error deleting image.'], 500);
        }
    }

    public function uploadComment(CommentRequest $request)
    {
        set_time_limit(250);
        $product_id = $request->product_id;

        DB::beginTransaction();

        try {
            if (Auth::check()) {
                $user_id = Auth::user()->id;
            } else {
                // Tạo người dùng mới nếu chưa đăng nhập
                $user = User::create([
                    'name' => $request->user_name,
                    'email' => $request->email,
                    'password' => bcrypt($request->password), // Mã hóa mật khẩu
                ]);
                $user_id = $user->id;
            }

            $review = Review::create([
                'content' => $request->comment_content,
                'rating' => $request->rating,
                'user_id' => $user_id,
                'product_id' => $product_id,
                'processing' => !empty(session('uploaded_files', [])),
            ]);

            // Cập nhật rating cho sản phẩm
            $this->updateProductRating($product_id);

            $listImage = session('uploaded_files', []);

            if (!empty($listImage)) {
                $message = 'Đánh giá của bạn đã được gửi đi chờ kiểm duyệt';
                event(new UploadImage($listImage, $review));
            } else {
                $message = '';
            }

            DB::commit();
            Session::forget('uploaded_files');

            return redirect()->route('product.detail', $product_id)->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error uploading comment: ' . $e->getMessage());
            Session::forget('uploaded_files');

            return redirect()->route('product.detail', $product_id)->with('error', 'Có lỗi xảy ra khi gửi đánh giá của bạn.');
        }
    }

    protected function updateProductRating($product_id)
    {
        // Lấy tất cả các đánh giá cho sản phẩm
        $reviews = Review::where('product_id', $product_id)->get();

        if ($reviews->count() > 0) {
            // Tính toán rating trung bình
            $averageRating = $reviews->avg('rating');

            // Cập nhật sản phẩm với rating mới
            $product = Product::find($product_id);
            $product->rating = $averageRating; // Giả sử có cột rating trong bảng products
            $product->save();
        }
    }
}
