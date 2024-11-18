@section('chauffeurs_active', 'open')
@section('chauffeur_bookings_active', 'active')
<div>
    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->

        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Admin /</span> Chauffeur Bookings</h4>

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
                                <th style="font-size: 11px;white-space:nowrap">Pickup-Dropoff</th>
                                <th style="font-size: 11px;white-space:nowrap">User Name</th>
                                <th style="font-size: 11px;white-space:nowrap">Phone Number</th>
                                <th style="font-size: 11px;white-space:nowrap">Chauffeur</th>
                                <th style="font-size: 11px;white-space:nowrap">Status</th>
                                <th style="font-size: 11px;white-space:nowrap">View</th>
                                <th style="font-size: 11px;white-space:nowrap">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @if (count($chauffeurBookings) > 0)
                                @foreach ($chauffeurBookings as $index => $chauffeur)
                                    <tr class="cursor-pointer">
                                        <td>
                                            {{ ($chauffeurBookings->currentPage() - 1) * $chauffeurBookings->perPage() + $index + 1 }}
                                        </td>



                                        <!-- Pickup-Dropoff -->
                                        <td style="font-size: 14px;">
                                            <strong>Pickup:</strong> {{ $chauffeur->pickup ?? 'N/A' }}<br>
                                            <strong>Dropoff:</strong> {{ $chauffeur->dropoff ?? 'N/A' }}
                                        </td>

                                        <!-- User Name -->
                                        <td>
                                            {{ $chauffeur->user->name ?? 'N/A' }}
                                        </td>

                                        <!-- Phone Number -->
                                        <td>
                                            <a
                                                href="tel:{{ $chauffeur->user->country_code ?? '' }}{{ $chauffeur->user->phone_number ?? '' }}">
                                                {{ $chauffeur->user->phone_number ?? 'N/A' }}</a>

                                        </td>

                                        <!-- Chauffeur Name -->
                                        <td>
                                            {{ $chauffeur->chauffeur->driver->full_name ?? 'N/A' }}
                                        </td>


                                        <td>
                                            @if ($chauffeur->status == 'pending')
                                                <button class="btn-sm btn-warning">
                                                    Pending
                                                </button>
                                            @elseif($chauffeur->status == 'approved')
                                                <button class="btn-sm btn-primary">
                                                    Approved
                                                </button>
                                            @elseif($chauffeur->status == 'rejected')
                                                <button class="btn-sm btn-danger">
                                                    Rejected
                                                </button>
                                            @elseif($chauffeur->status == 'canceled')
                                                <button class="btn-sm btn-danger">
                                                    Canceled
                                                </button>
                                            @elseif($chauffeur->status == 'service_stopped')
                                                <button class="btn-sm btn-secondary">
                                                    Service Stopped
                                                </button>
                                            @elseif($chauffeur->status == 'completed')
                                                <button class="btn-sm btn-indo">
                                                    Completed
                                                </button>
                                            @endif
                                        </td>

                                        <td>
                                            <div class="d-flex align-items-center">
                                                <button style="background: none; outline:none;border:none;"
                                                    wire:click="showRecord({{ $chauffeur->id }})">

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
                                                        wire:click="editBooking({{ $chauffeur->id }})">
                                                        <i class="bx bx-pencil me-1"></i> Edit
                                                    </button>
                                                    <button wire:confir="Are you sure you want to delete this record?" class="dropdown-item"
                                                        wire:click="deleteBooking({{ $chauffeur->id }})">
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
                            {{ $chauffeurBookings->links('custom-pagination') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- / Content -->


        <div class="content-backdrop fade"></div>
    </div>
    <!-- Content wrapper -->
    <div wire:ignore.self class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalTitle">Edit Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mx-auto">
                            <div>

                                <div class="row">
                                    <div class="col-12">

                                        <div>
                                            <!-- Chauffeur ID -->
                                            <div class="mb-3">
                                                <label class="form-label">Chauffeur</label>

                                                <select wire:model="chauffeur_id"
                                                    class="form-control @error('chauffeur_id') is-invalid @enderror">
                                                    @foreach ($chauffeurs as $chauffeur)
                                                        <option value="{{ $chauffeur->id }}">
                                                            {{ $chauffeur->driver->full_name ?? $chauffeur->tagline }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                @error('chauffeur_id')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Pickup -->
                                            <div class="mb-3">
                                                <label class="form-label">Pickup</label>
                                                <input type="text" wire:model="pickup"
                                                    class="form-control @error('pickup') is-invalid @enderror">
                                                @error('pickup')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Dropoff -->
                                            <div class="mb-3">
                                                <label class="form-label">Dropoff</label>
                                                <input type="text" wire:model="dropoff"
                                                    class="form-control @error('dropoff') is-invalid @enderror">
                                                @error('dropoff')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Pickup Location Type -->
                                            <div class="mb-3">
                                                <label class="form-label">Pickup Location Type</label>
                                                <input type="text" wire:model="pickup_location_type"
                                                    class="form-control @error('pickup_location_type') is-invalid @enderror">
                                                @error('pickup_location_type')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Destination Location Type -->
                                            <div class="mb-3">
                                                <label class="form-label">Destination Location Type</label>
                                                <input type="text" wire:model="destination_location_type"
                                                    class="form-control @error('destination_location_type') is-invalid @enderror">
                                                @error('destination_location_type')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Date -->
                                            <div class="mb-3">
                                                <label class="form-label">Date</label>
                                                <input type="date" wire:model="date"
                                                    class="form-control @error('date') is-invalid @enderror">
                                                @error('date')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Start Time -->
                                            <div class="mb-3">
                                                <label class="form-label">Start Time</label>
                                                <input type="time" wire:model="start_time"
                                                    class="form-control @error('start_time') is-invalid @enderror">
                                                @error('start_time')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- End Time -->
                                            <div class="mb-3">
                                                <label class="form-label">End Time</label>
                                                <input type="time" wire:model="end_time"
                                                    class="form-control @error('end_time') is-invalid @enderror">
                                                @error('end_time')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Vehicle Type -->
                                            <div class="mb-3">
                                                <label class="form-label">Vehicle Type</label>
                                                <input type="text" wire:model="vehicle_type"
                                                    class="form-control @error('vehicle_type') is-invalid @enderror">
                                                @error('vehicle_type')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Preferred Vehicle -->
                                            <div class="mb-3">
                                                <label class="form-label">Preferred Vehicle</label>
                                                <input type="text" wire:model="preferred_vehicle"
                                                    class="form-control @error('preferred_vehicle') is-invalid @enderror">
                                                @error('preferred_vehicle')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Chauffeur Type -->
                                            <div class="mb-3">
                                                <label class="form-label">Chauffeur Type</label>

                                                <select wire:model="chauffeur_type"
                                                class="form-control @error('chauffeur_type') is-invalid @enderror">
                                               <option value="with_vehicle">With Vehicle</option>
                                               <option value="without_vehicle">Without Vehicle</option>
                                            </select>
                                                @error('chauffeur_type')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Hire Type -->
                                            <div class="mb-3">
                                                <label class="form-label">Hire Type</label>
                                                <input type="text" wire:model="hire_type"
                                                    class="form-control @error('hire_type') is-invalid @enderror">
                                                @error('hire_type')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Event Type -->
                                            <div class="mb-3">
                                                <label class="form-label">Event Type</label>
                                                <input type="text" wire:model="event_type"
                                                    class="form-control @error('event_type') is-invalid @enderror">
                                                @error('event_type')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Child Seats -->
                                            <div class="mb-3">
                                                <label class="form-label">Child Seats</label>
                                                <input type="number" wire:model="child_seats"
                                                    class="form-control @error('child_seats') is-invalid @enderror">
                                                @error('child_seats')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Specific Vehicle Models -->
                                            <div class="mb-3">
                                                <label class="form-label">Specific Vehicle Models</label>
                                                <input type="text" wire:model="specific_vehicle_models"
                                                    class="form-control @error('specific_vehicle_models') is-invalid @enderror">
                                                @error('specific_vehicle_models')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Additional Amenities -->
                                            <div class="mb-3">
                                                <label class="form-label">Additional Amenities</label>
                                                <input type="text" wire:model="additional_amenities"
                                                    class="form-control @error('additional_amenities') is-invalid @enderror">
                                                @error('additional_amenities')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Additional Requests -->
                                            <div class="mb-3">
                                                <label class="form-label">Additional Requests</label>
                                                <input type="text" wire:model="additional_requests"
                                                    class="form-control @error('additional_requests') is-invalid @enderror">
                                                @error('additional_requests')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Price -->
                                            <div class="mb-3">
                                                <label class="form-label">Price</label>
                                                <input type="number" step="0.01" wire:model="price"
                                                    class="form-control @error('price') is-invalid @enderror">
                                                @error('price')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Admin Commission -->
                                            <div class="mb-3">
                                                <label class="form-label">Admin Commission</label>
                                                <input type="number" step="0.01" wire:model="admin_commission"
                                                    class="form-control @error('admin_commission') is-invalid @enderror">
                                                @error('admin_commission')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- GST -->
                                            <div class="mb-3">
                                                <label class="form-label">GST</label>
                                                <input type="number" step="0.01" wire:model="gst"
                                                    class="form-control @error('gst') is-invalid @enderror">
                                                @error('gst')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Service Tax -->
                                            <div class="mb-3">
                                                <label class="form-label">Service Tax</label>
                                                <input type="number" step="0.01" wire:model="service_tax"
                                                    class="form-control @error('service_tax') is-invalid @enderror">
                                                @error('service_tax')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Status Dropdown -->
                                            <div class="mb-3">
                                                <label class="form-label">Status</label>
                                                <select wire:model="status"
                                                    class="form-select @error('status') is-invalid @enderror">
                                                    <option value="pending">Pending</option>
                                                    <option value="approved">Approved</option>
                                                    <option value="canceled">Canceled</option>
                                                    <option value="rejected">Rejected</option>
                                                    <option value="service_stopped">Service Stopped</option>
                                                    <option value="completed">Completed</option>
                                                </select>
                                                @error('status')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Payment Status Dropdown -->
                                            <div class="mb-3">
                                                <label class="form-label">Payment Status</label>
                                                <select wire:model="payment_status"
                                                    class="form-select @error('payment_status') is-invalid @enderror">
                                                    <option value="pending">Pending</option>
                                                    <option value="completed">Completed</option>
                                                </select>
                                                @error('payment_status')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>


                                        </div>



                                    </div>
                                </div>



                                <button wire:click.prevent="updateBooking" class="btn btn-primary mt-5"
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
                        <h5 class="modal-title" id="selectedRecordModalTitle">Chauffeur Booking Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <table class="table table-borderless">
                                    <tbody>

                                        <!-- Chauffeur Booking Details Table -->
                                        <table class="table table-striped">
                                            <!-- Chauffeur Name -->
                                            <tr>
                                                <td style="width: 50%; text-wrap: wrap;" class="align-middle">
                                                    <small class="text-light fw-semibold">Chauffeur Name</small>
                                                </td>
                                                <td style="width: 50%; text-wrap: wrap;" class="py-3">
                                                    <h6 class="mb-0">
                                                        {{ $selectedRecord->chauffeur->driver->full_name ?? 'N/A' }}
                                                    </h6>
                                                </td>
                                            </tr>

                                            <!-- Chauffeur Phone Number -->
                                            <tr>
                                                <td style="width: 50%; text-wrap: wrap;" class="align-middle">
                                                    <small class="text-light fw-semibold">Chauffeur Phone
                                                        Number</small>
                                                </td>
                                                <td style="width: 50%; text-wrap: wrap;" class="py-3">
                                                    <h6 class="mb-0">
                                                        {{ $selectedRecord->chauffeur->driver->country_code ?? '' }}
                                                        {{ $selectedRecord->chauffeur->driver->phone_number ?? 'N/A' }}
                                                    </h6>
                                                </td>
                                            </tr>

                                            <!-- Pickup -->
                                            <tr>
                                                <td class="align-middle">
                                                    <small class="text-light fw-semibold">Pickup Location</small>
                                                </td>
                                                <td class="py-3">
                                                    <h6 class="mb-0">{{ $selectedRecord->pickup ?? 'N/A' }}</h6>
                                                </td>
                                            </tr>

                                            <!-- Dropoff -->
                                            <tr>
                                                <td class="align-middle">
                                                    <small class="text-light fw-semibold">Dropoff Location</small>
                                                </td>
                                                <td class="py-3">
                                                    <h6 class="mb-0">{{ $selectedRecord->dropoff ?? 'N/A' }}</h6>
                                                </td>
                                            </tr>

                                            <!-- Pickup Location Type -->
                                            <tr>
                                                <td class="align-middle">
                                                    <small class="text-light fw-semibold">Pickup Location Type</small>
                                                </td>
                                                <td class="py-3">
                                                    <h6 class="mb-0">
                                                        {{ $selectedRecord->pickup_location_type ?? 'N/A' }}</h6>
                                                </td>
                                            </tr>

                                            <!-- Destination Location Type -->
                                            <tr>
                                                <td class="align-middle">
                                                    <small class="text-light fw-semibold">Destination Location
                                                        Type</small>
                                                </td>
                                                <td class="py-3">
                                                    <h6 class="mb-0">
                                                        {{ $selectedRecord->destination_location_type ?? 'N/A' }}</h6>
                                                </td>
                                            </tr>

                                            <!-- Date -->
                                            <tr>
                                                <td class="align-middle">
                                                    <small class="text-light fw-semibold">Date</small>
                                                </td>
                                                <td class="py-3">
                                                    <h6 class="mb-0">{{ $selectedRecord->date ?? 'N/A' }}</h6>
                                                </td>
                                            </tr>

                                            <!-- Start Time -->
                                            <tr>
                                                <td class="align-middle">
                                                    <small class="text-light fw-semibold">Start Time</small>
                                                </td>
                                                <td class="py-3">
                                                    <h6 class="mb-0">{{ $selectedRecord->start_time ?? 'N/A' }}</h6>
                                                </td>
                                            </tr>

                                            <!-- End Time -->
                                            <tr>
                                                <td class="align-middle">
                                                    <small class="text-light fw-semibold">End Time</small>
                                                </td>
                                                <td class="py-3">
                                                    <h6 class="mb-0">{{ $selectedRecord->end_time ?? 'N/A' }}</h6>
                                                </td>
                                            </tr>

                                            <!-- Vehicle Type -->
                                            <tr>
                                                <td class="align-middle">
                                                    <small class="text-light fw-semibold">Vehicle Type</small>
                                                </td>
                                                <td class="py-3">
                                                    <h6 class="mb-0">{{ $selectedRecord->vehicle_type ?? 'N/A' }}
                                                    </h6>
                                                </td>
                                            </tr>

                                            <!-- Preferred Vehicle -->
                                            <tr>
                                                <td class="align-middle">
                                                    <small class="text-light fw-semibold">Preferred Vehicle</small>
                                                </td>
                                                <td class="py-3">
                                                    <h6 class="mb-0">
                                                        {{ $selectedRecord->preferred_vehicle ?? 'N/A' }}</h6>
                                                </td>
                                            </tr>

                                            <!-- Chauffeur Type -->
                                            <tr>
                                                <td class="align-middle">
                                                    <small class="text-light fw-semibold">Chauffeur Type</small>
                                                </td>
                                                <td class="py-3">
                                                    <h6 class="mb-0">
                                                        {{ $selectedRecord->chauffeur_type == 'with_vehicle' ? 'With Vehicle' : 'Without Vehicle' }}
                                                    </h6>
                                                </td>
                                            </tr>

                                            <!-- Hire Type -->
                                            <tr>
                                                <td class="align-middle">
                                                    <small class="text-light fw-semibold">Hire Type</small>
                                                </td>
                                                <td class="py-3">
                                                    <h6 class="mb-0">{{ $selectedRecord->hire_type ?? 'N/A' }}</h6>
                                                </td>
                                            </tr>

                                            <!-- Event Type -->
                                            <tr>
                                                <td class="align-middle">
                                                    <small class="text-light fw-semibold">Event Type</small>
                                                </td>
                                                <td class="py-3">
                                                    <h6 class="mb-0">{{ $selectedRecord->event_type ?? 'N/A' }}</h6>
                                                </td>
                                            </tr>

                                            <!-- Child Seats -->
                                            <tr>
                                                <td class="align-middle">
                                                    <small class="text-light fw-semibold">Child Seats</small>
                                                </td>
                                                <td class="py-3">
                                                    <h6 class="mb-0">{{ $selectedRecord->child_seats ?? 'N/A' }}
                                                    </h6>
                                                </td>
                                            </tr>

                                            <!-- Specific Vehicle Models -->
                                            <tr>
                                                <td class="align-middle">
                                                    <small class="text-light fw-semibold">Specific Vehicle
                                                        Models</small>
                                                </td>
                                                <td class="py-3">
                                                    <h6 class="mb-0">
                                                        {{ $selectedRecord->specific_vehicle_models ?? 'N/A' }}</h6>
                                                </td>
                                            </tr>

                                            <!-- Additional Amenities -->
                                            <tr>
                                                <td class="align-middle">
                                                    <small class="text-light fw-semibold">Additional Amenities</small>
                                                </td>
                                                <td class="py-3">
                                                    <h6 class="mb-0">
                                                        {{ $selectedRecord->additional_amenities ?? 'N/A' }}</h6>
                                                </td>
                                            </tr>

                                            <!-- Additional Requests -->
                                            <tr>
                                                <td class="align-middle">
                                                    <small class="text-light fw-semibold">Additional Requests</small>
                                                </td>
                                                <td class="py-3">
                                                    <h6 class="mb-0">
                                                        {{ $selectedRecord->additional_requests ?? 'N/A' }}</h6>
                                                </td>
                                            </tr>

                                            <!-- Price -->
                                            <tr>
                                                <td class="align-middle">
                                                    <small class="text-light fw-semibold">Price</small>
                                                </td>
                                                <td class="py-3">
                                                    <h6 class="mb-0">{{ $selectedRecord->price ?? 'N/A' }}</h6>
                                                </td>
                                            </tr>

                                            <!-- Admin Commission -->
                                            <tr>
                                                <td class="align-middle">
                                                    <small class="text-light fw-semibold">Admin Commission</small>
                                                </td>
                                                <td class="py-3">
                                                    <h6 class="mb-0">
                                                        {{ $selectedRecord->admin_commission ?? 'N/A' }}</h6>
                                                </td>
                                            </tr>

                                            <!-- GST -->
                                            <tr>
                                                <td class="align-middle">
                                                    <small class="text-light fw-semibold">GST</small>
                                                </td>
                                                <td class="py-3">
                                                    <h6 class="mb-0">{{ $selectedRecord->gst ?? 'N/A' }}</h6>
                                                </td>
                                            </tr>

                                            <!-- Service Tax -->
                                            <tr>
                                                <td class="align-middle">
                                                    <small class="text-light fw-semibold">Service Tax</small>
                                                </td>
                                                <td class="py-3">
                                                    <h6 class="mb-0">{{ $selectedRecord->service_tax ?? 'N/A' }}
                                                    </h6>
                                                </td>
                                            </tr>

                                            <!-- Status -->
                                            <tr>
                                                <td class="align-middle">
                                                    <small class="text-light fw-semibold">Status</small>
                                                </td>
                                                <td class="py-3">
                                                    <h6 class="mb-0">{{ ucfirst($selectedRecord->status) ?? 'N/A' }}
                                                    </h6>
                                                </td>
                                            </tr>

                                            <!-- Payment Status -->
                                            <tr>
                                                <td class="align-middle">
                                                    <small class="text-light fw-semibold">Payment Status</small>
                                                </td>
                                                <td class="py-3">
                                                    <h6 class="mb-0">
                                                        {{ ucfirst($selectedRecord->payment_status) ?? 'N/A' }}</h6>
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
            $wire.on('closeEditModal', () => {
                $('#editModal').modal('hide');
            });
            $wire.on('openEditModal', () => {
                $('#editModal').modal('show');
            });
        </script>
    @endscript



    @include('livewire.notification')
</div>
