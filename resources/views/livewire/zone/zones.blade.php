@section('zones_active', 'open')
@section('all_zones_active', 'active')

<div>
    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->

        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Admin /</span> Zone Management</h4>

                <div class="d-flex align-items-center">
                    <a href="{{ route('create-zone') }}" class="btn btn-primary">
                        <span class="tf-icons bx bx-plus"></span>&nbsp; Add Zone
                    </a>
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
                                <th>Name</th>
                                <th>Service Location</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @if ($zones->count() > 0)
                                @foreach ($zones as $index => $zone)
                                    <tr>
                                        <td>{{ ($zones->currentPage() - 1) * $zones->perPage() + $index + 1 }}</td>
                                        <td>{{ $zone->name }}</td>
                                        <td>{{ $zone->serviceLocation->name ?? 'N/A' }}</td>

                                        <td>
                                            @if ($zone->active == true)
                                                <button
                                                    wire:confirm="Are you sure you want to change the status of this zone?"
                                                    wire:click.prevent="changeStatus({{ $zone->id }},0)"
                                                    data-bs-toggle="tooltip" data-bs-offset="0,4"
                                                    data-bs-placement="top" data-bs-html="true" title=""
                                                    data-bs-original-title="<i class='bx bx-edit-alt bx-xs' ></i> <span>Tap To Change Status</span>"
                                                    class="btn-sm btn-primary">
                                                    Active
                                                </button>
                                            @elseif($zone->active == false)
                                                <button
                                                    wire:confirm="Are you sure you want to change the status of this zone?"
                                                    wire:click.prevent="changeStatus({{ $zone->id }},1)"
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
                                                    <a href="{{ route('update-zone', $zone->id) }}"
                                                        class="dropdown-item">
                                                        <i class="bx bx-pencil me-1"></i> Edit
                                                    </a>
                                                    <button
                                                        wire:confirm="Are you sure you want to change the status of this zone?"
                                                        class="dropdown-item"
                                                        wire:click="deleteZone({{ $zone->id }})">
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
                            {{ $zones->links('custom-pagination') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- / Content -->

        <div class="content-backdrop fade"></div>
    </div>
    <!-- Content wrapper -->
    @include('livewire.notification')
</div>
