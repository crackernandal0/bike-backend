@section('chauffeurs_active', 'open')
@section('chauffeurs_profile_active', 'active')
<div>
    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->

        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Admin /</span> Chauffeurs</h4>

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
                                <th style="font-size: 11px;white-space:nowrap">Image</th>
                                <th style="font-size: 11px;white-space:nowrap">Name</th>
                                <th style="font-size: 11px;white-space:nowrap">Phone Number</th>
                                <th style="font-size: 11px;white-space:nowrap">Staus</th>
                                <th style="font-size: 11px;white-space:nowrap">View</th>
                                <th style="font-size: 11px;white-space:nowrap">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @if (count($chauffeurs) > 0)
                                @foreach ($chauffeurs as $index => $user)
                                    <tr class="cursor-pointer">
                                        <td>
                                            {{ ($chauffeurs->currentPage() - 1) * $chauffeurs->perPage() + $index + 1 }}
                                        </td>

                                        <td style="white-space:nowrap"><img height="50" style="border-radius: 5px"
                                                src="{{ asset($user->image) }}" alt="chauffeur"></td>


                                        <td style="white-space:nowrap">{{ $user->driver->full_name ?? 'N/A' }}</td>


                                        <td style="white-space:nowrap"><a style="word-break: break-all;"
                                                class="text-decoration-underline"
                                                href="tel:{{ $user->driver->country_code ?? 'N/A' }}{{ $user->driver->phone_number ?? 'N/A' }}">{{ $user->driver->country_code ?? 'N/A' }}{{ $user->driver->phone_number ?? 'N/A' }}</a>
                                        </td>




                                        <td>
                                            @if ($user->status == 'pending')
                                                <button class="btn-sm btn-warning">
                                                    Pending
                                                </button>
                                            @elseif($user->status == 'approved')
                                                <button class="btn-sm btn-primary">
                                                    Approved
                                                </button>
                                            @elseif($user->status == 'rejected')
                                                <button class="btn-sm btn-danger">
                                                    Rejected
                                                </button>
                                            @endif
                                        </td>

                                        <td>
                                            <div class="d-flex align-items-center">
                                                <button style="background: none; outline:none;border:none;"
                                                    wire:click="showRecord({{ $user->id }})">

                                                    <i style="font-size:15px; font-weight:600;"
                                                        class="bx bx-show text-primary"></i>
                                                </button>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="dropdown dropup">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                    data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu" data-popper-placement="top-start">
                                                    <button class="dropdown-item"
                                                        wire:click="editChauffeur({{ $user->id }})">
                                                        <i class="bx bx-pencil me-1"></i> Edit
                                                    </button>
                                                    <button class="dropdown-item"
                                                        wire:click="deleteChauffeur({{ $user->id }})">
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
                            {{ $chauffeurs->links('custom-pagination') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- / Content -->


        <div class="content-backdrop fade"></div>
    </div>
    <!-- Content wrapper -->
    <div wire:ignore.self class="modal fade" id="editChauffeur" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editChauffeurTitle">Edit Driver</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mx-auto">
                            <div>

                                <div class="row">
                                    <div class="col-12">

                                        <div>
                                            <!-- Tagline -->
                                            <div class="mb-3">
                                                <label class="form-label">Tagline</label>
                                                <input type="text" wire:model="tagline"
                                                    class="form-control @error('tagline') is-invalid @enderror">
                                                @error('tagline')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Description -->
                                            <div class="mb-3">
                                                <label class="form-label">Description</label>
                                                <textarea wire:model="description" class="form-control @error('description') is-invalid @enderror"></textarea>
                                                @error('description')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Availability -->
                                            <div class="mb-3">
                                                <label class="form-label">Availability</label>
                                                <input type="text" wire:model="availability"
                                                    class="form-control @error('availability') is-invalid @enderror">
                                                @error('availability')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Skills & Certifications (Dynamic Fields) -->
                                            <div>
                                                <label class="form-label">Skills & Certifications</label>
                                                @foreach ($skills_certifications as $index => $skill)
                                                    <div class="input-group mb-2">
                                                        <input type="text"
                                                            wire:model="skills_certifications.{{ $index }}"
                                                            class="form-control" placeholder="Skill/Certification">
                                                        <button class="btn btn-danger"
                                                            wire:click.prevent="removeSkill({{ $index }})">Remove</button>
                                                    </div>
                                                @endforeach
                                                <button class="btn btn-primary mt-2" wire:click.prevent="addSkill">Add
                                                    Skill/Certification</button>
                                            </div>

                                            <!-- Additional Services (Dynamic Fields) -->
                                            <div class="mt-4">
                                                <label class="form-label">Additional Services</label>
                                                @foreach ($additional_services as $index => $service)
                                                    <div class="input-group mb-2">
                                                        <input type="text"
                                                            wire:model="additional_services.{{ $index }}"
                                                            class="form-control" placeholder="Service">
                                                        <button class="btn btn-danger"
                                                            wire:click.prevent="removeService({{ $index }})">Remove</button>
                                                    </div>
                                                @endforeach
                                                <button class="btn btn-primary mt-2"
                                                    wire:click.prevent="addService">Add
                                                    Service</button>
                                            </div>

                                            <!-- Status Dropdown -->
                                            <div class="mb-3 mt-4">
                                                <label class="form-label">Status</label>
                                                <select wire:model="status"
                                                    class="form-select @error('status') is-invalid @enderror">
                                                    <option value="pending">Pending</option>
                                                    <option value="approved">Approved</option>
                                                    <option value="declined">Declined</option>
                                                </select>
                                                @error('status')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Profile Image Upload -->
                                            <div class="mb-3">
                                                <label class="form-label">Profile Image</label>
                                                <input type="file" wire:model="image" class="form-control">
                                                @error('image')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror

                                                <!-- Show current image or newly uploaded image -->
                                                @if ($image)
                                                    <img height="100" src="{{ $image->temporaryUrl() }}"
                                                        class="mt-2 ms-2">
                                                @elseif ($old_image)
                                                    <img height="100" src="{{ asset($old_image) }}"
                                                        class="mt-2 ms-2">
                                                @endif
                                            </div>


                                        </div>



                                    </div>
                                </div>



                                <button wire:click.prevent="updateChauffeur" class="btn btn-primary mt-5"
                                    wire:loading.attr="disabled">
                                    <span wire:loading.remove>Submit</span>
                                    <div wire:loading>
                                        Loading...
                                    </div>

                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="selectedRecordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            @if ($selectedRecord)
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="selectedRecordModalTitle">Chauffeur Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <table class="table table-borderless">
                                    <tbody>

                                        <!-- Chauffeur Details Table -->
                                        <table class="table table-striped">
                                            <!-- Chauffeur Name -->
                                            <tr>
                                                <td style="width: 50%; text-wrap: wrap;" class="align-middle">
                                                    <small class="text-light fw-semibold">Chauffeur Name</small>
                                                </td>
                                                <td style="width: 50%; text-wrap: wrap;" class="py-3">
                                                    <h6 class="mb-0">
                                                        {{ $selectedRecord->driver->full_name ?? 'N/A' }}</h6>
                                                </td>
                                            </tr>

                                            <!-- Chauffeur Tagline -->
                                            <tr>
                                                <td style="width: 100%; text-wrap: wrap;" class="align-middle">
                                                    <small class="text-light fw-semibold">Tagline</small>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 100%; text-wrap: wrap;" class="py-3">
                                                    <h6 class="mb-0">{{ $selectedRecord->tagline ?? 'N/A' }}</h6>
                                                </td>
                                            </tr>

                                            <!-- Chauffeur Description -->
                                            <tr>
                                                <td style="width: 100%; text-wrap: wrap;" class="align-middle">
                                                    <small class="text-light fw-semibold">Description</small>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 100%; text-wrap: wrap;" class="py-3">
                                                    <h6 class="mb-0">{{ $selectedRecord->description ?? 'N/A' }}
                                                    </h6>
                                                </td>
                                            </tr>

                                            <!-- Chauffeur Image -->
                                            <tr>
                                                <td style="width: 50%; text-wrap: wrap;" class="align-middle">
                                                    <small class="text-light fw-semibold">Image</small>
                                                </td>
                                                <td style="width: 50%; text-wrap: wrap;" class="py-3">
                                                    @if ($selectedRecord->image)
                                                        <img src="{{ asset($selectedRecord->image) }}"
                                                            alt="Chauffeur Image" class="img-fluid" width="100">
                                                    @else
                                                        <span>N/A</span>
                                                    @endif
                                                </td>
                                            </tr>

                                            <!-- Skills Certifications (JSON Field) -->
                                            <tr>
                                                <td style="width: 100%; text-wrap: wrap;" class="align-middle">
                                                    <small class="text-light fw-semibold">Skills Certifications</small>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 100%; text-wrap: wrap;" class="py-3">
                                                    @if ($selectedRecord->skills_certifications)
                                                        <ul>
                                                            @foreach (json_decode($selectedRecord->skills_certifications, true) as $skill)
                                                                <li>{{ $skill }}</li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <span>N/A</span>
                                                    @endif
                                                </td>
                                            </tr>

                                            <!-- Additional Services (JSON Field) -->
                                            <tr>
                                                <td style="width: 100%; text-wrap: wrap;" class="align-middle">
                                                    <small class="text-light fw-semibold">Additional Services</small>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 100%; text-wrap: wrap;" class="py-3">
                                                    @if ($selectedRecord->additional_services)
                                                        <ul>
                                                            @foreach (json_decode($selectedRecord->additional_services, true) as $service)
                                                                <li>{{ $service }}</li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <span>N/A</span>
                                                    @endif
                                                </td>
                                            </tr>

                                            <!-- Chauffeur Availability -->
                                            <tr>
                                                <td style="width: 50%; text-wrap: wrap;" class="align-middle">
                                                    <small class="text-light fw-semibold">Availability</small>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 50%; text-wrap: wrap;" class="py-3">
                                                    <h6 class="mb-0">{{ $selectedRecord->availability ?? 'N/A' }}
                                                    </h6>
                                                </td>
                                            </tr>

                                            <!-- Chauffeur Status -->
                                            <tr>
                                                <td style="width: 50%; text-wrap: wrap;" class="align-middle">
                                                    <small class="text-light fw-semibold">Status</small>
                                                </td>
                                                <td style="width: 50%; text-wrap: wrap;" class="py-3">
                                                    <h6 class="mb-0">{{ ucfirst($selectedRecord->status ?? 'N/A') }}
                                                    </h6>
                                                </td>
                                            </tr>

                                            <!-- Chauffeur Driver Details (Phone Number, etc.) -->
                                            <tr>
                                                <td style="width: 50%; text-wrap: wrap;" class="align-middle">
                                                    <small class="text-light fw-semibold">Driver Phone Number</small>
                                                </td>
                                                <td style="width: 50%; text-wrap: wrap;" class="py-3">
                                                    <h6 class="mb-0">
                                                        {{ $selectedRecord->driver->country_code ?? '' }}
                                                        {{ $selectedRecord->driver->phone_number ?? 'N/A' }}</h6>
                                                </td>
                                            </tr>

                                        </table>




                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @script
        <script>
            $wire.on('show-record', () => {
                $('#selectedRecordModal').modal('show');
            });
            $wire.on('hideEditChauffeur', () => {
                $('#editChauffeur').modal('hide');
            });
            $wire.on('editChauffeur', () => {
                $('#editChauffeur').modal('show');
            });
        </script>
    @endscript



    @include('livewire.notification')
</div>
