@section('zones_active', 'open')
@section('zone_vehicles_active', 'active')

<div>
    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->

        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Admin /</span> Zone Vehicles Management
                </h4>

                <div class="d-flex align-items-center">
                    <button data-bs-toggle="modal" data-bs-target="#addVehiclePrice" class="btn btn-primary">
                        <span class="tf-icons bx bx-plus"></span>&nbsp; Add Zone Vehicle
                    </button>
                </div>
            </div>


            <div class="card">
                <div class="row mt-4 pe-4">
                    <div class="col-lg-6 d-flex align-items-center justify-content-between">

                    </div>

                    <div class="col-lg-3">

                    </div>
                    <div class="col-lg-3 mt-4 mt-lg-0">
                        <div class="input-group input-group-merge">
                            <span class="input-group-text" id="basic-addon-search31"><i class="bx bx-search"></i></span>
                            <input type="text" wire:model.live.debounce.500ms="search" class="form-control"
                                placeholder="Search..." aria-label="Search..." aria-describedby="basic-addon-search31">
                        </div>
                    </div>
                </div>
                <div class="table-responsive text-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Zone</th>
                                <th>Vehicle Type</th>
                                <th>Vehicle Subcategory</th>
                                <th>View</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @if (count($zoneTypePrices) > 0)
                                @foreach ($zoneTypePrices as $index => $zoneTypePrice)
                                    <tr>
                                        <td>{{ ($zoneTypePrices->currentPage() - 1) * $zoneTypePrices->perPage() + $index + 1 }}
                                        </td>
                                        <td>{{ $zoneTypePrice->zone->name ?? 'N/A' }}</td>
                                        <td>{{ $zoneTypePrice->vehicleType->name ?? 'N/A' }}</td>
                                        <td>{{ $zoneTypePrice->vehicleSubcategory->name ?? 'N/A' }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <button style="background: none; outline:none;border:none;"
                                                    wire:click="showRecord({{ $zoneTypePrice->id }})">

                                                    <i style="font-size:15px; font-weight:600;"
                                                        class="bx bx-show text-primary"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($zoneTypePrice->active == true)
                                                <button
                                                    wire:confirm="Are you sure you want to change the status of this record?"
                                                    wire:click.prevent="changeStatus({{ $zoneTypePrice->id }},0)"
                                                    data-bs-toggle="tooltip" data-bs-offset="0,4"
                                                    data-bs-placement="top" data-bs-html="true" title=""
                                                    data-bs-original-title="<i class='bx bx-edit-alt bx-xs' ></i> <span>Tap To Change Status</span>"
                                                    class="btn-sm btn-primary">
                                                    Active
                                                </button>
                                            @elseif($zoneTypePrice->active == false)
                                                <button
                                                    wire:confirm="Are you sure you want to change the status of this record?"
                                                    wire:click.prevent="changeStatus({{ $zoneTypePrice->id }},1)"
                                                    data-bs-toggle="tooltip" data-bs-offset="0,4"
                                                    data-bs-placement="top" data-bs-html="true" title=""
                                                    data-bs-original-title="<i class='bx bx-edit-alt bx-xs' ></i> <span>Tap To Change Status</span>"
                                                    class="btn-sm btn-warning">
                                                    Inactive
                                                </button>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="dropdown dropup">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                    data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu" data-popper-placement="top-start">
                                                    <button class="dropdown-item"
                                                        wire:click="editZoneTypePrice({{ $zoneTypePrice->id }})">
                                                        <i class="bx bx-pencil me-1"></i> Edit
                                                    </button>
                                                    <button wire:confirm="Are you sure you want to delete this record?"
                                                        class="dropdown-item"
                                                        wire:click="deleteZoneTypePrice({{ $zoneTypePrice->id }})">
                                                        <i class="bx bx-trash me-1"></i> Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr class="text-center">
                                    <td colspan="5">No Record Found.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="container mt-4">
                    <div class="row">
                        <div class="col-12 d-flex align-items-center justify-content-end">
                            {{ $zoneTypePrices->links('custom-pagination') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- / Content -->

        <div class="content-backdrop fade"></div>
    </div>
    <!-- Content wrapper -->

    <!-- Add Service Location Modal -->
    <div wire:ignore.self class="modal fade" id="addVehiclePrice" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addVehiclePriceTitle">Add New Service Location</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>


                        <div class="mb-3">
                            <label class="form-label" for="countryDropdown">Zone</label>
                            <select wire:model="zone_id" class="form-control">
                                <option value="">Select a Zone</option>
                                @foreach ($zones as $zone)
                                    <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                @endforeach
                            </select>
                            @error('zone_id')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="countryDropdown">Vehicle Type</label>
                            <select wire:model="vehicle_type_id" class="form-control">
                                <option value="">Select a Type</option>
                                @foreach ($vehicleTypes as $vehicleType)
                                    <option value="{{ $vehicleType->id }}">{{ $vehicleType->name }}</option>
                                @endforeach
                            </select>
                            @error('vehicle_type_id')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <label class="form-label" for="countryDropdown">Vehicle Type</label>
                            <select wire:model="vehicle_subcategory_id" class="form-control">
                                <option value="">Select a Type</option>
                                @foreach ($vehicleSubcategories as $vehicleSubcategory)
                                    <option value="{{ $vehicleSubcategory->id }}">{{ $vehicleSubcategory->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('vehicle_subcategory_id')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <label class="form-label" for="serviceLocationName">Payment Type</label>
                            <div class="input-group input-group-merge">
                                <div class="form-check">
                                    <input type="checkbox" wire:model="payment_types" value="Cash"
                                        id="payment_type_cash"
                                        class="form-check-input @error('payment_types') is-invalid @enderror">
                                    <label class="form-check-label" for="payment_type_cash">Cash</label>
                                </div>
                                <div class="form-check ms-2">
                                    <input type="checkbox" wire:model="payment_types" value="Gateway"
                                        id="payment_type_gateway"
                                        class="form-check-input @error('payment_types') is-invalid @enderror">
                                    <label class="form-check-label" for="payment_type_gateway">Gateway</label>
                                </div>
                                <div class="form-check ms-2">
                                    <input type="checkbox" wire:model="payment_types" value="Wallet"
                                        id="payment_type_wallet"
                                        class="form-check-input @error('payment_types') is-invalid @enderror">
                                    <label class="form-check-label" for="payment_type_wallet">Wallet</label>
                                </div>
                            </div>
                            @error('payment_types')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <label class="form-label" for="serviceLocationName">Base Price</label>
                            <div class="input-group input-group-merge">
                                <input type="text" wire:model="base_price"
                                    class="form-control @error('base_price') is-invalid @enderror"
                                    placeholder="Enter payment type">
                            </div>
                            @error('base_price')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="serviceLocationName">Price per Distance (Per KM)</label>
                            <div class="input-group input-group-merge">
                                <input type="text" wire:model="price_per_distance"
                                    class="form-control @error('price_per_distance') is-invalid @enderror"
                                    placeholder="Enter Price per Distance">
                            </div>
                            @error('price_per_distance')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="serviceLocationName">Waiting Charge Minutes</label>
                            <div class="input-group input-group-merge">
                                <input type="text" wire:model="waiting_charge"
                                    class="form-control @error('waiting_charge') is-invalid @enderror"
                                    placeholder="Enter Price per Distance">
                            </div>
                            @error('waiting_charge')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <label class="form-label" for="serviceLocationName">Waiting Charge Price Per Time</label>
                            <div class="input-group input-group-merge">
                                <input type="text" wire:model="price_per_time"
                                    class="form-control @error('price_per_time') is-invalid @enderror"
                                    placeholder="Enter Price per Distance">
                            </div>
                            @error('price_per_time')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="serviceLocationName">Cancellation Fee</label>
                            <div class="input-group input-group-merge">
                                <input type="text" wire:model="cancellation_fee"
                                    class="form-control @error('cancellation_fee') is-invalid @enderror"
                                    placeholder="Enter Cancellation Fee">
                            </div>
                            @error('cancellation_fee')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="serviceLocationName">Admin Commision (In %)</label>
                            <div class="input-group input-group-merge">
                                <input type="text" wire:model="admin_commision"
                                    class="form-control @error('admin_commision') is-invalid @enderror"
                                    placeholder="Enter Admin Commision">
                            </div>
                            @error('admin_commision')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <label class="form-label" for="serviceLocationName">Service Tax (In %)</label>
                            <div class="input-group input-group-merge">
                                <input type="text" wire:model="service_tax"
                                    class="form-control @error('service_tax') is-invalid @enderror"
                                    placeholder="Enter Service Tax">
                            </div>
                            @error('service_tax')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <label class="form-label" for="serviceLocationName">Gst Tax (In %)</label>
                            <div class="input-group input-group-merge">
                                <input type="text" wire:model="gst_tax"
                                    class="form-control @error('gst_tax') is-invalid @enderror"
                                    placeholder="Enter Gst Tax">
                            </div>
                            @error('gst_tax')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <div class="form-check form-switch mb-3">
                                <input wire:model="active" class="form-check-input" type="checkbox" id="active">
                                <label class="form-check-label" for="active">Active</label>
                            </div>
                            @error('active')
                                <div class="error">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <button wire:click.prevent="submit" class="btn btn-primary mt-3"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove>Submit</span>
                            <div wire:loading>Loading...</div>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Service Location Modal -->
    <div wire:ignore.self class="modal fade" id="editVehiclePrice" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editVehiclePriceTitle">Add New Service Location</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>


                        <div class="mb-3">
                            <label class="form-label" for="countryDropdown">Zone</label>
                            <select wire:model="zone_id" class="form-control">
                                <option value="">Select a Zone</option>
                                @foreach ($zones as $zone)
                                    <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                @endforeach
                            </select>
                            @error('zone_id')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="countryDropdown">Vehicle Type</label>
                            <select wire:model="vehicle_type_id" class="form-control">
                                <option value="">Select a Type</option>
                                @foreach ($vehicleTypes as $vehicleType)
                                    <option value="{{ $vehicleType->id }}">{{ $vehicleType->name }}</option>
                                @endforeach
                            </select>
                            @error('vehicle_type_id')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <label class="form-label" for="countryDropdown">Vehicle Type</label>
                            <select wire:model="vehicle_subcategory_id" class="form-control">
                                <option value="">Select a Type</option>
                                @foreach ($vehicleSubcategories as $vehicleSubcategory)
                                    <option value="{{ $vehicleSubcategory->id }}">{{ $vehicleSubcategory->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('vehicle_subcategory_id')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="serviceLocationName">Payment Type</label>
                            <div class="input-group input-group-merge">
                                <div class="form-check">
                                    <input type="checkbox" wire:model="payment_types" value="Cash"
                                        id="payment_type_cash"
                                        class="form-check-input @error('payment_types') is-invalid @enderror">
                                    <label class="form-check-label" for="payment_type_cash">Cash</label>
                                </div>
                                <div class="form-check ms-2">
                                    <input type="checkbox" wire:model="payment_types" value="Gateway"
                                        id="payment_type_gateway"
                                        class="form-check-input @error('payment_types') is-invalid @enderror">
                                    <label class="form-check-label" for="payment_type_gateway">Gateway</label>
                                </div>
                                <div class="form-check ms-2">
                                    <input type="checkbox" wire:model="payment_types" value="Wallet"
                                        id="payment_type_wallet"
                                        class="form-check-input @error('payment_types') is-invalid @enderror">
                                    <label class="form-check-label" for="payment_type_wallet">Wallet</label>
                                </div>
                            </div>
                            @error('payment_types')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <label class="form-label" for="serviceLocationName">Base Price</label>
                            <div class="input-group input-group-merge">
                                <input type="text" wire:model="base_price"
                                    class="form-control @error('base_price') is-invalid @enderror"
                                    placeholder="Enter payment type">
                            </div>
                            @error('base_price')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="serviceLocationName">Price per Distance (Per KM)</label>
                            <div class="input-group input-group-merge">
                                <input type="text" wire:model="price_per_distance"
                                    class="form-control @error('price_per_distance') is-invalid @enderror"
                                    placeholder="Enter Price per Distance">
                            </div>
                            @error('price_per_distance')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="serviceLocationName">Waiting Charge Minutes</label>
                            <div class="input-group input-group-merge">
                                <input type="text" wire:model="waiting_charge"
                                    class="form-control @error('waiting_charge') is-invalid @enderror"
                                    placeholder="Enter Price per Distance">
                            </div>
                            @error('waiting_charge')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <label class="form-label" for="serviceLocationName">Waiting Charge Price Per Time</label>
                            <div class="input-group input-group-merge">
                                <input type="text" wire:model="price_per_time"
                                    class="form-control @error('price_per_time') is-invalid @enderror"
                                    placeholder="Enter Price per Distance">
                            </div>
                            @error('price_per_time')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="serviceLocationName">Cancellation Fee</label>
                            <div class="input-group input-group-merge">
                                <input type="text" wire:model="cancellation_fee"
                                    class="form-control @error('cancellation_fee') is-invalid @enderror"
                                    placeholder="Enter Cancellation Fee">
                            </div>
                            @error('cancellation_fee')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="serviceLocationName">Admin Commision (In %)</label>
                            <div class="input-group input-group-merge">
                                <input type="text" wire:model="admin_commision"
                                    class="form-control @error('admin_commision') is-invalid @enderror"
                                    placeholder="Enter Admin Commision">
                            </div>
                            @error('admin_commision')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <label class="form-label" for="serviceLocationName">Service Tax (In %)</label>
                            <div class="input-group input-group-merge">
                                <input type="text" wire:model="service_tax"
                                    class="form-control @error('service_tax') is-invalid @enderror"
                                    placeholder="Enter Service Tax">
                            </div>
                            @error('service_tax')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <label class="form-label" for="serviceLocationName">Gst Tax (In %)</label>
                            <div class="input-group input-group-merge">
                                <input type="text" wire:model="gst_tax"
                                    class="form-control @error('gst_tax') is-invalid @enderror"
                                    placeholder="Enter Gst Tax">
                            </div>
                            @error('gst_tax')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <div class="form-check form-switch mb-3">
                                <input wire:model="active" class="form-check-input" type="checkbox" id="active">
                                <label class="form-check-label" for="active">Active</label>
                            </div>
                            @error('active')
                                <div class="error">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <button wire:click.prevent="update" class="btn btn-primary mt-3"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove>Submit</span>
                            <div wire:loading>Loading...</div>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="selectedRecordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            @if ($selectedRecord)
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="selectedRecordModalTitle">Zone Vehicle Price Details</h5>
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
                                                    class="text-light fw-semibold">Zone Name</small></td>
                                            <td style="width: 50%;text-wrap:wrap;" class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->zone->name ?? 'N/A' }}</h6>
                                            </td>
                                        </tr>

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

                                        <tr>
                                            <td style="width: 50%;text-wrap:wrap;" class="align-middle"><small
                                                    class="text-light fw-semibold">Payment Options</small></td>
                                            <td style="width: 50%;text-wrap:wrap;" class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->payment_type ?? 'N/A' }}</h6>
                                            </td>
                                        </tr>


                                        <tr>
                                            <td style="width: 50%;text-wrap:wrap;" class="align-middle"><small
                                                    class="text-light fw-semibold">Base Price</small></td>
                                            <td style="width: 50%;text-wrap:wrap;" class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->base_price ?? 'N/A' }}</h6>
                                            </td>
                                        </tr>


                                        <tr>
                                            <td style="width: 50%;text-wrap:wrap;" class="align-middle"><small
                                                    class="text-light fw-semibold">Price Per Distance</small></td>
                                            <td style="width: 50%;text-wrap:wrap;" class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->price_per_distance ?? 'N/A' }}
                                                </h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="width: 50%;text-wrap:wrap;" class="align-middle"><small
                                                    class="text-light fw-semibold">Waiting Charge</small></td>
                                            <td style="width: 50%;text-wrap:wrap;" class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->waiting_charge ?? 'N/A' }}</h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="width: 50%;text-wrap:wrap;" class="align-middle"><small
                                                    class="text-light fw-semibold">Price Per Time</small></td>
                                            <td style="width: 50%;text-wrap:wrap;" class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->price_per_time ?? 'N/A' }}</h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="width: 50%;text-wrap:wrap;" class="align-middle"><small
                                                    class="text-light fw-semibold">Cancellation Fee</small></td>
                                            <td style="width: 50%;text-wrap:wrap;" class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->cancellation_fee ?? 'N/A' }}
                                                </h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="width: 50%;text-wrap:wrap;" class="align-middle"><small
                                                    class="text-light fw-semibold">Admin Commission</small></td>
                                            <td style="width: 50%;text-wrap:wrap;" class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->admin_commision ?? 'N/A' }}
                                                </h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="width: 50%;text-wrap:wrap;" class="align-middle"><small
                                                    class="text-light fw-semibold">Service Tax</small></td>
                                            <td style="width: 50%;text-wrap:wrap;" class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->service_tax ?? 'N/A' }}</h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="width: 50%;text-wrap:wrap;" class="align-middle"><small
                                                    class="text-light fw-semibold">GST Tax</small></td>
                                            <td style="width: 50%;text-wrap:wrap;" class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->gst_tax ?? 'N/A' }}</h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="width: 50%;text-wrap:wrap;" class="align-middle"><small
                                                    class="text-light fw-semibold">Active</small></td>
                                            <td style="width: 50%;text-wrap:wrap;" class="py-3">
                                                <h6 class="mb-0">{{ $selectedRecord->active ? 'Yes' : 'No' }}</h6>
                                            </td>
                                        </tr>



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
            $wire.on('hideAddVehiclePrice', () => {
                $('#addVehiclePrice').modal('hide');
            });
            $wire.on('editVehiclePrice', () => {
                $('#editVehiclePrice').modal('show');
            });
            $wire.on('hideEditVehiclePrice', () => {
                $('#editVehiclePrice').modal('hide');
            });
        </script>
    @endscript

    @include('livewire.notification')

</div>
