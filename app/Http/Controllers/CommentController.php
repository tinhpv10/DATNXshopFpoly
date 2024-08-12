<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Product;
use App\Models\Review;
use App\Models\ReviewMedia;
use App\Models\User;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class CommentController extends Controller
{
    private $_API;

    public function __construct()
    {
        $this->_API = env('WEBPURIFY_API_KEY');
    }

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

            return response()->json(['success' => true, 'message' => 'File information saved successfullyyyyy.', 'files' => $uploaded_files]);
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
        if (Auth::check()) {
            $user_id = Auth::user()->id;
        } else {
            // Tạo người dùng mới nếu chưa đăng nhập
            $user = User::create([
                'name' => $request->user_name,
                'email' => $request->email,
                'password' => $request->password,
            ]);
            $user_id = $user->id;
        }

        // Tạo một review mới
        $review = Review::create([
            'content' => $request->comment_content,
            'rating' => $request->rating,
            'user_id' => $user_id,
            'product_id' => $product_id,
        ]);

        // Cập nhật rating cho sản phẩm
        $this->updateProductRating($product_id);

        $listImage = session('uploaded_files', []);

        // Xử lý hình ảnh (không thay đổi)
        for ($i = 0; $i < sizeof($listImage); $i++) {
            $image = $listImage[$i]['filepath'];
            $moderationResult = Cloudinary::upload($image)->getSecurePath();

            $resultId = $this->checkUrlImg($moderationResult);
            $resultStatus = $this->checkStatusImg($resultId);

            if ($resultStatus['rsp']['status'] === 'declined') {
                $blurImgUrl = Cloudinary::upload($image, [
                    'transformation' => [
                        'effect' => 'blur:500'
                    ]
                ])->getSecurePath();
            } else {
                $blurImgUrl = $moderationResult;
            }

            $imageName = $this->saveImage($blurImgUrl);
            $imageDB = asset('storage/commentImg/' . $imageName);

            ReviewMedia::create([
                'review_id' => $review->id,
                'review_media' => $imageDB,
            ]);
        }

        Session::forget('uploaded_files');

        return redirect()->route('product.detail', $product_id);
    }

    private function updateProductRating($productId)
    {
        // Lấy sản phẩm theo ID
        $product = Product::findOrFail($productId);

        // Lấy tất cả các review của sản phẩm đó
        $reviews = Review::where('product_id', $productId)->get();

        // Kiểm tra xem có review nào không
        if ($reviews->isEmpty()) {
            // Nếu không có review, đặt rating là 0
            $product->rating = 0.0; // Sử dụng số thực
        } else {
            // Tính tổng số sao
            $totalRating = $reviews->sum('rating');
            // Lấy số lượng đánh giá
            $numberOfReviews = $reviews->count();
            // Tính trung bình cộng rating
            $averageRating = $totalRating / $numberOfReviews;
            // Cập nhật rating cho sản phẩm
            $product->rating = (float)$averageRating; // Đảm bảo là số thực
        }

        // Lưu sản phẩm với rating mới
        $product->save();
    }




    private function checkUrlImg($imageUrl)
    {
        $response = Http::post("https://im-api1.webpurify.com/services/rest/?method=webpurify.live.imgcheck&api_key=$this->_API&imgurl=$imageUrl&format=json");
        $responseData = $response->json();
        $imageId = $responseData['rsp']['imgid'];

        return $imageId;
    }

    private function checkStatusImg($imageId, $waitTimeInSeconds = 15)
    {
        //$retryCount: số lần thử
        //$waitTimeInSeconds: thời gian chờ giữa các lần thử
        try {
            $status = 'pending';
            $attempt = 0;

            while ($status === 'pending') {
                $response = Http::post("https://im-api1.webpurify.com/services/rest/?method=webpurify.live.imgstatus&api_key=$this->_API&imgid=$imageId&format=json");
                $responseData = $response->json();

                if (isset($responseData['rsp']['status'])) {
                    $status = $responseData['rsp']['status'];
                } else {
                    $status = 'error';
                    break;
                }
                if ($status === 'pending') {
                    sleep($waitTimeInSeconds);
                }
//                $attempt++;
            }
            return $responseData;

        } catch (\Exception $e) {
            Log::error('Error uploading and moderating image: ' . $e->getMessage());
            return response()->json(['error' => 'Lỗi xử lý hình ảnh.'], 500);
        }

    }

    private function saveImage($url)
    {
        // Tải nội dung hình ảnh từ URL
        $imageContents = file_get_contents($url);

        // Lấy tên hình ảnh từ URL
        $imageName = basename($url);

        // Tạo đường dẫn lưu trữ hình ảnh trong thư mục public/imgUpload
        $localPath = "public/commentImg/$imageName";

        // Lưu hình ảnh vào thư mục storage/app/public/imgUpload
        Storage::put($localPath, $imageContents);

        // Trả về đường dẫn lưu trữ cục bộ của hình ảnh
        return $imageName;
    }

    public function like(Review $review)
    {
        try {
            // Tăng giá trị cột 'like_count' lên 1 và lưu vào cơ sở dữ liệu
            $review->increment('like_count');
            $review->save();

            return response()->json([
                'success' => true,
                'like_count' => $review->like_count,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tăng like count: ' . $e->getMessage(),
            ]);
        }
    }



}
