<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //register
    /**
     * @unauthenticated
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            /**
             * @example Customer B 
             */
            'name' => 'required|string|max:255',
            /**
             * @example customer2@gmail.com
             */
            'email' => 'required|string|email|unique:users',
            /**
             * @example 12345678
             */
            'password' => 'required|string|min:8',
            /**
             * @example 08888888
             */
            'whatsapp' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'data' => ['error' => $validator->errors()]
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'whatsapp' => $request->whatsapp
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $userData = $user->toArray();
        $userData['token'] = $token;

        return response()->json([
            'success' => true,
            'message' => 'Login success',
            'data' => $userData
        ]);
    }


    //login
    /**
     * @unauthenticated
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            /**
             * @example aji@gmail.com
             */
            'email' => 'required|email',
            /**
             * @example 1234567
             */
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'data' => ['error' => $validator->errors()]
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid login details',
                'data' => null
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $setting = Setting::getSetting();

        $token = $user->createToken('auth_token')->plainTextToken;

        $userData = $user->toArray();
        $userData['token'] = $token;

        if ($user->role === 'driver') {
            $userData['driver'] = $user->driver;
            $userData['setting'] = $setting;
        }

        return response()->json([
            'success' => true,
            'message' => 'Login success',
            'data' => $userData
        ]);
    }

    // get data user
    public function user(Request $request)
    {
        $user = $request->user();
        if ($user->role === 'driver') {
            $user->driver = $user->driver;
        }

        return response()->json([
            'success' => true,
            'message' => 'Login success',
            'data' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'success' => true,
            'message' => 'Logout success',
            'data' => null
        ]);
    }
}
