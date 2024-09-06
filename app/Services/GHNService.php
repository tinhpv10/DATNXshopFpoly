<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GHNService{
    protected $tokenAPI;
    protected $shopId;

    public function __construct(){
        $this->tokenAPI = config('services.ghn.tokenAPI');
        $this->shopId = config('services.ghn.shopId');
    }

    /**
     * Lấy danh sách mã Tỉnh/Thành để tạo đơn hàng
     * @return array|mixed
     */
    public function GetProvince(){
        $response = Http::withHeaders([
            'token' => $this->tokenAPI,
        ])->post("https://online-gateway.ghn.vn/shiip/public-api/master-data/province", []);

        return $response->json();
    }

    /**
     * Lấy danh sách mã Quận/Huyện để tạo đơn hàng
     * @param $provinceId
     * @return array|mixed
     */
    public function GetDistrict($provinceId){
        $response = Http::withHeaders([
            'token' => $this->tokenAPI,
        ])->post("https://online-gateway.ghn.vn/shiip/public-api/master-data/district", [
            'province_id' => $provinceId
        ]);

        return $response->json();
    }

    /**
     * lấy mã Phường/Xã để tạo đơn hàng
     * @param $districtId
     * @return array|mixed
     */
    public function GetWard($districtId){
        $response = Http::withHeaders([
            'token' => $this->tokenAPI,
        ])->post("https://online-gateway.ghn.vn/shiip/public-api/master-data/ward?district_id", [
            'district_id' => $districtId
        ]);

        return $response->json();
    }

    /**
     * Sử dụng API này để tính phí dịch vụ
     * @param $formDistrictId
     * @param $formWardCode
     * @param $toDistrictId
     * @param $toWardCode
     * @param $coupon
     * @return array|mixed
     */
    public function CalculateFee($formDistrictId, $formWardCode, $toDistrictId, $toWardCode, $coupon=null){
        $response = Http::withHeaders([
            'token' => $this->tokenAPI,
            'ShopId' => $this->shopId,
        ])->post("https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/fee", [
            'from_district_id' => $formDistrictId,
            'from_ward_code' => $formWardCode,
            'to_district_id' => $toDistrictId,
            'to_ward_code' => $toWardCode,
            'service_type_id' => 2,
            'insurance_value' => 0,
            'weight' => 500,
            'coupon' => $coupon,

        ]);

        return $response->json();
    }

    public function DeliveryTime($formDistrictId, $formWardCode, $toDistrictId, $toWardCode){
        $response = Http::withHeaders([
            'token' => $this->tokenAPI,
            'ShopId' => $this->shopId,
        ])->post("https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/leadtime", [
            'from_district_id' => $formDistrictId,
            'from_ward_code' => $formWardCode,
            'to_district_id' => $toDistrictId,
            'to_ward_code' => $toWardCode,
            'service_id' => 53320,

        ]);

        return $response->json();
    }



}
