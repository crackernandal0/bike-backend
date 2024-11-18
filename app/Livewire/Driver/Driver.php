<?php

namespace App\Livewire\Driver;

use App\Models\Driver\Driver as DriverDriver;
use App\Models\Driver\DriverDocument;
use App\Models\Vehicles\VehicleSubcategory;
use App\Models\Vehicles\VehicleType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Driver extends Component
{
    use WithFileUploads;


    public $driverId;

    // Driver data
    public $full_name, $email, $country_code, $phone_number, $language, $date_of_birth, $address, $profile_photo, $old_profile_photo, $experience_years, $active, $role, $vehicle_subcategory_id, $service_location_id, $available, $instructor_bio;

    public $status, $account_status, $available_for_chauffeur, $available_for_trips;

    public $joining_type, $driving_license_number, $old_driving_license_photo, $driving_license_photo, $aadhar_pan_number, $aadhar_pan_photo, $old_aadhar_pan_photo, $vehicle_type_id, $vehicle_model, $registration_number, $registration_photo, $insurance_photo, $old_registration_photo, $old_insurance_photo;

    public $bank_account_number, $ifsc_code, $account_holder_name;

    public $qualifications, $qualifications_attachments = [], $certifications = [], $old_qualifications_attachments = [], $old_certifications = [], $training_specializations;

    public $additional_requests, $service_preferences, $available_from, $availability_schedule, $emergency_contact_name, $emergency_contact_number;

    public $documents = [];

    public function mount($id)
    {
        $this->driverId = $id;
    }

    public function editDriver()
    {
        $driver = DriverDriver::with(['additionalInfo', 'bankInfo', 'documents'])->find($this->driverId);

        if ($driver) {
            // Populate Driver basic data
            $this->full_name = $driver->full_name;
            $this->email = $driver->email;
            $this->country_code = $driver->country_code;
            $this->phone_number = $driver->phone_number;
            $this->language = $driver->language;
            $this->date_of_birth = $driver->date_of_birth;
            $this->address = $driver->address;
            $this->old_profile_photo = $driver->profile_photo;

            $this->active = $driver->active;
            $this->status = $driver->status;
            $this->account_status = $driver->account_status;
            $this->experience_years = $driver->experience_years;
            $this->role = $driver->role;
            $this->vehicle_type_id = $driver->vehicle_type_id;
            $this->vehicle_subcategory_id = $driver->vehicle_subcategory_id;
            $this->service_location_id = $driver->service_location_id;
            $this->available = $driver->available;
            $this->available_for_chauffeur = $driver->available_for_chauffeur ? true : false;
            $this->available_for_trips = $driver->available_for_trips ? true : false;
            $this->instructor_bio = $driver->instructor_bio;
            $this->joining_type = $driver->joining_type;

            // Populate DriverAdditionalInfo
            if ($driver->additionalInfo) {
                $this->additional_requests = $driver->additionalInfo->additional_requests;
                $this->service_preferences = $driver->additionalInfo->service_preferences;
                $this->available_from = $driver->additionalInfo->available_from;
                $this->availability_schedule = $driver->additionalInfo->availability_schedule;
                $this->emergency_contact_name = $driver->additionalInfo->emergency_contact_name;
                $this->emergency_contact_number = $driver->additionalInfo->emergency_contact_number;
                $this->qualifications = $driver->additionalInfo->qualifications;
                $this->old_qualifications_attachments = json_decode($driver->additionalInfo->qualifications_attachments);
                $this->old_certifications = json_decode($driver->additionalInfo->certifications);
                $this->training_specializations = $driver->additionalInfo->training_specializations;
            }

            // Populate DriverBankInfo
            if ($driver->bankInfo) {
                $this->bank_account_number = $driver->bankInfo->bank_account_number;
                $this->ifsc_code = $driver->bankInfo->ifsc_code;
                $this->account_holder_name = $driver->bankInfo->account_holder_name;
            }

            // Populate DriverDocuments
            if ($driver->documents) {
                foreach ($driver->documents as $document) {
                    if ($document->document_type == 'Driving License') {
                        $this->driving_license_number = $document->document_number;
                        $this->old_driving_license_photo = $document->document_photo;
                    }
                    if ($document->document_type == 'Aadhar/PAN') {
                        $this->aadhar_pan_number = $document->document_number;
                        $this->old_aadhar_pan_photo = $document->document_photo;
                    }
                }
            }

            // Dispatch the editDriver event
            $this->dispatch('editDriver');
        } else {
            session()->flash('error', 'Driver not found!');
        }
    }



    public function updateDriver()
    {
        $this->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:drivers,email,' . $this->driverId,
            'country_code' => 'required|string|max:10',
            'phone_number' => 'required|string|max:20|unique:drivers,phone_number,' . $this->driverId,
            'language' => 'nullable|string|max:50',
            'date_of_birth' => 'required|date',
            'address' => 'required|string|max:500',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'experience_years' => 'nullable|integer|min:0',

            // Vehicle Information
            'joining_type' => 'nullable|string|in:With Vehicle,Without Vehicle',
            'vehicle_type_id' => 'nullable|exists:vehicle_types,id',
            'vehicle_model' => 'nullable',
            'registration_number' => 'nullable|string|max:50',
            'registration_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'insurance_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',

            // Identification and Verification
            'driving_license_number' => 'required|string|max:50',
            'driving_license_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'aadhar_pan_number' => 'required|string|max:50',
            'aadhar_pan_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',

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


        try {
            DB::beginTransaction();

            $driver = DriverDriver::findOrFail($this->driverId);

            // Update Driver basic information
            $driver->update([
                'full_name' => $this->full_name,
                'email' => $this->email,
                'country_code' => $this->country_code,
                'phone_number' => $this->phone_number,
                'language' => $this->language,
                'date_of_birth' => $this->date_of_birth,
                'address' => $this->address,
                'experience_years' => $this->experience_years,
                'joining_type' => $this->joining_type,
                'vehicle_type_id' => $this->vehicle_type_id,
                'vehicle_subcategory_id' => $this->vehicle_subcategory_id,
                'available_for_chauffeur' => $this->available_for_chauffeur,
                'available_for_trips' => $this->available_for_trips,
                'joining_type' => $this->joining_type,
                'role' => $this->role,
                'account_status' => $this->account_status,
                'status' => $this->status,
                'active' => $this->active,
            ]);

            if ($this->profile_photo) {
                $new_profile_photo = uploadMedia($this->profile_photo, 'drivers/profile', 60, $this->old_profile_photo);

                $driver->update(['profile_photo' => $new_profile_photo]);
            }


            // Update vehicle information if joining type is 'With Vehicle'
            // if ($this->joining_type === 'With Vehicle' && $driver->vehicle) {
            //     $driver->vehicle->update([
            //         'vehicle_type_id' => $this->vehicle_type_id,
            //         'vehicle_model' => $this->vehicle_model,
            //         'registration_number' => $this->registration_number,
            //     ]);

            //     if ($this->registration_photo) {
            //         $driver->vehicle->registration_photo = $this->storeFile($this->registration_photo, 'drivers/vehicles');
            //     }

            //     if ($this->insurance_photo) {
            //         $driver->vehicle->insurance_photo = $this->storeFile($this->insurance_photo, 'drivers/vehicles');
            //     }
            // }

            if ($driver->bankInfo) {
                // Update bank information
                $driver->bankInfo->update([
                    'bank_account_number' => $this->bank_account_number,
                    'ifsc_code' => $this->ifsc_code,
                    'account_holder_name' => $this->account_holder_name,
                ]);
            }
            if ($driver->additionalInfo) {
                // Update additional information
                $driver->additionalInfo->update([
                    'additional_requests' => $this->additional_requests,
                    'service_preferences' => $this->service_preferences,
                    'available_from' => $this->available_from,
                    'availability_schedule' => $this->availability_schedule,
                    'emergency_contact_name' => $this->emergency_contact_name,
                    'emergency_contact_number' => $this->emergency_contact_number,
                    'qualifications' => $this->qualifications,
                    'training_specializations' => $this->training_specializations,
                ]);
            }

            // Fetch existing attachments from the database
            $existing_attachments = json_decode($driver->additionalInfo->qualifications_attachments, true) ?? [];
            $existing_certs = json_decode($driver->additionalInfo->certifications, true) ?? [];

            // Update qualifications attachments
            if (!empty($this->qualifications_attachments)) {
                $attachments = [];
                foreach ($this->qualifications_attachments as $attachment) {
                    $attachments[] = $this->storeFile($attachment, 'drivers/qualifications');
                }
                // Merge new attachments with existing ones
                $attachments = array_merge($existing_attachments, $attachments);
                $driver->additionalInfo->qualifications_attachments = json_encode($attachments);
            }

            // Update certifications
            if (!empty($this->certifications)) {
                $certs = [];
                foreach ($this->certifications as $certification) {
                    $certs[] = $this->storeFile($certification, 'drivers/certifications');
                }
                // Merge new certifications with existing ones
                $certs = array_merge($existing_certs, $certs);
                $driver->additionalInfo->certifications = json_encode($certs);
            }

            // Save the additional info
            $driver->additionalInfo->save();


            // Update documents if they are uploaded
            // if (!empty($this->documents)) {
            //     foreach ($this->documents as $doc) {
            //         $this->updateDocument($driver->id, $doc);
            //     }
            // }

            $driving_license_photo = $this->old_driving_license_photo;
            $aadhar_pan_photo = $this->old_aadhar_pan_photo;

            if ($this->driving_license_photo) {
                $driving_license_photo = uploadMedia($this->driving_license_photo, 'drivers/documents', 90, $this->old_driving_license_photo);
            }

            if ($this->aadhar_pan_photo) {
                $aadhar_pan_photo = uploadMedia($this->aadhar_pan_photo, 'drivers/documents', 90, $this->old_aadhar_pan_photo);
            }

            $documents = [
                [
                    'type' => 'Driving License',
                    'number' => $this->driving_license_number,
                    'photo' => $driving_license_photo
                ],
                [
                    'type' => 'Aadhar/PAN',
                    'number' => $this->aadhar_pan_number,
                    'photo' => $aadhar_pan_photo
                ]
            ];

            if ($driver->documents()) {
                $driver->documents()->delete();
            }
            foreach ($documents as $doc) {
                DriverDocument::create([
                    'driver_id'  => $driver->id,
                    'document_type' => $doc['type'],
                    'document_number' => $doc['number'],
                    'document_photo' => $doc['photo'],
                ]);
            }

            DB::commit();
            $this->dispatch('hideEditDriver', 'driving_license_photo', 'qualifications_attachments', 'certifications');

            $this->reset('aadhar_pan_photo',);

            session()->flash('success', 'Driver updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error updating driver: ' . $e->getMessage());
        }
    }

    protected function updateDocument($driverId, $doc)
    {
        $document = DriverDocument::where('driver_id', $driverId)
            ->where('document_type', $doc['type'])
            ->first();

        if ($document) {
            $document->update([
                'document_number' => $doc['number'],
                'document_photo' => $this->storeFile($doc['photo'], 'drivers/documents'),
            ]);
        }
    }

    protected function storeFile($file, $path)
    {
        if ($file->isValid()) {
            // Store the file and get the path
            $storedPath = $file->store($path, 'public');

            // Return the public URL to store in the database
            return 'storage/' . $storedPath;
        }
        return null;
    }

    public function deleteDriver()
    {
        $Driver = Driver::find($this->driverId);
        if ($Driver) {
            $Driver->delete();
            session()->flash('success', 'Driver deleted successfully!');
        } else {
            session()->flash('error', 'Driver not found!');
        }
    }

    public function deleteQualificationAttachment($index)
    {
        // Fetch the old attachments
        $attachments = $this->old_qualifications_attachments;

        // Remove the selected index
        if (isset($attachments[$index])) {
            // Optionally, delete the file from storage
            Storage::delete(str_replace('/storage/', 'public/', $attachments[$index]));


            // Remove the attachment from the array
            unset($attachments[$index]);

            // Re-index the array to prevent gaps
            $attachments = array_values($attachments);

            $driver = DriverDriver::find($this->driverId);

            // Save the updated attachments back to the database
            $driver->additionalInfo->qualifications_attachments = json_encode($attachments);
            $driver->additionalInfo->save();

            // Update the Livewire component data
            $this->old_qualifications_attachments = $attachments;

            session()->flash('message', 'Qualification attachment deleted successfully.');
        }
    }

    public function deleteCertification($index)
    {
        // Fetch the old certifications
        $certs = $this->old_certifications;

        // Remove the selected index
        if (isset($certs[$index])) {
            // Optionally, delete the file from storage
            Storage::delete(str_replace('/storage/', 'public/', $certs[$index]));

            // Remove the certification from the array
            unset($certs[$index]);

            // Re-index the array to prevent gaps
            $certs = array_values($certs);

            $driver = DriverDriver::find($this->driverId);

            // Save the updated certifications back to the database
            $driver->additionalInfo->certifications = json_encode($certs);
            $driver->additionalInfo->save();

            // Update the Livewire component data
            $this->old_certifications = $certs;

            session()->flash('message', 'Certification deleted successfully.');
        }
    }

    public function updateStatus()
    {
        $driver = DriverDriver::find($this->driverId);
        if ($driver) {
            $driver->update(['status' => 'approved']);
            session()->flash('success', 'Driver status updated to approved successfully!');
        } else {
            session()->flash('error', 'Driver not found!');
        }
    }

    public function render()
    {
        $driver = DriverDriver::where('id', $this->driverId)->first();


        $vehicleTypes = VehicleType::get();
        $vehicleSubcategories = VehicleSubcategory::where('vehicle_type_id', $this->vehicle_type_id)->get();

        return view('livewire.driver.driver', compact('driver', 'vehicleTypes', 'vehicleSubcategories'));
    }
}