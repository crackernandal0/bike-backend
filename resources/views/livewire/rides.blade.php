@section('rides_active', 'active')
<div>
    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->

        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Admin /</span> Rides</h4>

            <!-- Basic Bootstrap Table -->
            <div class="card">
                <h5 class="card-header">
                    <div class="row">
                        <div class="col-lg-2 d-flex align-items-center justify-content-between">
                            <span>Rides</span>
                        </div>
                        <div class="col-lg-6 mt-4 mt-lg-0">
                            <!-- Filters -->
                            <div class="row">
                                <div class="col-6">
                                    <input type="date" wire:model.live.debounce.500ms="dateFrom" class="form-control"
                                        placeholder="Date From">
                                </div>
                                <div class="col-6">
                                    <input type="date" wire:model.live.debounce.500ms="dateTo" class="form-control"
                                        placeholder="Date To">
                                </div>
                            </div>

                        </div>
                        <div class="col-lg-4 mt-4 mt-lg-0">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text" id="basic-addon-search31"><i
                                        class="bx bx-search"></i></span>
                                <input type="text" wire:model.live.debounce.500ms="search" class="form-control"
                                    placeholder="Search..." aria-label="Search..."
                                    aria-describedby="basic-addon-search31">
                            </div>
                        </div>
                        <div class="row mt-3 mx-auto">
                            <div class="col-lg-4 col mt-3 mt-lg-0">
                                <select wire:model.live.debounce.500ms="ride_status" class="form-control">
                                    <option value="">Ride Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="completed">Completed</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                            <div class="col-lg-4 col mt-3 mt-lg-0">
                                <select wire:model.live.debounce.500ms="payment_status" class="form-control">
                                    <option value="">Payment Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="completed">Completed</option>
                                    <option value="canceled">Canceled</option>
                                    <option value="failed">Failed</option>
                                </select>
                            </div>
                            <div class="col-lg-4 col-12 mt-3 mt-lg-0">
                                <select wire:model.live.debounce.500ms="no_of_records" class="form-control">
                                    <option value="">Number Of Records</option>
                                    <option value="10">10</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="all">All</option>
                                </select>
                            </div>
                        </div>

                    </div>

                </h5>

                <div class="table-responsive text-wrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="font-size: 11px;white-space:nowrap">SNO.</th>
                                <th style="font-size: 11px;white-space:nowrap">Ride No.</th>
                                <th style="font-size: 11px;white-space:nowrap">User Name</th>
                                <th style="font-size: 11px;white-space:nowrap">Phone Number</th>
                                <th style="font-size: 11px;white-space:nowrap">Driver</th>
                                <th style="font-size: 11px;white-space:nowrap">Ride Status</th>
                                <th style="font-size: 11px;white-space:nowrap">Payment Status</th>
                                <th style="font-size: 11px;white-space:nowrap">View</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @if (count($rides) > 0)
                                @foreach ($rides as $index => $ride)
                                    <tr class="cursor-pointer">
                                        <td>
                                            {{ ($rides->currentPage() - 1) * $rides->perPage() + $index + 1 }}</td>
                                        <td style="white-space:nowrap">{{ $ride->ride_number }}</td>
                                        <td style="white-space:nowrap">{{ $ride->user->name ?? 'N/A' }}</td>

                                        <td style="white-space:nowrap"><a style="word-break: break-all;"
                                                class="text-decoration-underline"
                                                href="tel:{{ $ride->user->country_code ?? 'N/A' }}{{ $ride->user->phone_number ?? 'N/A' }}">{{ $ride->user->country_code ?? 'N/A' }}{{ $ride->user->phone_number ?? 'N/A' }}</a>
                                        </td>

                                        <td style="white-space:nowrap">{{ $ride->driver->full_name ?? 'N/A' }}</td>


                                        <td>
                                            @if ($ride->ride_status == 'completed')
                                                <button
                                                    wire:confirm="Are you sure you want to change the status of this service location?"
                                                    wire:click.prevent="changeStatus({{ $ride->id }},0)"
                                                    data-bs-toggle="tooltip" data-bs-offset="0,4"
                                                    data-bs-placement="top" data-bs-html="true" title=""
                                                    data-bs-original-title="<i class='bx bx-edit-alt bx-xs' ></i> <span>Tap To Change Status</span>"
                                                    class="btn-sm btn-primary">
                                                    Completed
                                                </button>
                                            @elseif($ride->ride_status == 'pending')
                                                <button
                                                    wire:confirm="Are you sure you want to change the status of this service location?"
                                                    wire:click.prevent="changeStatus({{ $ride->id }},0)"
                                                    data-bs-toggle="tooltip" data-bs-offset="0,4"
                                                    data-bs-placement="top" data-bs-html="true" title=""
                                                    data-bs-original-title="<i class='bx bx-edit-alt bx-xs' ></i> <span>Tap To Change Status</span>"
                                                    class="btn-sm btn-warning">
                                                    Pending
                                                </button>
                                            @elseif($ride->ride_status == 'accepted')
                                                <button
                                                    wire:confirm="Are you sure you want to change the status of this service location?"
                                                    wire:click.prevent="changeStatus({{ $ride->id }},0)"
                                                    data-bs-toggle="tooltip" data-bs-offset="0,4"
                                                    data-bs-placement="top" data-bs-html="true" title=""
                                                    data-bs-original-title="<i class='bx bx-edit-alt bx-xs' ></i> <span>Tap To Change Status</span>"
                                                    class="btn-sm btn-success">
                                                    Accepted
                                                </button>
                                            @elseif($ride->ride_status == 'driver_arrived')
                                                <button
                                                    wire:confirm="Are you sure you want to change the status of this service location?"
                                                    wire:click.prevent="changeStatus({{ $ride->id }},0)"
                                                    data-bs-toggle="tooltip" data-bs-offset="0,4"
                                                    data-bs-placement="top" data-bs-html="true" title=""
                                                    data-bs-original-title="<i class='bx bx-edit-alt bx-xs' ></i> <span>Tap To Change Status</span>"
                                                    class="btn-sm btn-success">
                                                    Driver Arrived
                                                </button>
                                            @elseif($ride->ride_status == 'in_progress')
                                                <button
                                                    wire:confirm="Are you sure you want to change the status of this service location?"
                                                    wire:click.prevent="changeStatus({{ $ride->id }},0)"
                                                    data-bs-toggle="tooltip" data-bs-offset="0,4"
                                                    data-bs-placement="top" data-bs-html="true" title=""
                                                    data-bs-original-title="<i class='bx bx-edit-alt bx-xs' ></i> <span>Tap To Change Status</span>"
                                                    class="btn-sm btn-success">
                                                    Ride Started
                                                </button>
                                            @elseif($ride->ride_status == 'canceled')
                                                <button
                                                    wire:confirm="Are you sure you want to change the status of this service location?"
                                                    wire:click.prevent="changeStatus({{ $ride->id }},0)"
                                                    data-bs-toggle="tooltip" data-bs-offset="0,4"
                                                    data-bs-placement="top" data-bs-html="true" title=""
                                                    data-bs-original-title="<i class='bx bx-edit-alt bx-xs' ></i> <span>Tap To Change Status</span>"
                                                    class="btn-sm btn-danger">
                                                    Canceled
                                                </button>
                                            @endif
                                        </td>

                                        <td>
                                            @if ($ride->payment_status == 'completed')
                                                <button
                                                    wire:confirm="Are you sure you want to change the status of this service location?"
                                                    wire:click.prevent="changePaymentStatus({{ $ride->id }},0)"
                                                    data-bs-toggle="tooltip" data-bs-offset="0,4"
                                                    data-bs-placement="top" data-bs-html="true" title=""
                                                    data-bs-original-title="<i class='bx bx-edit-alt bx-xs' ></i> <span>Tap To Change Status</span>"
                                                    class="btn-sm btn-primary">
                                                    Completed
                                                </button>
                                            @elseif($ride->payment_status == 'pending')
                                                <button
                                                    wire:confirm="Are you sure you want to change the status of this service location?"
                                                    wire:click.prevent="changePaymentStatus({{ $ride->id }},0)"
                                                    data-bs-toggle="tooltip" data-bs-offset="0,4"
                                                    data-bs-placement="top" data-bs-html="true" title=""
                                                    data-bs-original-title="<i class='bx bx-edit-alt bx-xs' ></i> <span>Tap To Change Status</span>"
                                                    class="btn-sm btn-warning">
                                                    Pending
                                                </button>
                                            @elseif($ride->payment_status == 'failed')
                                                <button
                                                    wire:confirm="Are you sure you want to change the status of this service location?"
                                                    wire:click.prevent="changePaymentStatus({{ $ride->id }},0)"
                                                    data-bs-toggle="tooltip" data-bs-offset="0,4"
                                                    data-bs-placement="top" data-bs-html="true" title=""
                                                    data-bs-original-title="<i class='bx bx-edit-alt bx-xs' ></i> <span>Tap To Change Status</span>"
                                                    class="btn-sm btn-danger">
                                                    Failed
                                                </button>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <button style="background: none; outline:none;border:none;"
                                                    wire:click="showRecord({{ $ride->id }})">

                                                    <i style="font-size:15px; font-weight:600;"
                                                        class="bx bx-show text-primary"></i>
                                                </button>
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
                            {{ $rides->links('custom-pagination') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- / Content -->


        <div class="content-backdrop fade"></div>
    </div>
    <!-- Content wrapper -->


    <div wire:ignore.self class="modal fade" id="selectedRecordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            @if ($selectedRecord)
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="selectedRecordModalTitle">Ride Details Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <table class="table table-borderless">
                                    <tbody>

                                        <tr>
                                            <td style="width: 50%;text-wrap:wrap;" class="align-middle"><small
                                                    class="text-light fw-semibold">Ride Number</small></td>
                                            <td style="width: 50%;text-wrap:wrap;" class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->ride_number ?? 'N/A' }}</h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="width: 50%;text-wrap:wrap;" class="align-middle"><small
                                                    class="text-light fw-semibold">Ride Status</small></td>
                                            <td style="width: 50%;text-wrap:wrap;" class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->ride_status ?? 'N/A' }}</h6>
                                            </td>
                                        </tr>

                                        <!-- User Information -->
                                        <tr>
                                            <td style="width: 50%;text-wrap:wrap;" class="align-middle"><small
                                                    class="text-light fw-semibold">User Name</small></td>
                                            <td style="width: 50%;text-wrap:wrap;" class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->user->name ?? 'N/A' }}</h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="width: 50%;text-wrap:wrap;" class="align-middle"><small
                                                    class="text-light fw-semibold">User Phone</small></td>
                                            <td style="width: 50%;text-wrap:wrap;" class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->user->phone ?? 'N/A' }}</h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="width: 50%;text-wrap:wrap;" class="align-middle"><small
                                                    class="text-light fw-semibold">User Email</small></td>
                                            <td style="width: 50%;text-wrap:wrap;" class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->user->email ?? 'N/A' }}</h6>
                                            </td>
                                        </tr>

                                        <!-- Service Location -->
                                        <tr>
                                            <td style="width: 50%;text-wrap:wrap;" class="align-middle"><small
                                                    class="text-light fw-semibold">Service Location</small></td>
                                            <td style="width: 50%;text-wrap:wrap;" class="py-3">
                                                <h6 class="mb-0">
                                                    {{ $selectedRecord->serviceLocation->name ?? 'N/A' }}</h6>
                                            </td>
                                        </tr>

                                        <!-- Zone Information -->
                                        <tr>
                                            <td style="width: 50%;text-wrap:wrap;" class="align-middle"><small
                                                    class="text-light fw-semibold">Zone Name</small></td>
                                            <td style="width: 50%;text-wrap:wrap;" class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->zone->name ?? 'N/A' }}</h6>
                                            </td>
                                        </tr>

                                        <!-- Vehicle Type and Subcategory -->
                                        <tr>
                                            <td style="width: 50%;text-wrap:wrap;" class="align-middle"><small
                                                    class="text-light fw-semibold">Vehicle Type</small></td>
                                            <td style="width: 50%;text-wrap:wrap;" class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->vehicleType->name ?? 'N/A' }}
                                                </h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="width: 50%;text-wrap:wrap;" class="align-middle"><small
                                                    class="text-light fw-semibold">Vehicle Subcategory</small></td>
                                            <td style="width: 50%;text-wrap:wrap;" class="py-3">
                                                <h6 class="mb-0">
                                                    {{ $selectedRecord->vehicleSubcategory->name ?? 'N/A' }}</h6>
                                            </td>
                                        </tr>

                                        <!-- Driver Information -->
                                        <tr>
                                            <td style="width: 50%;text-wrap:wrap;" class="align-middle"><small
                                                    class="text-light fw-semibold">Driver Name</small></td>
                                            <td style="width: 50%;text-wrap:wrap;" class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->driver->fullname ?? 'N/A' }}
                                                </h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="width: 50%;text-wrap:wrap;" class="align-middle"><small
                                                    class="text-light fw-semibold">Driver Phone</small></td>
                                            <td style="width: 50%;text-wrap:wrap;" class="py-3">
                                                <h6 class="mb-0">
                                                    {{ $selectedRecord->driver->phone_number ?? 'N/A' }}</h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="width: 50%;text-wrap:wrap;" class="align-middle"><small
                                                    class="text-light fw-semibold">Driver Address</small></td>
                                            <td style="width: 50%;text-wrap:wrap;" class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->driver->address ?? 'N/A' }}
                                                </h6>
                                            </td>
                                        </tr>

                                        <!-- Pickup and Dropoff (JSON fields) -->
                                        <tr>
                                            <td style="width: 50%;text-wrap:wrap;" class="align-middle"><small
                                                    class="text-light fw-semibold">Pickup Location</small></td>
                                            <td style="width: 50%;text-wrap:wrap;" class="py-3">
                                                <h6 class="mb-0">
                                                    {{ $selectedRecord->pickup['address'] ?? 'N/A' }}<br>
                                                    Lat: {{ $selectedRecord->pickup['lat'] ?? 'N/A' }}<br>
                                                    Long: {{ $selectedRecord->pickup['long'] ?? 'N/A' }}
                                                </h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="width: 50%;text-wrap:wrap;" class="align-middle"><small
                                                    class="text-light fw-semibold">Dropoff Location</small></td>
                                            <td style="width: 50%;text-wrap:wrap;" class="py-3">
                                                <h6 class="mb-0">
                                                    {{ $selectedRecord->dropoff['address'] ?? 'N/A' }}<br>
                                                    Lat: {{ $selectedRecord->dropoff['lat'] ?? 'N/A' }}<br>
                                                    Long: {{ $selectedRecord->dropoff['long'] ?? 'N/A' }}
                                                </h6>
                                            </td>
                                        </tr>

                                        <!-- Ride Stops (JSON format) -->
                                        @foreach ($selectedRecord->rideStops as $stop)
                                            <tr>
                                                <td style="width: 50%;text-wrap:wrap;" class="align-middle"><small
                                                        class="text-light fw-semibold">Stop Location</small></td>
                                                <td style="width: 50%;text-wrap:wrap;" class="py-3">
                                                    <h6 class="mb-0">
                                                        {{ $stop->stop['address'] ?? 'N/A' }}<br>
                                                        Lat: {{ $stop->stop['lat'] ?? 'N/A' }}<br>
                                                        Long: {{ $stop->stop['long'] ?? 'N/A' }}
                                                    </h6>
                                                </td>
                                            </tr>
                                        @endforeach

                                        <!-- Ride Feedback -->
                                        <tr>
                                            <td style="width: 50%;text-wrap:wrap;" class="align-middle"><small
                                                    class="text-light fw-semibold">Ride Rating</small></td>
                                            <td style="width: 50%;text-wrap:wrap;" class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->feedback->rating ?? 'N/A' }}
                                                </h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="width: 50%;text-wrap:wrap;" class="align-middle"><small
                                                    class="text-light fw-semibold">Feedback</small></td>
                                            <td style="width: 50%;text-wrap:wrap;" class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->feedback->feedback ?? 'N/A' }}
                                                </h6>
                                            </td>
                                        </tr>

                                        <!-- Other fields -->
                                        <tr>
                                            <td style="width: 50%;text-wrap:wrap;" class="align-middle"><small
                                                    class="text-light fw-semibold">Scheduled Date</small></td>
                                            <td style="width: 50%;text-wrap:wrap;" class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->scheduled_date ?? 'N/A' }}</h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="width: 50%;text-wrap:wrap;" class="align-middle"><small
                                                    class="text-light fw-semibold">Scheduled Time</small></td>
                                            <td style="width: 50%;text-wrap:wrap;" class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->scheduled_time ?? 'N/A' }}</h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="width: 50%;text-wrap:wrap;" class="align-middle"><small
                                                    class="text-light fw-semibold">Ride Type</small></td>
                                            <td style="width: 50%;text-wrap:wrap;" class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->ride_type ?? 'N/A' }}</h6>
                                            </td>
                                        </tr>

                                    
                                        <tr>
                                            <td class="align-middle"><small class="text-light fw-semibold">Ride
                                                    Status</small></td>
                                            <td class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->ride_status ?? 'N/A' }}</h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="align-middle"><small class="text-light fw-semibold">Ride
                                                    OTP</small></td>
                                            <td class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->ride_otp ?? 'N/A' }}</h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="align-middle"><small class="text-light fw-semibold">Instant
                                                    Ride</small></td>
                                            <td class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->instant_ride ? 'Yes' : 'No' }}
                                                </h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="align-middle"><small class="text-light fw-semibold">Ride
                                                    Later</small></td>
                                            <td class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->ride_later ? 'Yes' : 'No' }}
                                                </h6>
                                            </td>
                                        </tr>

                                   
                                        <!-- Service Location -->
                                        <tr>
                                            <td class="align-middle"><small class="text-light fw-semibold">Service
                                                    Location</small></td>
                                            <td class="py-3">
                                                <h6 class="mb-0">
                                                    {{ $selectedRecord->serviceLocation->name ?? 'N/A' }}</h6>
                                            </td>
                                        </tr>

                                        <!-- Vehicle Type -->
                                        <tr>
                                            <td class="align-middle"><small class="text-light fw-semibold">Vehicle
                                                    Type</small></td>
                                            <td class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->vehicleType->name ?? 'N/A' }}
                                                </h6>
                                            </td>
                                        </tr>

                                        <!-- Vehicle Subcategory -->
                                        <tr>
                                            <td class="align-middle"><small class="text-light fw-semibold">Vehicle
                                                    Subcategory</small></td>
                                            <td class="py-3">
                                                <h6 class="mb-0">
                                                    {{ $selectedRecord->vehicleSubcategory->name ?? 'N/A' }}</h6>
                                            </td>
                                        </tr>

                                
                                        <!-- Scheduled Ride -->
                                        <tr>
                                            <td class="align-middle"><small class="text-light fw-semibold">Is Schedule
                                                    Ride</small></td>
                                            <td class="py-3">
                                                <h6 class="mb-0">
                                                    {{ $selectedRecord->is_schedule_ride ? 'Yes' : 'No' }}</h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="align-middle"><small class="text-light fw-semibold">Scheduled
                                                    Date</small></td>
                                            <td class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->scheduled_date ?? 'N/A' }}</h6>
                                            </td>
                                        </tr>

                                 
                                     

                                        <tr>
                                            <td class="align-middle"><small class="text-light fw-semibold">Return
                                                    Trip</small></td>
                                            <td class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->return_trip ? 'Yes' : 'No' }}
                                                </h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="align-middle"><small class="text-light fw-semibold">Return
                                                    Date</small></td>
                                            <td class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->return_date ?? 'N/A' }}</h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="align-middle"><small class="text-light fw-semibold">Return
                                                    Time</small></td>
                                            <td class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->return_time ?? 'N/A' }}</h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="align-middle"><small class="text-light fw-semibold">Passenger
                                                    Count</small></td>
                                            <td class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->passenger_count ?? 'N/A' }}
                                                </h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="align-middle"><small class="text-light fw-semibold">Is For
                                                    Someone Else</small></td>
                                            <td class="py-3">
                                                <h6 class="mb-0">
                                                    {{ $selectedRecord->is_for_someone_else ? 'Yes' : 'No' }}</h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="align-middle"><small class="text-light fw-semibold">Rider
                                                    Name</small></td>
                                            <td class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->rider_name ?? 'N/A' }}</h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="align-middle"><small class="text-light fw-semibold">Rider Phone
                                                    Number</small></td>
                                            <td class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->rider_phone_number ?? 'N/A' }}
                                                </h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="align-middle"><small class="text-light fw-semibold">Additional
                                                    Notes</small></td>
                                            <td class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->additional_notes ?? 'N/A' }}
                                                </h6>
                                            </td>
                                        </tr>

                                        <!-- Ride Times -->
                                        <tr>
                                            <td class="align-middle"><small class="text-light fw-semibold">Ride
                                                    Accepted At</small></td>
                                            <td class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->ride_accepted_at ?? 'N/A' }}
                                                </h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="align-middle"><small class="text-light fw-semibold">Driver
                                                    Arrived At</small></td>
                                            <td class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->driver_arrived_at ?? 'N/A' }}
                                                </h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="align-middle"><small class="text-light fw-semibold">Ride
                                                    Started At</small></td>
                                            <td class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->ride_started_at ?? 'N/A' }}
                                                </h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="align-middle"><small class="text-light fw-semibold">Ride
                                                    Completed At</small></td>
                                            <td class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->ride_completed_at ?? 'N/A' }}
                                                </h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="align-middle"><small class="text-light fw-semibold">Ride
                                                    Cancelled At</small></td>
                                            <td class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->ride_cancelled_at ?? 'N/A' }}
                                                </h6>
                                            </td>
                                        </tr>

                                        <!-- Cancellation -->
                                        <tr>
                                            <td class="align-middle"><small class="text-light fw-semibold">Cancel
                                                    Type</small></td>
                                            <td class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->cancel_type ?? 'N/A' }}</h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="align-middle"><small class="text-light fw-semibold">Cancel
                                                    Reason</small></td>
                                            <td class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->cancel_reason ?? 'N/A' }}</h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="align-middle"><small class="text-light fw-semibold">Canceled
                                                    By</small></td>
                                            <td class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->canceled_by ?? 'N/A' }}</h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="align-middle"><small
                                                    class="text-light fw-semibold">Cancellation Fee</small></td>
                                            <td class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->cancellation_fee ?? 'N/A' }}
                                                </h6>
                                            </td>
                                        </tr>

                                        <!-- Payment -->
                                        <tr>
                                            <td class="align-middle"><small class="text-light fw-semibold">Payment
                                                    Type</small></td>
                                            <td class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->payment_type ?? 'N/A' }}</h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="align-middle"><small class="text-light fw-semibold">Payment
                                                    Amount</small></td>
                                            <td class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->payment_amount ?? 'N/A' }}</h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="align-middle"><small class="text-light fw-semibold">Final
                                                    Fare</small></td>
                                            <td class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->final_fare ?? 'N/A' }}</h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="align-middle"><small class="text-light fw-semibold">User Coins
                                                    Discount</small></td>
                                            <td class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->user_coins_discount ?? 'N/A' }}
                                                </h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="align-middle"><small class="text-light fw-semibold">Payment
                                                    Status</small></td>
                                            <td class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->payment_status ?? 'N/A' }}</h6>
                                            </td>
                                        </tr>

                                        <!-- Ride Booking -->
                                        <tr>
                                            <td class="align-middle"><small class="text-light fw-semibold">Ride Booked
                                                    At</small></td>
                                            <td class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->ride_booked_at ?? 'N/A' }}</h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="align-middle"><small class="text-light fw-semibold">Total
                                                    Distance</small></td>
                                            <td class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->total_distance ?? 'N/A' }}</h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="align-middle"><small class="text-light fw-semibold">Estimated
                                                    Time</small></td>
                                            <td class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->estimated_time ?? 'N/A' }}</h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="align-middle"><small class="text-light fw-semibold">Waiting
                                                    Minutes</small></td>
                                            <td class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->waiting_minutes ?? 'N/A' }}
                                                </h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="align-middle"><small class="text-light fw-semibold">Waiting
                                                    Charges</small></td>
                                            <td class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->waiting_charges ?? 'N/A' }}
                                                </h6>
                                            </td>
                                        </tr>

                                        <!-- Promo -->
                                        <tr>
                                            <td class="align-middle"><small class="text-light fw-semibold">Promo
                                                    ID</small></td>
                                            <td class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->promo->code ?? 'N/A' }}</h6>
                                            </td>
                                        </tr>

                                        <!-- Pickup and Dropoff -->
                                   
                                     


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
        </script>
    @endscript

    @include('livewire.notification')
</div>
