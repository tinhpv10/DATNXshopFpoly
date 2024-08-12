<?php

namespace App\Listeners;

use App\Events\UploadImage;
use App\Models\ReviewMedia;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessUploadImage implements ShouldQueue
{
    /**
     * Create the event listener.
     */

    private $_API;

    public function __construct()
    {
        $this->_API = env('WEBPURIFY_API_KEY');
    }

    /**
     * Handle the event.
     */
    public function handle(UploadImage $event): void
    {
        try {
            $images = $event->images;
            $review = $event->review;

            foreach ($images as $imageData) {
                $image = $imageData['filepath'];
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

            $review->processing = false;
            $review->save();
        } catch (\Exception $e) {
            $review->delete();
            Log::error('Error_uploading_comment: ' . $e->getMessage());
        }

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
            }
            return $responseData;
        } catch (\Exception $e) {
            Log::error('Error uploading and moderating image: ' . $e->getMessage());
            return response()->json(['error' => 'Lỗi xử lý hình ảnh.'], 500);
        }
    }

    private function saveImage($url)
    {
        $imageContents = file_get_contents($url);
        $imageName = basename($url);
        $localPath = "public/commentImg/$imageName";
        Storage::put($localPath, $imageContents);

        return $imageName;
    }
}
