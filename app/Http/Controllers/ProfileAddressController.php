<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Province;
use App\Models\UserAddress;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileAddressController extends Controller
{
    //
    public function index($id = null)
    {
        $user = Auth::user();
        $province = Province::all();
        $addresses = UserAddress::where('user_id', $user->id)->get();
        $address = $id ? UserAddress::find($id) : null; // Check if it's an edit operation

        $districts = District::all();
        $wards = Ward::all();

        if ($address) {
            // Lấy thông tin chi tiết của tỉnh, quận/huyện, xã/phường từ cơ sở dữ liệu
            $province = Province::find($address);
            $districts = District::where('province_id', $address->province_id)->get();
            $wards = Ward::where('district_id', $address->district_id)->get();
        }

        return view('layouts.profile_address', compact('user', 'province', 'addresses', 'address', 'districts', 'wards'));
    }


    public function getDistricts($provinceId)
    {
        $districts = District::where('province_id', $provinceId)->get();
        return response()->json($districts);
    }

    public function getWards($districtId)
    {
        $wards = Ward::where('district_id', $districtId)->get();
        return response()->json($wards);
    }

    public function searchAddress(Request $request)
    {
        $query = $request->input('query');

        // Example logic: search through provinces, districts, wards, or any other relevant data
        $results = [];

        // Search provinces
        $provinces = Province::where('name', 'like', '%' . $query . '%')->get();
        foreach ($provinces as $province) {
            $results[] = [
                'type' => 'province',
                'name' => $province->name,
                'id' => $province->id,
            ];
        }

        // Search districts
        $districts = District::where('name', 'like', '%' . $query . '%')->get();
        foreach ($districts as $district) {
            $results[] = [
                'type' => 'district',
                'name' => $district->name,
                'id' => $district->id,
            ];
        }

        // Search wards
        $wards = Ward::where('name', 'like', '%' . $query . '%')->get();
        foreach ($wards as $ward) {
            $results[] = [
                'type' => 'ward',
                'name' => $ward->name,
                'id' => $ward->id,
            ];
        }

        return response()->json($results);
    }

    public function store(Request $request)
    {
        // Create
        $userAddress = new UserAddress();
        $userAddress->name = $request->input('name');
        $userAddress->phone = $request->input('phone');
        $userAddress->province_id = $request->input('province_id');
        $userAddress->district_id = $request->input('district_id');
        $userAddress->ward_id = $request->input('ward_id');
        $userAddress->address_specific = $request->input('address_specific');
        $userAddress->user_id = auth()->user()->id; // Assuming authenticated user
        $userAddress->save();


        $defaultAddress = UserAddress::where('user_id', auth()->user()->id)
            ->where('is_default', 1)
            ->first();
        if ($defaultAddress) {
            $user = auth()->user();
            $user->user_address_id = $defaultAddress->id;
            $user->save();
        }
        // Redirect or do something else after saving
        return redirect()->route('profile.address')->with('success', 'Địa chỉ đã được thêm thành công!');

    }

    public function edit($id)
    {
        $address = UserAddress::find($id);
        $provinces = Province::all();
        $districts = District::where('province_id', $address->province_id)->get();
        $wards = Ward::where('district_id', $address->district_id)->get();

        return view('layouts.profile_address_edit', compact('address', 'provinces', 'districts', 'wards'));
    }


    public function update(Request $request, $id)
    {
        // Update
        $userAddress = UserAddress::findOrFail($id);
        $userAddress->update([
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'province_id' => $request->input('province_id'),
            'district_id' => $request->input('district_id'),
            'ward_id' => $request->input('ward_id'),
            'address_specific' => $request->input('address_specific'),
        ]);
        return redirect()->route('profile.address')->with('success', 'Địa chỉ đã được cập nhật thành công!');
    }


    public function delete($id)
    {
        $userAddress = UserAddress::find($id);
        if ($userAddress) {
            $userAddress->delete();
            return redirect()->route('profile.address')->with('success', 'Địa chỉ đã được xoá thành công!');
        }
        return redirect()->route('profile.address')->with('error', 'Địa chỉ không tồn tại!');

    }

    public function setDefault($id)
    {
        // Lấy ID của người dùng hiện tại
        $userId = auth()->id();

        // Đặt tất cả các địa chỉ của người dùng hiện tại thành không mặc định
        UserAddress::where('user_id', $userId)->update(['is_default' => 0]);

        // Đặt địa chỉ được chọn thành mặc định
        UserAddress::where('id', $id)->update(['is_default' => 1]);

        $defaultAddress = UserAddress::where('user_id', auth()->user()->id)
            ->where('is_default', 1)
            ->first();
        if ($defaultAddress) {
            $user = auth()->user();
            $user->user_address_id = $defaultAddress->id;
            $user->save();
        }

        return redirect()->route('cart.view')->with('success', 'Đã thiết lập địa chỉ mặc định thành công!');
    }

}
