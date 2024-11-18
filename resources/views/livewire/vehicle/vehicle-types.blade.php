@section('vehicles_active', 'open')
@section('vehicle_types_active', 'active')
<div>
    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->



        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Admin /</span> Vehicle Management</h4>


                <div class="d-flex align-items-center">
                    <button data-bs-toggle="modal" data-bs-target="#addVehicleType" class="btn btn-primary">
                        <span class="tf-icons bx bx-plus"></span>&nbsp; Add Vehicle Type
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
                                <th>Name</th>
                                <th>Icon</th>
                                <th>Actions</th>

                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @if (count($vehicleTypes) > 0)
                                @foreach ($vehicleTypes as $index => $vehicleType)
                                    <tr>
                                        <td>{{ ($vehicleTypes->currentPage() - 1) * $vehicleTypes->perPage() + $index + 1 }}
                                        </td>
                                        <td>{{ $vehicleType->name }}</td>
                                        <td><img src="{{ asset($vehicleType->icon) }}" width="50"></td>
                                        <td>
                                            <div class="dropdown dropup">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                    aria-hascategory="true" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu" data-popper-placement="top-start">

                                                    <button class="dropdown-item"
                                                        wire:click="editVehicleType({{ $vehicleType->id }})"><i
                                                            class="bx bx-pencil me-1"></i>
                                                        Edit</button>


                                                    <button class="dropdown-item"
                                                        wire:confirm="Are you sure you want to delete this vehicle type?"
                                                        wire:click="deleteVehicleType({{ $vehicleType->id }})"><i
                                                            class="bx bx-trash me-1"></i>
                                                        Delete</button>

                                                </div>
                                            </div>
                                        </td>


                                    </tr>
                                @endforeach
                            @else
                                <tr class="text-center">
                                    <td colspan="5">
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
                            {{ $vehicleTypes->links('custom-pagination') }}
                        </div>
                    </div>
                </div>
            </div>


        </div>
        <!-- / Content -->

        <div class="content-backdrop fade"></div>
    </div>
    <!-- Content wrapper -->




    <div wire:ignore.self class="modal fade" id="addVehicleType" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addVehicleTypeTitle">Add New Vehicle Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mx-auto">

                            <form>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label" for="basic-icon-default-company">Name</label>
                                            <div class="input-group input-group-merge">
                                                <span class="input-group-text"><i class="bx bx-heading"></i></span>
                                                <input type="text" wire:model="name"
                                                    class="form-control @error('name') is-invalid @enderror"
                                                    placeholder="Enter name">
                                            </div>
                                            @error('name')
                                                <div class="error">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="basic-icon-default-company">Icon</label>
                                            <div class="input-group input-group-merge">
                                                <span class="input-group-text"><i class="bx bx-link-alt"></i></span>
                                                <input type="file" wire:model="icon" class="form-control">
                                            </div>
                                            @error('icon')
                                                <div class="error">
                                                    {{ $message }}
                                                </div>
                                            @enderror

                                            @if ($icon)
                                                <div class="row my-3">
                                                    <div class="col-4">
                                                        <img class="img-fluid" src="{{ $icon->temporaryUrl() }}"
                                                            alt="">
                                                    </div>
                                                </div>
                                            @endif
                                        </div>








                                    </div>



                                </div>



                                <button wire:click.prevent="submit" class="btn btn-primary mt-5"
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



    <div wire:ignore.self class="modal fade" id="editVehicleType" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editVehicleTypeTitle">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mx-auto">
                            <div class="card mb-4">

                                <div class="card-body">
                                    <form>

                                        <div class="row">
                                            <div class="col-12">
                                                <div class="mb-3">
                                                    <label class="form-label"
                                                        for="basic-icon-default-company">Name</label>
                                                    <div class="input-group input-group-merge">
                                                        <span class="input-group-text"><i
                                                                class="bx bx-heading"></i></span>
                                                        <input type="text" wire:model="editName"
                                                            class="form-control @error('editName') is-invalid @enderror"
                                                            placeholder="Enter name">
                                                    </div>
                                                    @error('editName')
                                                        <div class="error">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label"
                                                        for="basic-icon-default-company">Icon</label>
                                                    <div class="input-group input-group-merge">
                                                        <span class="input-group-text"><i
                                                                class="bx bx-link-alt"></i></span>
                                                        <input type="file" wire:model="editIcon"
                                                            class="form-control">
                                                    </div>

                                                    @error('editIcon')
                                                        <div class="error">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                    @if ($editIcon)
                                                        <div class="row my-3">
                                                            <div class="col-4">
                                                                <img class="img-fluid"
                                                                    src="{{ $editIcon->temporaryUrl() }}"
                                                                    alt="">
                                                            </div>
                                                        </div>
                                                    @elseif ($oldIcon)
                                                        <div class="row my-3">
                                                            <div class="col-4">
                                                                <img class="img-fluid" src="{{ asset($oldIcon) }}"
                                                                    alt="">
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>






                                            </div>



                                        </div>



                                        <button wire:click.prevent="update" class="btn btn-primary mt-5"
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
        </div>
    </div>



    @script
        <script>
            $wire.on('hideAddVehicleType', () => {
                $('#addVehicleType').modal('hide');
            });
            $wire.on('editVehicleType', () => {
                $('#editVehicleType').modal('show');
            });
            $wire.on('hideEditVehicleType', () => {
                $('#editVehicleType').modal('hide');
            });
        </script>
    @endscript




    @include('livewire.notification')

</div>
