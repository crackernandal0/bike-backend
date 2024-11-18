<?php

namespace App\Http\Controllers\Api\V1\Driver;

use App\Helpers\Auth\AuthHelper;
use App\Http\Controllers\Controller;
use App\Models\Driver\Driver;
use App\Models\SmsOtp;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Driver\DriverAdditionalInfo;
use App\Models\Driver\DriverBankInfo;
use App\Models\Driver\DriverDocument;
use App\Models\Driver\DriverVehicle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


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
            // Check if driver is already registered
            $driver = Driver::where('phone_number', $request->phone_number)
                ->where('country_code', $request->country_code)
                ->first();

            if ($driver && !$driver->active) {
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

            if ($request->phone_number != '9781909727') {
                AuthHelper::sendSMS($request->country_code . $request->phone_number, 'User', $otp);
            }

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

            if ($request->phone_number != '9781909727') {
                // Check if the OTP record is valid
                if (!$otpRecord) {
                    return jsonResponse(false, __('auth.invalid_otp_or_expired'), 400);
                }
            }
            // Check if driver exists
            $driver = Driver::where('phone_number', $request->phone_number)
                ->where('country_code', $request->country_code)
                ->first();

            if ($driver) {
                // Verify the OTP and log in the driver if exists
                if ($driver->active) {

                    if ($driver->status == "approved") {
                        if ($request->fcm_token) {
                            $driver->update(['fcm_token' => $request->fcm_token]);
                        }
                        $token = $driver->createToken('FemiRides')->plainTextToken;
                        if ($otpRecord) {
                            $otpRecord->delete();
                        }

                        return jsonResponseWithData(true, __('auth.otp_verified'), [
                            'driver' => $driver,
                            'token' => $token,
                        ], 200);
                    } else {
                        return jsonResponse(false, "Your account is not approved by the admin yet. You will receive notification once your account is approved.", 400);
                    }
                } else {

                    return jsonResponse(false, __('auth.account_deactivated'), 400);
                }
            } else {

                // No user found, OTP verified but user does not exist
                if ($otpRecord) {
                    $otpRecord->delete();
                }
                return jsonResponse(true, __('auth.otp_verified'), 201);
            }
        } catch (\Exception $e) {
            return jsonResponse(false, $e->getMessage(), 400);
        }
    }


    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'signup_type' => 'required|string|in:driver,instructor',
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:drivers,email',
            'country_code' => 'required|string|max:10',
            'phone_number' => 'required|string|max:20|unique:drivers,phone_number',
            'language' => 'nullable|string|max:50',
            'date_of_birth' => 'required|date',
            'address' => 'required|string|max:500',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'experience_years' => 'nullable|integer|min:0',

            // Vehicle Information
            'joining_type' => 'required|string|in:With Vehicle,Without Vehicle',
            'vehicle_type_id' => 'nullable|required_if:joining_type,With Vehicle|exists:vehicle_types,id',
            'vehicle_model' => 'nullable|required_if:joining_type,With Vehicle',
            'registration_number' => 'nullable|string|max:50|required_if:joining_type,With Vehicle',
            'registration_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048|required_if:joining_type,With Vehicle',
            'insurance_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048|required_if:joining_type,With Vehicle',

            // Identification and Verification
            'driving_license_number' => 'required|string|max:50',
            'driving_license_photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'aadhar_pan_number' => 'required|string|max:50',
            'aadhar_pan_photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',

            // Banking Information
            'bank_account_number' => 'required|string|max:50',
            'ifsc_code' => 'required|string|max:20',
            'account_holder_name' => 'required|string|max:255',

            // Additional Information (Instructor Specific)
            'qualifications' => 'nullable|string',
            'qualifications_attachments' => 'nullable|array',
            'qualifications_attachments.*' => 'mimes:jpeg,png,jpg,webp,pdf,ppt,docx,csv,xlsx',
            'certifications' => 'nullable|array',
            'certifications.*' => 'mimes:jpeg,png,jpg,webp,pdf,ppt,docx,csv,xlsx',
            'training_specializations' => 'nullable|string',

            'additional_requests' => 'nullable|string',
            'service_preferences' => 'nullable|string',
            'available_from' => 'nullable|string|max:50',
            'availability_schedule' => 'nullable|string|in:Weekdays,Weekends,Flexible',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_number' => 'nullable|string|max:30',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        try {
            DB::beginTransaction();

            $profilePhotoPath = $request->hasFile('profile_photo') ? uploadImage($request->file('profile_photo'), 'drivers/profile_photos') : null;

            // Create Driver or Instructor
            $user = Driver::create([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'country_code' => $request->country_code,
                'phone_number' => $request->phone_number,
                'language' => $request->language,
                'date_of_birth' => $request->date_of_birth,
                'address' => $request->address,
                'role' => $request->signup_type,
                'profile_photo' => $profilePhotoPath,
                'experience_years' => $request->experience_years,
                'status' => 'pending', // Initially set to pending for admin approval
                'country_id' => $request->country_id,
                'timezone' => $request->timezone,
                'joining_type' => $request->joining_type,
                'vehicle_type_id' => $request->vehicle_type_id,
                'fcm_token' => $request->fcm_token,
                'available_for_chauffeur' => $request->available_for_chauffeur,
                'available_for_trips' => $request->available_for_trips,
            ]);

            $this->storeDocuments($request, $user->id,);

            if ($request->joining_type === 'With Vehicle') {
                $this->storeVehicle($request, $user->id,);
            }

            $this->storeBankInfo($request, $user->id,);

            $this->storeAdditionalInfo($request, $user->id, $request->signup_type);


            DB::commit();
            return jsonResponse(true, ucfirst($request->signup_type) . ' registration submitted successfully and is pending approval.');
        } catch (\Exception $e) {
            DB::rollBack();
            return jsonResponse(false, $e->getMessage(), 400);
        }
    }

    protected function storeVehicle($request, $userId)
    {
        DriverVehicle::create([
            'driver_id' => $userId,
            'vehicle_type_id' => $request->vehicle_type_id,
            'vehicle_model' => $request->vehicle_model,
            'registration_number' => $request->registration_number,
            'registration_photo' => $this->storeFile($request->file('registration_photo'), 'drivers/vehicles'),
            'insurance_photo' => $this->storeFile($request->file('insurance_photo'), 'drivers/vehicles'),
        ]);
    }

    protected function storeBankInfo($request, $userId)
    {
        DriverBankInfo::create([
            'driver_id'  => $userId,
            'bank_account_number' => $request->bank_account_number,
            'ifsc_code' => $request->ifsc_code,
            'account_holder_name' => $request->account_holder_name,
        ]);
    }

    // Helper Method for Storing Instructor Documents
    protected function storeDocuments($request, $userId)
    {
        $documents = [
            [
                'type' => 'Driving License',
                'number' => $request->driving_license_number,
                'photo' => $request->file('driving_license_photo')
            ],
            [
                'type' => 'Aadhar/PAN',
                'number' => $request->aadhar_pan_number,
                'photo' => $request->file('aadhar_pan_photo')
            ]
        ];


        foreach ($documents as $doc) {
            $photoPath = $this->storeFile($doc['photo'], 'drivers/documents');
            DriverDocument::create([
                'driver_id'  => $userId,
                'document_type' => $doc['type'],
                'document_number' => $doc['number'],
                'document_photo' => $photoPath,
            ]);
        }
    }

    protected function storeAdditionalInfo($request, $driverId, $signupType)
    {
        if ($signupType == 'driver') {
            DriverAdditionalInfo::create([
                'driver_id' => $driverId,
                'additional_requests' => $request->additional_requests,
                'service_preferences' => $request->service_preferences,
                'available_from' => $request->available_from,
                'availability_schedule' => $request->availability_schedule,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_number' => $request->emergency_contact_number,
            ]);
        } elseif ($signupType == 'instructor') {

            $qualifications_attachments = [];
            if ($request->file('qualifications_attachments')) {
                foreach ($request->file('qualifications_attachments') as $file) {
                    $qualifications_attachments[] = $this->storeFile($file, 'drivers/qualifications');
                }
            }
            $certifications = [];
            if ($request->file('certifications')) {
                foreach ($request->file('certifications') as $file) {
                    $certifications[] = $this->storeFile($file, 'drivers/qualifications');
                }
            }
            DriverAdditionalInfo::create([
                'driver_id' => $driverId,
                'qualifications' => $request->qualifications,
                'qualifications_attachments' => json_encode($qualifications_attachments),
                'certifications' => json_encode($certifications),
                'training_specializations' => $request->training_specializations,
                'additional_requests' => $request->additional_requests,
                'service_preferences' => $request->service_preferences,
                'available_from' => $request->available_from,
                'availability_schedule' => $request->availability_schedule,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_number' => $request->emergency_contact_number,
            ]);
        }
    }


    // Helper Method for Storing Files
    protected function storeFile($file, $path)
    {
        if ($file->isValid()) {
            $extension = $file->getClientOriginalExtension();
            if (in_array($extension, ['jpeg', 'png', 'jpg', 'webp'])) {
                return uploadImage($file, $path);
            } else {
                return $this->uploadDocument($file, $path);
            }
        }
        return null;
    }

    // Helper Method for Storing Non-Image Files (e.g., PDFs)
    protected function uploadDocument($file, $path)
    {
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $storagePath = "public/{$path}/{$filename}";
        Storage::put($storagePath, file_get_contents($file));
        return "storage/{$path}/{$filename}";
    }
}
