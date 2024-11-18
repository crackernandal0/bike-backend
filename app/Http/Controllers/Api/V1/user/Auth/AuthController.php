<?php

namespace App\Http\Controllers\Api\V1\User\Auth;

use App\Helpers\Auth\AuthHelper;
use App\Http\Controllers\Controller;
use App\Models\SmsOtp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function sendSMSOtp(Request $request)
    {
        // Validate the request input
        $validator = Validator::make($request->all(), [
            'country_code' => 'required|min:1|max:4',
            'phone_number' => 'required|numeric|digits_between:5,20',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        try {
            // Check if user is already registered
            $user = User::where('phone_number', $request->phone_number)
                ->where('country_code', $request->country_code)
                ->first();

            if ($user && !$user->active) {
                return jsonResponse(false, __('auth.account_deactivated'), 400);
            }

            // Generate OTP
            $otp = otp();

            // Update or create the OTP record
            SmsOtp::updateOrCreate(
                [
                    'country_code' => $request->country_code,
                    'phone_number' => $request->phone_number,
                ],
                [
                    'otp' => $otp,
                    'expires_at' => Carbon::now()->addMinute(5), // OTP expires in 5 minutes
                ]
            );

            AuthHelper::sendSMS($request->country_code . $request->phone_number, 'User', $otp);

            return jsonResponse(true, __('auth.otp_sent'));
        } catch (\Exception $e) {
            return jsonResponse(false, $e->getMessage(), 400);
        }
    }

    public function verifyOtp(Request $request)
    {
        // Validate the request input
        $validator = Validator::make($request->all(), [
            'country_code' => 'required|min:1|max:4',
            'phone_number' => 'required|numeric|digits_between:5,20',
            'otp' => 'required|numeric|digits:4',
            'fcm_token' => 'nullable|max:500',

        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        try {
            SmsOtp::where('expires_at', '<', now())->delete();

            // Find the OTP record
            $otpRecord = SmsOtp::where('country_code', $request->country_code)
                ->where('phone_number', $request->phone_number)
                ->where('otp', $request->otp)
                ->first();

            // Check if the OTP record is valid
            if (!$otpRecord) {
                return jsonResponse(false, __('auth.invalid_otp_or_expired'), 400);
            }

            // Check if user exists
            $user = User::where('phone_number', $request->phone_number)
                ->where('country_code', $request->country_code)
                ->first();

            if ($user) {
                // Verify the OTP and log in the user if exists
                if ($user->active) {
                    if ($request->fcm_token) {
                        $user->update(['fcm_token' => $request->fcm_token]);
                    }
                    $token = $user->createToken('FemiRides')->plainTextToken;

                    if ($otpRecord) {
                        $otpRecord->delete();
                    }

                    return jsonResponseWithData(true, __('auth.otp_verified'), [
                        'user' => $user,
                        'token' => $token,
                    ], 200);
                } else {

                    return jsonResponse(false, __('auth.account_deactivated'), 400);
                }
            } else {

                // No user found, OTP verified but user does not exist
                $otpRecord->delete();
                return jsonResponse(true, __('auth.otp_verified'), 201);
            }
        } catch (\Exception $e) {
            return jsonResponse(false, $e->getMessage(), 400);
        }
    }


    public function register(Request $request)
    {
        // Validate the request input
        $validator = Validator::make($request->all(), [
            'country_code' => 'required|min:1|max:4',
            'phone_number' => 'required|numeric|digits_between:5,20',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email',
            'language' => 'nullable|max:255',
            'timezone' => 'nullable|max:255',
            'referral_code' => 'nullable|max:255',
            'fcm_token' => 'nullable|max:500',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        try {
            $user = User::where('phone_number', $request->phone_number)
                ->where('country_code', $request->country_code)
                ->exists();

            if ($user) {
                return jsonResponse(false, __('auth.user_phone_exists'), 400);
            }

            $referralCode = null;
            // Generate a unique referral code
            do {
                $referralCode = strtoupper(str()->random(8));
            } while (User::where('referral_code', $referralCode)->exists());


            $referred_by = null;
            if ($request->referral_code) {
                $referralUser = User::where('referral_code', $request->referral_code)->first();
                if ($referralUser) {
                    $referred_by = $referralUser->id;
                }
            }

            // Register the user
            $user = User::create([
                'country_code' => $request->country_code,
                'phone_number' => $request->phone_number,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make(str()->random()),
                'active' => true,
                'mobile_confirmed' => true,
                'language' => $request->language ?? 'en',
                'timezone' => $request->timezone,
                'referral_code' => $referralCode,
                'referred_by' => $referred_by,
                'fcm_token' => $request->fcm_token,
            ]);

            // Log in the user and return token
            $token = $user->createToken($user->name)->plainTextToken;

            return jsonResponseWithData(true, __('auth.register_success'), [
                'user' => $user,
                'token' => $token,
            ], 200);
        } catch (\Exception $e) {
            return jsonResponse(false, $e->getMessage(), 400);
        }
    }


    public function socialAuth(Request $request, $provider)
    {
        // Validate the request input
        $validator = Validator::make($request->all(), [
            'oauth_token' => 'required',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }
        try {
            $oauthToken = $request->oauth_token;
            $socialUser = Socialite::driver($provider)->userFromToken($oauthToken);

            if ($socialUser) {
                $user = User::where('email', $socialUser->email)
                    ->first();

                if ($user) {
                    if (empty($user->phone_number)) {
                        return jsonResponseWithData(false, __('auth.phone_required'), ['user_id' => $user->id], 409);
                    }

                    if (!$user->email_confirmed) {
                        $user->update([
                            'email_confirmed' => true
                        ]);
                    }

                    $token = $user->createToken('FemiRides')->plainTextToken;
                    return jsonResponseWithData(true, __('auth.login_success'), [
                        'user' => $user,
                        'token' => $token
                    ], 200);
                } else {
                    $referralCode = null;
                    // Generate a unique referral code
                    do {
                        $referralCode = strtoupper(str()->random(8));
                    } while (User::where('referral_code', $referralCode)->exists());


                    $user = User::create([
                        'name' => $socialUser->name,
                        'email' => $socialUser->email,
                        'social_id' => $socialUser->id,
                        'social_provider' => $provider,
                        'profile_picture' => $socialUser->avatar,
                        'referral_code' => $referralCode,
                    ]);

                    return jsonResponseWithData(false, __('auth.phone_required'), ['user_id' => $user->id], 409);
                }
            } else {
                return jsonResponse(false, __('auth.social_login_failed'), 400);
            }
        } catch (\Exception $e) {
            return jsonResponse(false, $e->getMessage(), 400);
        }
    }

    public function verifyPhone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'country_code' => 'required|min:1|max:4',
            'phone_number' => 'required|numeric|digits_between:5,20',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        try {

            $user = User::find($request->user_id);
            $user->update([
                'country_code' => $request->country_code,
                'phone_number' => $request->phone_number,
                'mobile_confirmed' => true,
            ]);

            $token = $user->createToken('authToken')->plainTextToken;
            return jsonResponseWithData(true, __('auth.phone_verified_login_success'), [
                'user' => $user,
                'token' => $token
            ], 200);
        } catch (\Exception $e) {
            return jsonResponse(false, $e->getMessage(), 400);
        }
    }
}
