<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SemaphoreService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class UserOtpController extends Controller
{
    protected $semaphore;

    public function __construct(SemaphoreService $semaphore)
    {
        $this->semaphore = $semaphore;
    }

    public function send(Request $request)
    {
        $request->validate([
            'phone' => 'required|string'
        ]);

        $otp = rand(100000, 999999);

        Session::put('otp_code', $otp);
        Session::put('otp_phone', $request->phone);

        $message = "Your password change OTP code is: $otp. Do not share this with anyone.";

        $response = $this->semaphore->sendSMS($request->phone, $message);

        return response()->json(['result' => true, 'message' => 'OTP sent successfully', 'semaphore_response' => $response]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required'
        ]);

        if ($request->otp == Session::get('otp_code')) {
            Session::put('otp_verified', true);
            return response()->json(['result' => true, 'message' => 'OTP verified']);
        }

        return response()->json(['result' => false, 'message' => 'Invalid OTP']);
    }

    public function updatePassword(Request $request)
    {
        if (!Session::get('otp_verified')) {
            return response()->json(['result' => false, 'message' => 'OTP not verified.']);
        }

        $request->validate(['password' => 'required|min:8']);

        $user = auth()->user();
        $user->password = Hash::make($request->password);
        $user->save();

        // Clear OTP after use
        Session::forget(['otp_verified', 'otp_code', 'otp_phone']);

        return response()->json(['result' => true, 'message' => 'Password updated successfully.']);
    }
}
