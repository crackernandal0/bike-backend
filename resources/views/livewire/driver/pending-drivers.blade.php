@section('drivers_active', 'open')
@section('pending_drivers_active', 'active')
<div>
    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->

        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Admin /</span> Pending Drivers</h4>

            <!-- Basic Bootstrap Table -->
            <div class="card">
                <h5 class="card-header">
                    <div class="row">

                        <div class="col-lg-6 d-flex align-items-center justify-content-between">

                        </div>

                        <div class="col-lg-3">

                        </div>
                        <div class="col-lg-3 mt-4 mt-lg-0">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text" id="basic-addon-search31"><i
                                        class="bx bx-search"></i></span>
                                <input type="text" wire:model.live.debounce.500ms="search" class="form-control"
                                    placeholder="Search..." aria-label="Search..."
                                    aria-describedby="basic-addon-search31">
                            </div>
                        </div>
                    </div>

                </h5>

                <div class="table-responsive text-wrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="font-size: 11px;white-space:nowrap">SNO.</th>
                                <th style="font-size: 11px;white-space:nowrap">Name</th>
                                <th style="font-size: 11px;white-space:nowrap">Email</th>
                                <th style="font-size: 11px;white-space:nowrap">Phone Number</th>
                                <th style="font-size: 11px;white-space:nowrap">Verification Status</th>
                                <th style="font-size: 11px;white-space:nowrap">Applied At</th>
                                <th style="font-size: 11px;white-space:nowrap">View</th>
                                <th style="font-size: 11px;white-space:nowrap">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @if (count(value: $drivers) > 0)
                            @foreach ($drivers as $index => $user)
                            <tr class="cursor-pointer">
                                <td>
                                    {{ ($drivers->currentPage() - 1) * $drivers->perPage() + $index + 1 }}</td>
                                <td style="white-space:nowrap">{{ $user->full_name }}</td>

                                <td style="white-space:nowrap"><a style="word-break: break-all;"
                                        class="text-decoration-underline"
                                        href="mailto:{{ $user->email }}">{{ $user->email }}</a></td>

                                <td style="white-space:nowrap"><a style="word-break: break-all;"
                                        class="text-decoration-underline"
                                        href="tel:{{ $user->country_code }}{{ $user->phone_number }}">{{ $user->country_code }}{{ $user->phone_number }}</a>
                                </td>



                                <td>
                                    @if ($user->status == 'pending')
                                    <button class="btn-sm btn-warning" wire:click="updateStatus({{ $user->id }})">
                                        Pending
                                    </button>
                                    @elseif($user->status == 'rejected')
                                    <button class="btn-sm btn-danger">
                                        Rejected
                                    </button>
                                    @endif
                                </td>

                                <td style="white-space:nowrap">{{ $user->created_at->format('d/m/Y H:i A') }}
                                </td>

                                <td>
                                    <div class="d-flex align-items-center">
                                        <a href="{{ route('driver', $user->id) }}"
                                            style="background: none; outline:none;border:none;">

                                            <i style="font-size:15px; font-weight:600;"
                                                class="bx bx-show text-primary"></i>
                                        </a>
                                    </div>
                                </td>

                                <td>
                                    <div class="dropdown dropup">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu" data-popper-placement="top-start">
                                            <button class="dropdown-item" wire:click="editDriver({{ $user->id }})">
                                                <i class="bx bx-pencil me-1"></i> Edit
                                            </button>
                                            <button class="dropdown-item" wire:click="deleteDriver({{ $user->id }})">
                                                <i class="bx bx-trash me-1"></i> Delete
                                            </button>
                                        </div>
                                    </div>
                                </td>

                            </tr>
                            @endforeach
                            @else
                            <tr class="text-center">
                                <td colspan="15">
                                    No Record Found.
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>

                </div>
                <div class="container mt-4">
                    <div class="row">
                        <div class="col-12 d-flex align-items-center justify-content-end">
                            {{ $drivers->links('custom-pagination') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- / Content -->


        <div class="content-backdrop fade"></div>
    </div>
    <!-- Content wrapper -->
    <div wire:ignore.self class="modal fade" id="editDriver" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDriverTitle">Edit Driver</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mx-auto">
                            <form>

                                <div class="row">
                                    <div class="col-12">

                                        <!-- Full Name -->
                                        <div class="mb-3">
                                            <label class="form-label" for="full_name">Full Name</label>
                                            <div class="input-group input-group-merge">
                                                <input type="text" wire:model="full_name"
                                                    class="form-control @error('full_name') is-invalid @enderror"
                                                    placeholder="Enter full name">
                                            </div>
                                            @error('full_name')
                                            <div class="error">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Email -->
                                        <div class="mb-3">
                                            <label class="form-label" for="email">Email</label>
                                            <div class="input-group input-group-merge">
                                                <input type="email" wire:model="email"
                                                    class="form-control @error('email') is-invalid @enderror"
                                                    placeholder="Enter email">
                                            </div>
                                            @error('email')
                                            <div class="error">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Country Code -->
                                        <div class="mb-3">
                                            <label class="form-label" for="country_code">Country Code</label>
                                            <div class="input-group input-group-merge">
                                                <input type="text" wire:model="country_code"
                                                    class="form-control @error('country_code') is-invalid @enderror"
                                                    placeholder="Enter country code">
                                            </div>
                                            @error('country_code')
                                            <div class="error">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Phone Number -->
                                        <div class="mb-3">
                                            <label class="form-label" for="phone_number">Phone Number</label>
                                            <div class="input-group input-group-merge">
                                                <input type="text" wire:model="phone_number"
                                                    class="form-control @error('phone_number') is-invalid @enderror"
                                                    placeholder="Enter phone number">
                                            </div>
                                            @error('phone_number')
                                            <div class="error">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Language -->
                                        <div class="mb-3">
                                            <label class="form-label" for="language">Language</label>
                                            <div class="input-group input-group-merge">

                                                <select wire:model="language"
                                                    class="form-control @error('language') is-invalid @enderror">
                                                    <option value="">Select</option>
                                                    <option value="hi">Hindi</option>
                                                    <option value="en">English</option>
                                                    <option value="te">Telugu</option>
                                                </select>
                                            </div>
                                            @error('language')
                                            <div class="error">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Date of Birth -->
                                        <div class="mb-3">
                                            <label class="form-label" for="date_of_birth">Date of Birth</label>
                                            <div class="input-group input-group-merge">
                                                <input type="date" wire:model="date_of_birth"
                                                    class="form-control @error('date_of_birth') is-invalid @enderror"
                                                    placeholder="Select date of birth">
                                            </div>
                                            @error('date_of_birth')
                                            <div class="error">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Address -->
                                        <div class="mb-3">
                                            <label class="form-label" for="address">Address</label>
                                            <div class="input-group input-group-merge">
                                                <input type="text" wire:model="address"
                                                    class="form-control @error('address') is-invalid @enderror"
                                                    placeholder="Enter address">
                                            </div>
                                            @error('address')
                                            <div class="error">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Profile Photo -->
                                        <div class="mb-3 col-12">
                                            <label for="formFile" class="form-label">Upload Profile Image</label>
                                            <input class="form-control" type="file" wire:model="profile_photo">

                                            @error('profile_photo')
                                            <div style="color: red;">{{ $message }}</div>
                                            @enderror

                                            @if ($profile_photo)
                                            <div class="row mt-3">
                                                <div class="col-lg-4">
                                                    <img src="{{ $profile_photo->temporaryUrl() }}" alt="profile_photo"
                                                        class="img-fluid">
                                                </div>
                                            </div>
                                            @elseif($old_profile_photo)
                                            <div class="row mt-3">
                                                <div class="col-lg-4">
                                                    <img src="{{ asset($old_profile_photo) }}" alt="profile_photo"
                                                        class="img-fluid">
                                                </div>
                                            </div>
                                            @endif

                                        </div>

                                        <!-- Experience Years -->
                                        <div class="mb-3">
                                            <label class="form-label" for="experience_years">Experience
                                                (Years)</label>
                                            <div class="input-group input-group-merge">
                                                <input type="number" wire:model="experience_years"
                                                    class="form-control @error('experience_years') is-invalid @enderror"
                                                    placeholder="Enter years of experience">
                                            </div>
                                            @error('experience_years')
                                            <div class="error">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Available for Chauffeur -->
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    wire:model="available_for_chauffeur" id="available_for_chauffeur">
                                                <label class="form-check-label" for="available_for_chauffeur">Available
                                                    for Chauffeur</label>
                                            </div>
                                            @error('available_for_chauffeur')
                                            <div class="error">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Available for Trips -->
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    wire:model="available_for_trips" id="available_for_trips">
                                                <label class="form-check-label" for="available_for_trips">Available
                                                    for Trips</label>
                                            </div>
                                            @error('available_for_trips')
                                            <div class="error">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Joining Type -->
                                        <div class="mb-3">
                                            <label class="form-label" for="joining_type">Joining Type</label>
                                            <div class="input-group input-group-merge">
                                                <select wire:model="joining_type"
                                                    class="form-control @error('joining_type') is-invalid @enderror">
                                                    <option value="">Select joining type</option>
                                                    <option value="With Vehicle">With Vehicle</option>
                                                    <option value="Without Vehicle">Without Vehicle</option>
                                                </select>
                                            </div>
                                            @error('joining_type')
                                            <div class="error">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Vehicle Type -->
                                        <div class="mb-3">
                                            <label class="form-label" for="vehicle_type_id">Vehicle
                                                Type</label>
                                            <div class="input-group input-group-merge">
                                                <select wire:model.live="vehicle_type_id"
                                                    class="form-control @error('vehicle_type_id') is-invalid @enderror">
                                                    <option value="">Select Type</option>
                                                    @foreach ($vehicleTypes as $vehicleType)
                                                    <option value="{{ $vehicleType->id }}">
                                                        {{ $vehicleType->name }}
                                                    </option>
                                                    @endforeach
                                                </select>

                                            </div>


                                            @error('vehicle_type_id')
                                            <div class="error">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Vehicle Model -->
                                        <div class="mb-3">
                                            <label class="form-label" for="vehicle_subcategory_id">Vehicle
                                                Subcategory</label>
                                            <div class="input-group input-group-merge">
                                                <select wire:model="vehicle_subcategory_id"
                                                    class="form-control @error('vehicle_subcategory_id') is-invalid @enderror">
                                                    <option value="">Select Type</option>
                                                    @foreach ($vehicleSubcategories as $vehicleSubcategory)
                                                    <option value="{{ $vehicleSubcategory->id }}">
                                                        {{ $vehicleSubcategory->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @error('vehicle_subcategory_id')
                                            <div class="error">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="driving_license_number">Driving License
                                                Number</label>
                                            <div class="input-group input-group-merge">
                                                <input type="text" wire:model="driving_license_number"
                                                    class="form-control @error('driving_license_number') is-invalid @enderror"
                                                    placeholder="Enter registration number">
                                            </div>
                                            @error('driving_license_number')
                                            <div class="error">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="driving_license_photo">Driving License
                                                Number</label>
                                            <div class="input-group input-group-merge">
                                                <input type="file" wire:model="driving_license_photo"
                                                    class="form-control @error('driving_license_photo') is-invalid @enderror"
                                                    placeholder="Enter registration number">
                                            </div>
                                            @error('driving_license_photo')
                                            <div class="error">{{ $message }}</div>
                                            @enderror

                                            @if ($driving_license_photo)
                                            <div class="row mt-3">
                                                <div class="col-lg-4">
                                                    <img src="{{ $driving_license_photo->temporaryUrl() }}"
                                                        alt="driving_license_photo" class="img-fluid">
                                                </div>
                                            </div>
                                            @elseif($old_driving_license_photo)
                                            <div class="row mt-3">
                                                <div class="col-lg-4">
                                                    <img src="{{ asset($old_driving_license_photo) }}"
                                                        alt="driving_license_photo" class="img-fluid">
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="aadhar_pan_number">Aadhar/Pan
                                                Number</label>
                                            <div class="input-group input-group-merge">
                                                <input type="text" wire:model="aadhar_pan_number"
                                                    class="form-control @error('aadhar_pan_number') is-invalid @enderror"
                                                    placeholder="Enter Aadhar/Pan Number">
                                            </div>
                                            @error('aadhar_pan_number')
                                            <div class="error">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="aadhar_pan_photo">Aadhar/Pan Number</label>
                                            <div class="input-group input-group-merge">
                                                <input type="file" wire:model="aadhar_pan_photo"
                                                    class="form-control @error('aadhar_pan_photo') is-invalid @enderror"
                                                    placeholder="Enter Aadhar/Pan Number">
                                            </div>
                                            @error('aadhar_pan_photo')
                                            <div class="error">{{ $message }}</div>
                                            @enderror

                                            @if ($aadhar_pan_photo)
                                            <div class="row mt-3">
                                                <div class="col-lg-4">
                                                    <img src="{{ $aadhar_pan_photo->temporaryUrl() }}"
                                                        alt="aadhar_pan_photo" class="img-fluid">
                                                </div>
                                            </div>
                                            @elseif($old_aadhar_pan_photo)
                                            <div class="row mt-3">
                                                <div class="col-lg-4">
                                                    <img src="{{ asset($old_aadhar_pan_photo) }}" alt="aadhar_pan_photo"
                                                        class="img-fluid">
                                                </div>
                                            </div>
                                            @endif
                                        </div>

                                        <!-- Registration Number -->
                                        <div class="mb-3">
                                            <label class="form-label" for="registration_number">Registration
                                                Number</label>
                                            <div class="input-group input-group-merge">
                                                <input type="text" wire:model="registration_number"
                                                    class="form-control @error('registration_number') is-invalid @enderror"
                                                    placeholder="Enter registration number">
                                            </div>
                                            @error('registration_number')
                                            <div class="error">{{ $message }}</div>
                                            @enderror

                                        </div>

                                        <!-- Registration Photo -->
                                        <div class="mb-3">
                                            <label class="form-label" for="registration_photo">Registration
                                                Photo</label>
                                            <div class="input-group input-group-merge">
                                                <input type="file" wire:model="registration_photo"
                                                    class="form-control @error('registration_photo') is-invalid @enderror">
                                            </div>
                                            @error('registration_photo')
                                            <div class="error">{{ $message }}</div>
                                            @enderror

                                            @if ($registration_photo)
                                            <div class="row mt-3">
                                                <div class="col-lg-4">
                                                    <img src="{{ $registration_photo->temporaryUrl() }}"
                                                        alt="registration_photo" class="img-fluid">
                                                </div>
                                            </div>
                                            @elseif($old_registration_photo)
                                            <div class="row mt-3">
                                                <div class="col-lg-4">
                                                    <img src="{{ asset($old_registration_photo) }}"
                                                        alt="registration_photo" class="img-fluid">
                                                </div>
                                            </div>
                                            @endif
                                        </div>

                                        <!-- Insurance Photo -->
                                        <div class="mb-3">
                                            <label class="form-label" for="insurance_photo">Insurance Photo</label>
                                            <div class="input-group input-group-merge">
                                                <input type="file" wire:model="insurance_photo"
                                                    class="form-control @error('insurance_photo') is-invalid @enderror">
                                            </div>
                                            @error('insurance_photo')
                                            <div class="error">{{ $message }}</div>
                                            @enderror

                                            @if ($insurance_photo)
                                            <div class="row mt-3">
                                                <div class="col-lg-4">
                                                    <img src="{{ $insurance_photo->temporaryUrl() }}"
                                                        alt="insurance_photo" class="img-fluid">
                                                </div>
                                            </div>
                                            @elseif($old_insurance_photo)
                                            <div class="row mt-3">
                                                <div class="col-lg-4">
                                                    <img src="{{ asset($old_insurance_photo) }}" alt="insurance_photo"
                                                        class="img-fluid">
                                                </div>
                                            </div>
                                            @endif
                                        </div>

                                        <!-- Bank Account Number -->
                                        <div class="mb-3">
                                            <label class="form-label" for="bank_account_number">Bank Account
                                                Number</label>
                                            <div class="input-group input-group-merge">
                                                <input type="text" wire:model="bank_account_number"
                                                    class="form-control @error('bank_account_number') is-invalid @enderror"
                                                    placeholder="Enter bank account number">
                                            </div>
                                            @error('bank_account_number')
                                            <div class="error">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- IFSC Code -->
                                        <div class="mb-3">
                                            <label class="form-label" for="ifsc_code">IFSC Code</label>
                                            <div class="input-group input-group-merge">
                                                <input type="text" wire:model="ifsc_code"
                                                    class="form-control @error('ifsc_code') is-invalid @enderror"
                                                    placeholder="Enter IFSC code">
                                            </div>
                                            @error('ifsc_code')
                                            <div class="error">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Account Holder Name -->
                                        <div class="mb-3">
                                            <label class="form-label" for="account_holder_name">Account Holder
                                                Name</label>
                                            <div class="input-group input-group-merge">
                                                <input type="text" wire:model="account_holder_name"
                                                    class="form-control @error('account_holder_name') is-invalid @enderror"
                                                    placeholder="Enter account holder name">
                                            </div>
                                            @error('account_holder_name')
                                            <div class="error">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Qualifications -->
                                        <div class="mb-3">
                                            <label class="form-label" for="qualifications">Qualifications</label>
                                            <div class="input-group input-group-merge">
                                                <input type="text" wire:model="qualifications"
                                                    class="form-control @error('qualifications') is-invalid @enderror"
                                                    placeholder="Enter qualifications">
                                            </div>
                                            @error('qualifications')
                                            <div class="error">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Qualifications Attachments -->
                                        <div class="mb-3">
                                            <label class="form-label" for="qualifications_attachments">Qualifications
                                                Attachments</label>
                                            <div class="input-group input-group-merge">
                                                <input type="file" wire:model="qualifications_attachments" multiple
                                                    class="form-control @error('qualifications_attachments') is-invalid @enderror">
                                            </div>
                                            @error('qualifications_attachments')
                                            <div class="error">{{ $message }}</div>
                                            @enderror
                                            @if ($qualifications_attachments)
                                            @foreach ($qualifications_attachments as $qualifications_attachment)
                                            <div class="row mt-3">
                                                <div class="col-lg-4">
                                                    <img src="{{ $qualifications_attachment->temporaryUrl() }}"
                                                        alt="qualifications_attachment" class="img-fluid">
                                                </div>
                                            </div>
                                            @endforeach
                                            @endif

                                            @if ($old_qualifications_attachments)
                                            @foreach ($old_qualifications_attachments as $index =>
                                            $old_qualifications_attachment)
                                            <div class="row mt-3">
                                                <div class="col-lg-4">
                                                    <img src="{{ asset($old_qualifications_attachment) }}"
                                                        alt="qualifications_attachments" class="img-fluid">


                                                    <button wire:confirm="You want to delete this image?"
                                                        wire:click.prevent="deleteQualificationAttachment({{ $index }})"
                                                        class="btn-sm rounded-pill btn-icon btn-danger"><i
                                                            class="bx bxs-trash"></i></button>
                                                </div>
                                            </div>
                                            @endforeach
                                            @endif

                                        </div>

                                        <!-- Certifications -->
                                        <div class="mb-3">
                                            <label class="form-label" for="certifications">Certifications</label>
                                            <div class="input-group input-group-merge">
                                                <input type="file" wire:model="certifications" multiple
                                                    class="form-control @error('certifications') is-invalid @enderror">
                                            </div>
                                            @error('certifications')
                                            <div class="error">{{ $message }}</div>
                                            @enderror

                                            @if ($certifications)
                                            @foreach ($certifications as $certification)
                                            <div class="row mt-3">
                                                <div class="col-lg-4">
                                                    <img src="{{ $certification->temporaryUrl() }}"
                                                        alt="qualifications_attachment" class="img-fluid">
                                                </div>
                                            </div>
                                            @endforeach
                                            @endif

                                            @if ($old_certifications)
                                            @foreach ($old_certifications as $index => $old_certification)
                                            <div class="row mt-3">
                                                <div class="col-lg-4">
                                                    <img src="{{ asset($old_certification) }}" alt="certifications"
                                                        class="img-fluid">
                                                    <button wire:confirm="You want to delete this image?"
                                                        wire:click.prevent="deleteCertification({{ $index }})"
                                                        class="btn-sm rounded-pill btn-icon btn-danger"><i
                                                            class="bx bxs-trash"></i></button>

                                                </div>
                                            </div>
                                            @endforeach
                                            @endif
                                        </div>

                                        <!-- Emergency Contact Name -->
                                        <div class="mb-3">
                                            <label class="form-label" for="emergency_contact_name">Emergency Contact
                                                Name</label>
                                            <div class="input-group input-group-merge">
                                                <input type="text" wire:model="emergency_contact_name"
                                                    class="form-control @error('emergency_contact_name') is-invalid @enderror"
                                                    placeholder="Enter emergency contact name">
                                            </div>
                                            @error('emergency_contact_name')
                                            <div class="error">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Emergency Contact Number -->
                                        <div class="mb-3">
                                            <label class="form-label" for="emergency_contact_number">Emergency Contact
                                                Number</label>
                                            <div class="input-group input-group-merge">
                                                <input type="text" wire:model="emergency_contact_number"
                                                    class="form-control @error('emergency_contact_number') is-invalid @enderror"
                                                    placeholder="Enter emergency contact number">
                                            </div>
                                            @error('emergency_contact_number')
                                            <div class="error">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Documents -->
                                        {{-- <div class="mb-3">
                                            <label class="form-label" for="documents">Documents</label>
                                            <div class="input-group input-group-merge">
                                                <input type="file" wire:model="documents" multiple
                                                    class="form-control @error('documents') is-invalid @enderror">
                                            </div>
                                            @error('documents')
                                                <div class="error">{{ $message }}
                                    </div>
                                    @enderror
                                </div> --}}


                                <div class="mb-3">
                                    <label class="form-label" for="basic-icon-default-company">Approval
                                        Status</label>
                                    <div class="input-group input-group-merge">

                                        <select class="form-control @error('status') is-invalid @enderror"
                                            wire:model="status">
                                            <option value="pending">Pending</option>
                                            <option value="approved">Approved</option>
                                            <option value="rejected">Rejected</option>
                                        </select>
                                    </div>
                                    @error('status')
                                    <div class="error">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="basic-icon-default-company">Account
                                        Active Status</label>
                                    <div class="input-group input-group-merge">

                                        <select class="form-control @error('is_active') is-invalid @enderror"
                                            wire:model="is_active">
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                    @error('is_active')
                                    <div class="error">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="basic-icon-default-company">Account
                                        Status</label>
                                    <div class="input-group input-group-merge">

                                        <select class="form-control @error('account_status') is-invalid @enderror"
                                            wire:model="account_status">
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                    @error('account_status')
                                    <div class="error">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>


                                <div class="mb-3">
                                    <label class="form-label" for="basic-icon-default-company">Account
                                        Type</label>
                                    <div class="input-group input-group-merge">

                                        <select class="form-control @error('role') is-invalid @enderror"
                                            wire:model="role">
                                            <option value="driver">Driver</option>
                                            <option value="instructor">Instructor</option>
                                            <option value="both">Driver & Instructor</option>
                                        </select>
                                    </div>
                                    @error('role')
                                    <div class="error">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>



                        </div>



                    </div>



                    <button wire:click.prevent="updateDriver" class="btn btn-primary mt-5" wire:loading.attr="disabled">
                        <span wire:loading.remove>Submit</span>
                        <div wire:loading>
                            Loading...
                        </div>

                    </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@script
<script>
$wire.on('hideEditDriver', () => {
    $('#editDriver').modal('hide');
});
$wire.on('editDriver', () => {
    $('#editDriver').modal('show');
});
</script>
@endscript



@include('livewire.notification')
</div>