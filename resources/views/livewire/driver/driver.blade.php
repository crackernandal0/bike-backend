@section('drivers_active', 'open')
@section('driver_active', 'active')
<div>
    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->

        <div class="container-xxl flex-grow-1 container-p-y">

            <section>
                <div class="container">
                    <h4 class="fw-bold py-4"><span class="text-muted fw-light">Admin /</span> Drivers /
                        {{ $driver->full_name }}</h4>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="card mb-4">
                                <div class="card-body text-center">
                                    <p style="font-size: 12px" class="text-muted text-end mb-3"><span
                                            class="text-primary">Joined At:</span>
                                        {{ $driver->created_at ? \Carbon\Carbon::parse($driver->created_at)->format('d/m/Y h:i A') : 'N/A' }}
                                    </p>
                                    <div
                                        style="height: 150px; width: 150px; border-radius: 50%; overflow: hidden; margin: 0 auto;">
                                        <img style="width: 100%; height: 100%; object-fit: cover; object-position: center;"
                                            src="{{ $driver->profile_photo ? asset($driver->profile_photo) : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS-eCqzJ3AbzMWk_Fs4HmdmurRnrGuF6CKWHA&s' }}"
                                            alt="avatar">
                                    </div>
                                    <h5 class="mt-3">{{ $driver->full_name }}
                                        <p class="text-muted mb-1 mt-2 h6">
                                            {{ $driver->country_code . $driver->phone_number }}</p>
                                    </h5>

                                    <div class="d-flex align-items-center justify-content-center">
                                        <button wire:click="editDriver" class="btn btn-outline-primary">
                                            <i class="bx bxs-edit me-1"></i>
                                            Edit
                                        </button>
                                        <button wire:confirm="Are you sure you want to delete this user?"
                                            wire:click="deleteDriver" class="btn btn-outline-danger ms-3">
                                            <i class="bx bxs-trash me-1"></i>

                                            Delete
                                        </button>
                                    </div>

                                </div>
                            </div>
                            <div class="card mt-3">
                                <div class="card-body">
                                    <p class="mb-4"><span class="text-primary font-italic me-1">More Info</span></p>

                                    <p class="mb-1" style="font-size: .77rem;">Experience In years</p>
                                    <div class="my-2">
                                        <h6 class="mb-3">{{ $driver->experience_years ?? 'N/A' }}</h6>
                                    </div>

                                    <p class="mb-1" style="font-size: .77rem;">Role</p>
                                    <div class="my-2">
                                        <h6 class="mb-3">
                                            @if ($driver->role == 'both')
                                                Driver & Instructor
                                            @else
                                                {{ ucfirst($driver->role) }}
                                            @endif
                                        </h6>
                                    </div>

                                    <p class="mb-1" style="font-size: .77rem;">Available</p>
                                    <div class="my-2">
                                        @if ($driver->available)
                                            <img width="20" height="20"
                                                src="{{ asset('admin/assets/img/icons/unicons/verified.svg') }}"
                                                alt="check icon">
                                        @else
                                            <img width="15" height="15"
                                                src="{{ asset('admin/assets/img/icons/unicons/cross.svg') }}"
                                                alt="cross icon">
                                        @endif
                                    </div>

                                    <p class="mb-1" style="font-size: .77rem;">Available for Chauffeur</p>
                                    <div class="my-2">
                                        @if ($driver->available_for_chauffeur)
                                            <img width="20" height="20"
                                                src="{{ asset('admin/assets/img/icons/unicons/verified.svg') }}"
                                                alt="check icon">
                                        @else
                                            <img width="15" height="15"
                                                src="{{ asset('admin/assets/img/icons/unicons/cross.svg') }}"
                                                alt="cross icon">
                                        @endif
                                    </div>

                                    <p class="mb-1" style="font-size: .77rem;">Available for Trips</p>
                                    <div class="my-2">
                                        @if ($driver->available_for_trips)
                                            <img width="20" height="20"
                                                src="{{ asset('admin/assets/img/icons/unicons/verified.svg') }}"
                                                alt="check icon">
                                        @else
                                            <img width="15" height="15"
                                                src="{{ asset('admin/assets/img/icons/unicons/cross.svg') }}"
                                                alt="cross icon">
                                        @endif
                                    </div>


                                    <p class="mb-1" style="font-size: .77rem;">Total Accepts</p>
                                    <div class="my-2">
                                        <h6 class="mb-3">{{ $driver->total_accepts ?? 'N/A' }}</h6>
                                    </div>

                                    <p class="mb-1" style="font-size: .77rem;">Total Rejects</p>
                                    <div class="my-2">
                                        <h6 class="mb-3">{{ $driver->total_rejects ?? 'N/A' }}</h6>
                                    </div>

                                    <p class="mb-1" style="font-size: .77rem;">Total Students</p>
                                    <div class="my-2">
                                        <h6 class="mb-3">{{ $driver->total_students ?? 'N/A' }}</h6>
                                    </div>

                                    <p class="mb-1" style="font-size: .77rem;">Total Ratings</p>
                                    <div class="my-2">
                                        <h6 class="mb-3">{{ $driver->total_ratings ?? 'N/A' }}</h6>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8 mt-4 mt-lg-0">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="row">
                                        <p class="mb-4"><span class="text-primary font-italic me-1">Account
                                                Details</span>
                                        </p>
                                        <div class="col-sm-3">
                                            <p class="mb-0">Full Name</p>
                                        </div>
                                        <div class="col-sm-9">
                                            <p class="text-muted mb-0">{{ $driver->full_name }}
                                            </p>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <p class="mb-0">Email</p>
                                        </div>
                                        <div class="col-sm-9">
                                            <p class="text-muted mb-0"><a style="word-break: break-all;"
                                                    href="mailto:{{ $driver->email }}">{{ $driver->email }}</a></p>
                                        </div>
                                    </div>
                                    <hr>

                                    <div class="row">
                                        <div class="col-sm-3">
                                            <p class="mb-0">Address</p>
                                        </div>
                                        <div class="col-sm-9">
                                            <p class="text-muted mb-0">{{ $driver->address }}</p>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <p class="mb-0">Phone number</p>
                                        </div>
                                        <div class="col-sm-9">
                                            <p class="text-muted mb-0">
                                                {{ $driver->country_code . $driver->phone_number ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <p class="mb-0">Date Of
                                                Bith</p>
                                        </div>
                                        <div class="col-sm-9">
                                            <p class="text-muted mb-0">{{ $driver->date_of_birth ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <p class="mb-0">Language</p>
                                        </div>
                                        <div class="col-sm-9">
                                            <p class="text-muted mb-0">{{ $driver->language ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <p class="mb-0">Account Created At</p>
                                        </div>
                                        <div class="col-sm-9">
                                            <p class="text-muted mb-0">
                                                {{ $driver->created_at->format('d/m/Y h:i A') }}
                                            </p>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <p class="mb-0">On Duty</p>
                                        </div>
                                        <div class="col-sm-3">
                                            <p class="text-muted mb-0">
                                                @if ($driver->active)
                                                    <img width="20" height="20"
                                                        src="{{ asset('admin/assets/img/icons/unicons/verified.svg') }}"
                                                        alt="check icon">
                                                @else
                                                    <img width="15" height="15"
                                                        src="{{ asset('admin/assets/img/icons/unicons/cross.svg') }}"
                                                        alt="cross icon">
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-sm-3 mt-3 mt-lg-0">
                                            <p class="mb-0">Account
                                                Status</p>
                                        </div>
                                        <div class="col-sm-3">
                                            <p class="text-muted mb-0">
                                                @if ($driver->account_status)
                                                    <img width="20" height="20"
                                                        src="{{ asset('admin/assets/img/icons/unicons/verified.svg') }}"
                                                        alt="check icon">
                                                @else
                                                    <img width="15" height="15"
                                                        src="{{ asset('admin/assets/img/icons/unicons/cross.svg') }}"
                                                        alt="cross icon">
                                                @endif
                                            </p>
                                        </div>
                                    </div>


                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="card mb-4 mb-md-0">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-12">
                                                    <p class="mb-4"><span
                                                            class="text-primary font-italic me-1">Assigned Vehicle
                                                            Info</span>
                                                    </p>

                                                    <!-- Vehicle Type -->
                                                    <p class="mb-1" style="font-size: .77rem;">Vehicle Type</p>
                                                    <div class="my-2">
                                                        @if ($driver->vehicleType)
                                                            <h6 class="mb-3">
                                                                <img class="me-2" height="15"
                                                                    src="{{ asset($driver->vehicleType->icon) }}"
                                                                    alt="vehicle icon">
                                                                {{ $driver->vehicleType->name ?? 'N/A' }}
                                                            </h6>
                                                        @else
                                                            <h6 class="mb-3">N/A</h6>
                                                        @endif
                                                    </div>

                                                    <!-- Vehicle Subcategory -->
                                                    <p class="mb-1" style="font-size: .77rem;">Vehicle Subcategory
                                                    </p>
                                                    <div class="my-2">
                                                        <h6 class="mb-3">
                                                            {{ $driver->vehicleSubcategory->name ?? 'N/A' }}</h6>
                                                    </div>

                                                    <!-- Service Location -->
                                                    <p class="mb-1" style="font-size: .77rem;">Service Location</p>
                                                    <div class="my-2">
                                                        <h6 class="mb-3">
                                                            {{ $driver->serviceLocation->name ?? 'N/A' }}</h6>
                                                    </div>

                                                    <!-- Joining Type -->
                                                    <p class="mb-1" style="font-size: .77rem;">Joining Type</p>
                                                    <div class="my-2">
                                                        <h6 class="mb-3">{{ $driver->joining_type ?? 'N/A' }}</h6>
                                                    </div>



                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 mt-4">
                                    <div class="card mb-4 mb-md-0">
                                        <div class="card-body">
                                            <div class="row">


                                                <!-- Additional Driver Info -->
                                                @if ($driver->additionalInfo)
                                                    <div class="col-12">
                                                        <p class="mb-4"><span
                                                                class="text-primary font-italic me-1">Driver's
                                                                Additional Info</span></p>

                                                        <!-- Additional Requests -->
                                                        <p class="mb-1" style="font-size: .77rem;">Additional
                                                            Requests</p>
                                                        <div class="my-2">
                                                            <h6 class="mb-3">
                                                                {{ $driver->additionalInfo->additional_requests ?? 'N/A' }}
                                                            </h6>
                                                        </div>

                                                        <!-- Service Preferences -->
                                                        <p class="mb-1" style="font-size: .77rem;">Service
                                                            Preferences</p>
                                                        <div class="my-2">
                                                            <h6 class="mb-3">
                                                                {{ $driver->additionalInfo->service_preferences ?? 'N/A' }}
                                                            </h6>
                                                        </div>

                                                        <!-- Availability -->
                                                        <p class="mb-1" style="font-size: .77rem;">Available From
                                                        </p>
                                                        <div class="my-2">
                                                            <h6 class="mb-3">
                                                                {{ $driver->additionalInfo->available_from ?? 'N/A' }}
                                                            </h6>
                                                        </div>

                                                        <!-- Emergency Contact -->
                                                        <p class="mb-1" style="font-size: .77rem;">Emergency Contact
                                                        </p>
                                                        <div class="my-2">
                                                            <h6 class="mb-3">Name:
                                                                {{ $driver->additionalInfo->emergency_contact_name ?? 'N/A' }}
                                                            </h6>
                                                            <h6 class="mb-3">Phone Number:
                                                                {{ $driver->additionalInfo->emergency_contact_number ?? 'N/A' }}
                                                            </h6>
                                                        </div>

                                                        <!-- Qualifications -->
                                                        <p class="mb-1" style="font-size: .77rem;">Qualifications
                                                        </p>
                                                        <div class="my-2">
                                                            <h6 class="mb-3">
                                                                {{ $driver->additionalInfo->qualifications ?? 'N/A' }}
                                                            </h6>
                                                        </div>

                                                        <!-- Qualifications Attachments -->
                                                        <p class="mb-1" style="font-size: .77rem;">Qualifications
                                                            Attachments</p>
                                                        <div class="my-2">
                                                            @if (!empty($driver->additionalInfo->qualifications_attachments))
                                                                @foreach (json_decode($driver->additionalInfo->qualifications_attachments) as $attachment)
                                                                    <a target="_blank"
                                                                        href="{{ asset($attachment) }}">
                                                                        <img height="100"
                                                                            src="{{ asset($attachment) }}"
                                                                            alt="qualification attachment">
                                                                    </a>
                                                                @endforeach
                                                            @else
                                                                <h6 class="mb-3">N/A</h6>
                                                            @endif
                                                        </div>

                                                        <!-- Certifications -->
                                                        <p class="mb-1" style="font-size: .77rem;">Certifications
                                                        </p>
                                                        <div class="my-2">
                                                            @if (!empty($driver->additionalInfo->certifications))
                                                                @foreach (json_decode($driver->additionalInfo->certifications) as $certification)
                                                                    <a target="_blank"
                                                                        href="{{ asset($certification) }}">

                                                                        <img height="100"
                                                                            src="{{ asset($certification) }}"
                                                                            alt="certification">
                                                                    </a>
                                                                @endforeach
                                                            @else
                                                                <h6 class="mb-3">N/A</h6>
                                                            @endif
                                                        </div>

                                                        <!-- Training Specializations -->
                                                        <p class="mb-1" style="font-size: .77rem;">Training
                                                            Specializations</p>
                                                        <div class="my-2">
                                                            <h6 class="mb-3">
                                                                {{ $driver->additionalInfo->training_specializations ?? 'N/A' }}
                                                            </h6>
                                                        </div>
                                                    </div>
                                                @else
                                                    <h6 class="mb-3">No Additional Info Available</h6>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 mt-4">
                                    <div class="card mb-4 mb-md-0">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-12">
                                                    <p class="mb-4"><span
                                                            class="text-primary font-italic me-1">Driver
                                                            Documents</span></p>

                                                    <!-- Documents Loop -->
                                                    @if ($driver->documents && $driver->documents->isNotEmpty())
                                                        @foreach ($driver->documents as $document)
                                                            <!-- Document Type -->
                                                            <p class="mb-1" style="font-size: .77rem;">Document Type
                                                            </p>
                                                            <div class="my-2">
                                                                <h6 class="mb-3">
                                                                    {{ $document->document_type ?? 'N/A' }}</h6>
                                                            </div>

                                                            <!-- Document Number -->
                                                            <p class="mb-1" style="font-size: .77rem;">Document
                                                                Number</p>
                                                            <div class="my-2">
                                                                <h6 class="mb-3">
                                                                    {{ $document->document_number ?? 'N/A' }}</h6>
                                                            </div>

                                                            <!-- Document Photo -->
                                                            <p class="mb-1" style="font-size: .77rem;">Document
                                                                Photo</p>
                                                            <div class="my-2">
                                                                @if ($document->document_photo)
                                                                    <img width="100" height="100"
                                                                        src="{{ asset($document->document_photo) }}"
                                                                        alt="document photo">
                                                                @else
                                                                    <h6 class="mb-3">N/A</h6>
                                                                @endif
                                                            </div>

                                                            <hr />
                                                        @endforeach
                                                    @else
                                                        <h6 class="mb-3">No Documents Available</h6>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-12 mt-4">
                                    <div class="card mb-4 mb-md-0">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-12">
                                                    <p class="mb-4"><span
                                                            class="text-primary font-italic me-1">Driver Bank
                                                            Info</span></p>

                                                    @if ($driver->bankInfo)
                                                        <!-- Account Holder Name -->
                                                        <p class="mb-1" style="font-size: .77rem;">Account Holder
                                                            Name</p>
                                                        <div class="my-2">
                                                            <h6 class="mb-3">
                                                                {{ $driver->bankInfo->account_holder_name ?? 'N/A' }}
                                                            </h6>
                                                        </div>

                                                        <!-- Bank Account Number -->
                                                        <p class="mb-1" style="font-size: .77rem;">Bank Account
                                                            Number</p>
                                                        <div class="my-2">
                                                            <h6 class="mb-3">
                                                                {{ $driver->bankInfo->bank_account_number ?? 'N/A' }}
                                                            </h6>
                                                        </div>

                                                        <!-- IFSC Code -->
                                                        <p class="mb-1" style="font-size: .77rem;">IFSC Code</p>
                                                        <div class="my-2">
                                                            <h6 class="mb-3">
                                                                {{ $driver->bankInfo->ifsc_code ?? 'N/A' }}</h6>
                                                        </div>
                                                    @else
                                                        <h6 class="mb-3">No Bank Info Available</h6>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
            </section>
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
                                                        <img src="{{ $profile_photo->temporaryUrl() }}"
                                                            alt="profile_photo" class="img-fluid">
                                                    </div>
                                                </div>
                                            @elseif($old_profile_photo)
                                                <div class="row mt-3">
                                                    <div class="col-lg-4">
                                                        <img src="{{ asset($old_profile_photo) }}"
                                                            alt="profile_photo" class="img-fluid">
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
                                                <label class="form-check-label"
                                                    for="available_for_chauffeur">Available for Chauffeur</label>
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
                                                        <img src="{{ asset($old_aadhar_pan_photo) }}"
                                                            alt="aadhar_pan_photo" class="img-fluid">
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
                                                        <img src="{{ asset($old_insurance_photo) }}"
                                                            alt="insurance_photo" class="img-fluid">
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
                                                @foreach ($old_qualifications_attachments as $index => $old_qualifications_attachment)
                                                    <div class="row mt-3">
                                                        <div class="col-lg-4">
                                                            <img src="{{ asset($old_qualifications_attachment) }}"
                                                                alt="qualifications_attachments" class="img-fluid">
                                                         

                                                            <button wire:confirm="You want to delete this image?"  wire:click.prevent="deleteQualificationAttachment({{ $index }})" class="btn-sm rounded-pill btn-icon btn-danger"><i class="bx bxs-trash"></i></button>
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
                                                            <img src="{{ asset($old_certification) }}"
                                                                alt="certifications" class="img-fluid">
                                                                <button wire:confirm="You want to delete this image?"  wire:click.prevent="deleteCertification({{ $index }})" class="btn-sm rounded-pill btn-icon btn-danger"><i class="bx bxs-trash"></i></button>
                                                   
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
                                                <div class="error">{{ $message }}</div>
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

                                                <select
                                                    class="form-control @error('account_status') is-invalid @enderror"
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



                                <button wire:click.prevent="updateDriver" class="btn btn-primary mt-5"
                                    wire:loading.attr="disabled">
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
