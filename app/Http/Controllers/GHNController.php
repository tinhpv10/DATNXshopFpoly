<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\GHNService;
class GHNController extends Controller
{
    protected $ghnService;
    public function __construct(GHNService $ghnService){
        $this->ghnService = $ghnService;
    }

    public function calculateShipping(Request $request){

        $toProvinceName = $request->input('to_provinceName');
        $toDistrictName = $request->input('to_districtName');
        $toWardName = $request->input('to_wardName');

        $formProvinceName = $request->input('form_provinceName');
        $formDistrictName = $request->input('form_districtName');
        $formWardName = $request->input('form_wardName');

        //lấy id cửa hàng của Sản phẩm
        $shop_id = $request->input('shop_id');

        // Lấy ID tỉnh, huyện, xã cho địa chỉ nhận
        $toProvinceId = $this->provinceId($toProvinceName);
        $toDistrictId = $this->districtId($toProvinceId, $toDistrictName);
        $toWardCode = $this->wardCode($toDistrictId, $toWardName);

        // Lấy ID tỉnh, huyện, xã cho địa chỉ gửi
        $formProvinceId = $this->provinceId($formProvinceName);
        $formDistrictId = $this->districtId($formProvinceId, $formDistrictName);
        $formWardCode = $this->wardCode($formDistrictId, $formWardName);

        //Tính phí giao hàng
        $shippingFree =  $this->ghnService->CalculateFee($formDistrictId, $formWardCode, $toDistrictId, $toWardCode);
        dd($shippingFree);

        //Tính thời gian
        $time = $this->ghnService->DeliveryTime($formDistrictId, $formWardCode, $toDistrictId, $toWardCode);

        $number = $time['data']['leadtime'];
        $data = date('d-m-Y H:i:s', $number);


        return response()->json([
            'status' => 200,
            'shippingFree' => $shippingFree['data']['total'],
            'formDistrictId' => $formDistrictId,
            'formWardCode' => $formWardCode,
            'toDistrictId' => $toDistrictId,
            'toWardCode' => $toWardCode,
            'time' => $number,
            'data' => $data,
        ]);

    }

    public function calculateTime(Request $request){
        $toProvinceName = $request->input('to_provinceName');
        $toDistrictName = $request->input('to_districtName');
        $toWardName = $request->input('to_wardName');

        $formProvinceName = $request->input('form_provinceName');
        $formDistrictName = $request->input('form_districtName');
        $formWardName = $request->input('form_wardName');

        // Lấy ID tỉnh, huyện, xã theo API GHN cho địa chỉ nhận
        $toProvinceId = $this->provinceId($toProvinceName);
        $toDistrictId = $this->districtId($toProvinceId, $toDistrictName);
        $toWardCode = $this->wardCode($toDistrictId, $toWardName);

        // Lấy ID tỉnh, huyện, xã theo API GHN cho địa chỉ gửi
        $formProvinceId = $this->provinceId($formProvinceName);
        $formDistrictId = $this->districtId($formProvinceId, $formDistrictName);
        $formProvinceId = $this->wardCode($formDistrictId, $formWardName);




        return response()->json([
           'status' => 200,
           'time' => $time['data']['leadtime'],
        ]);




    }




    /**
     * Lấy ID tỉnh theo API dựa theo tên tỉnh từ database
     * @param $provinceName
     * @return mixed|null
     */
    public function provinceId($provinceName){
        $provinceId = null;
        $provinces = $this->ghnService->GetProvince();
        foreach ($provinces['data'] as $province){
            foreach ($province['NameExtension'] as  $provinceNameAPI){
                if (strcasecmp($provinceNameAPI, $provinceName) === 0) {
                    $provinceId = $province['ProvinceID'];
                    break 2; // Thoát khỏi cả hai vòng lặp nếu tìm thấy
                }
            }
        }

        return $provinceId;
    }


    /**
     * Lấy ID huyện theo API dựa theo tên huyện từ database
     * @param $provinceId
     * @param $districtName
     * @return mixed|null
     */
    public function districtId($provinceId, $districtName){
        $districtId = null;
        $districts = $this->ghnService->GetDistrict($provinceId);
        foreach ($districts['data'] as $district){
            foreach ($district['NameExtension'] as $districtNameAPI){
                if (strcasecmp($districtNameAPI, $districtName) === 0) {
                    $districtId = $district['DistrictID'];
                    break 2;
                }else{
                    $districtId = 'khong khop '. $districtName;
                }
            }
        }

        return $districtId;
    }


    /**
     * Lấy ID xã theo API dựa theo tên xã từ database
     * @param $districtId
     * @param $wardName
     * @return mixed|null
     */
    public function wardCode($districtId, $wardName){
        $wardCode = null;
        $wards = $this->ghnService->GetWard($districtId);
        foreach ($wards['data'] as $ward){
            foreach ($ward['NameExtension'] as $wardNameAPI){
                if (strcasecmp($wardNameAPI, $wardName) === 0) {
                    $wardCode = $ward['WardCode'];
                    break 2;
                }
            }
        }

        return $wardCode;
    }



}
