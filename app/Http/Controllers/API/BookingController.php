<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            ], 422);
        }

        $setting = Setting::first();
        $price = ceil($request->distance) * $setting->price_per_km;

        return response()->json([
            'success' => true,
            'message' => 'harga berhasil dihitung',
            'data' => $price
        ]);
    }

    // store booking
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            /**
             * @example -6.1111
             */
            'latitude_origin' => 'required|numeric|between:-90,90',
            /**
             * @example 106.1111
             */
            'longitude_origin' => 'required|numeric|between:-180,180',
            /**
             * @example Monas
             */
            'address_origin' => 'required|string|max:255',
            /**
             * @example -6.2222
             */
            'latitude_destination' => 'required|numeric|between:-90,90',
            /**
             * @example 106.2222
             */
            'longitude_destination' => 'required|numeric|between:-180,180',
            /**
             * @example Bekasi
             */
            'address_destination' => 'required|string|max:255',
            /**
             * @example 4
             */
            'distance' => 'required|numeric|min:0',
            /**
             * @example 300
             */
            'time_estimate' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'data' => ['errors' => $validator->errors()]
            ], 422);
        }

        if (!auth()->user()->checkCustomer()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya customer yang bisa melakukan booking',
                'data' => ['errors' => $validator->errors()]
            ], 403);
        }

        // validasi untuk membatasi booking, customer bisa melakukan booking kembali ketika statusnya cancel atau paid
        if(Booking::hasActiveBooking(auth()->id())){
            $activeBooking = Booking::getActiveBooking(auth()->id(),auth()->user()->role)->load(['customer', 'driver']);

            return response()->json([
                'success' =>false,
                'message' =>'Anda masih memliki booking yang aktif. Selesaikan terlebih dahulu',
                'data' => [
                    'active_booking' =>$activeBooking
                ]
            ], 422);
        }

        $setting = Setting::getSetting();
        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Setting belum diatur',
                'data' => null
            ]);
        }

        $price = ceil($request->distance) * $setting->price_per_km;

        $booking = Booking::create([
            'customer_id' => auth()->id(),
            'latitude_origin' => $request->latitude_origin,
            'longitude_origin' => $request->longitude_origin,
            'address_origin' => $request->address_origin,
            'latitude_destination' => $request->latitude_destination,
            'longitude_destination' => $request->longitude_destination,
            'address_destination' => $request->address_destination,
            'distance' => $request->distance,
            'price' => $price,
            'time_estimate' => $request->time_estimate,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking Berhasil',
            'data' => [
                'booking' => $booking->load('customer')->load('driver'),
                'price_details' => [
                    'distance' => $request->distance,
                    'price_per_km' => $setting->price_per_km,
                    'total_price' => $price
                ]
            ]
        ]);
    }

    public function cancel(Booking $booking)
    {
        if (auth()->id() !== $booking->customer_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki hak untuk mengcancel booking ini',
                'data' => null
            ], 403);
        }

        if (!$booking->isFindingDriver()) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak dapat dibatalkan',
                'data' => null
            ], 422);
        }

        $booking->update(['status' => Booking::STATUS_CANCELLED]);

        return response()->json([
            'success' => true,
            'message' => 'Booking berhasil dibatalkan',
            'data' => null
        ]);
    }

    public function getAll(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d',
            'status' => 'nullable|in:finding_driver,driver_pickup,driver_deliver,paid,arrived,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'data' => ['errors' => $validator->errors()]
            ], 422);
        }

        $query = Booking::with(['customer', 'driver'])
            ->when($request->filled('start_date'), function ($q) use ($request) {
                return $q->whereDate(['created_at'], '>=', $request->start_date);
            })
            ->when($request->filled('end_date'), function ($q) use ($request) {
                return $q->whereDate(['created_at'], '<=', $request->start_date);
            })
            ->when($request->filled('end_date'), function ($q) use ($request) {
                return $q->where('status', $request->start_date);
            });

        if (auth()->user()->checkDriver()) {
            $query->where('driver_id', auth()->user()->driver->id);
        } elseif (auth()->user()->checkCustomer()) {
            $query->where('customer_id', auth()->user()->id);
        }

        $bookings = $query->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Booking berhasil diambil',
            'data' => $bookings
        ]);
    }
}
