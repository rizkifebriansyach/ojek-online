<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    // cek harga
    public function priceCheck(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'distance' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'data' => ['errors' => $validator->errors()]
            ]);
        }

        $setting = Setting::first();
        $price = ceil($request->distance) * $setting->price_per_km;

        return response()->json([
            'success' => true,
            'message' => 'harga berhasil dihitung',
            'data' => $price
        ]);
    }
}
