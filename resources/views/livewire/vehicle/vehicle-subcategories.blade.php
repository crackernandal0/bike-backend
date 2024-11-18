@section('vehicles_active', 'open')
@section('vehicle_subcategories_active', 'active')
<div>
    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->



        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Admin /</span> Vehicle Subcategories</h4>


                <div class="d-flex align-items-center">
                    <button data-bs-toggle="modal" data-bs-target="#addVehicleSubCategory" class="btn btn-primary">
                        <span class="tf-icons bx bx-plus"></span>&nbsp; Add Vehicle Category
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
                                <th>Image</th>
                                <th>Type</th>
                                <th>Short Ameties</th>
                                <th>Passangers</th>
                                <th>Actions</th>

                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @if (count($vehicleSubcategorys) > 0)
                                @foreach ($vehicleSubcategorys as $index => $vehicleSubCategory)
                                    <tr>
                                        <td>{{ ($vehicleSubcategorys->currentPage() - 1) * $vehicleSubcategorys->perPage() + $index + 1 }}
                                        </td>
                                        <td>{{ $vehicleSubCategory->name }}</td>
                                        <td><img src="{{ asset($vehicleSubCategory->image) }}" width="50"></td>
                                        <td>{{ $vehicleSubCategory->vehicleType->name ?? null }}</td>
                                        <td>{{ $vehicleSubCategory->short_amenties }}</td>
                                        <td>{{ $vehicleSubCategory->passangers }}</td>

                                        <td>
                                            <div class="dropdown dropup">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                    aria-hascategory="true" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu" data-popper-placement="top-start">

                                                    <button class="dropdown-item"
                                                        wire:click="editVehicleSubCategory({{ $vehicleSubCategory->id }})"><i
                                                            class="bx bx-pencil me-1"></i>
                                                        Edit</button>


                                                    <button class="dropdown-item"
                                                        wire:confirm="Are you sure you want to delete this vehicle type?"
                                                        wire:click="deletevehicleSubcategory({{ $vehicleSubCategory->id }})"><i
                                                            class="bx bx-trash me-1"></i>
                                                        Delete</button>

                                                </div>
                                            </div>
                                        </td>


                                    </tr>
                                @endforeach
                            @else
                                <tr class="text-center">
                                    <td colspan="8">
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
                            {{ $vehicleSubcategorys->links('custom-pagination') }}
                        </div>
                    </div>
                </div>
            </div>


        </div>
        <!-- / Content -->

        <div class="content-backdrop fade"></div>
    </div>
    <!-- Content wrapper -->




    <div wire:ignore.self class="modal fade" id="addVehicleSubCategory" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addVehicleSubCategoryTitle">Add New Vehicle Type</h5>
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
                                            <label class="form-label"
                                                for="basic-icon-default-company">Passangers</label>
                                            <div class="input-group input-group-merge">
                                                <span class="input-group-text"><i class="bx bx-heading"></i></span>
                                                <input type="number" wire:model="passangers"
                                                    class="form-control @error('passangers') is-invalid @enderror"
                                                    placeholder="Enter passangers">
                                            </div>
                                            @error('passangers')
                                                <div class="error">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="basic-icon-default-company">Image</label>
                                            <div class="input-group input-group-merge">
                                                <span class="input-group-text"><i class="bx bx-link-alt"></i></span>
                                                <input type="file" wire:model="image" class="form-control">
                                            </div>
                                            @error('image')
                                                <div class="error">
                                                    {{ $message }}
                                                </div>
                                            @enderror

                                            @if ($image)
                                                <div class="row my-3">
                                                    <div class="col-4">
                                                        <img class="img-fluid" src="{{ $image->temporaryUrl() }}"
                                                            alt="">
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="basic-icon-default-company">Short
                                                Amenties</label>
                                            <div class="input-group input-group-merge">
                                                <span class="input-group-text"><i class="bx bx-heading"></i></span>
                                                <input type="text" wire:model="short_amenties"
                                                    class="form-control @error('short_amenties') is-invalid @enderror"
                                                    placeholder="Enter Short Amenties">
                                            </div>
                                            @error('short_amenties')
                                                <div class="error">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="basic-icon-default-company">Vehcile
                                                Type</label>
                                            <div class="input-group input-group-merge">
                                                <span class="input-group-text"><i class="bx bxs-car"></i></span>

                                                <select wire:model="vehicle_type_id"
                                                    class="form-control @error('vehicle_type_id') is-invalid @enderror">
                                                    <option value="">Select</option>
                                                    @foreach ($vehicleTypes as $vehicleType)
                                                        <option value="{{ $vehicleType->id }}">
                                                            {{ $vehicleType->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @error('vehicle_type_id')
                                                <div class="error">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <div class="mt-3">
                                            <h6>Specifications</h6>

                                            <!-- Loop through the specifications array and show input fields for each specification -->
                                            @foreach ($specifications as $index => $specification)
                                                <div class="mb-3">
                                                    <label class="form-label">Specification
                                                        {{ $index + 1 }}</label>

                                                    <!-- Icon Field -->
                                                    <div class="input-group input-group-merge mb-2">
                                                        <span class="input-group-text"><i
                                                                class="bx bx-image"></i></span>
                                                        <input type="file"
                                                            wire:model="specifications.{{ $index }}.icon"
                                                            class="form-control @error('specifications.{{ $index }}.icon') is-invalid @enderror">
                                                    </div>
                                                    @error('specifications.{{ $index }}.icon')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror

                                                    <!-- Type Field -->
                                                    <div class="input-group input-group-merge mb-2">
                                                        <span class="input-group-text"><i
                                                                class="bx bx-category"></i></span>
                                                        <input type="text"
                                                            wire:model="specifications.{{ $index }}.type"
                                                            class="form-control @error('specifications.{{ $index }}.type') is-invalid @enderror"
                                                            placeholder="Enter Specification Type (e.g., Connectivity)">
                                                    </div>
                                                    @error('specifications.{{ $index }}.type')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror

                                                    <!-- Value Field -->
                                                    <div class="input-group input-group-merge mb-2">
                                                        <span class="input-group-text"><i
                                                                class="bx bx-info-circle"></i></span>
                                                        <input type="text"
                                                            wire:model="specifications.{{ $index }}.value"
                                                            class="form-control @error('specifications.{{ $index }}.value') is-invalid @enderror"
                                                            placeholder="Enter Specification Value (e.g., WiFi)">
                                                    </div>
                                                    @error('specifications.{{ $index }}.value')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror

                                                    <!-- Minus Button to remove the field with tooltip -->
                                                    <button type="button" data-bs-toggle="tooltip"
                                                        data-popup="tooltip-custom" data-bs-placement="top"
                                                        title="Remove Field"
                                                        wire:click.prevent="removeField({{ $index }})"
                                                        class="btn btn-danger mt-2">
                                                        <i class="bx bx-minus"></i>
                                                    </button>
                                                </div>
                                            @endforeach

                                            <!-- Plus Button to add a new field -->
                                            <button type="button" class="btn btn-primary mt-3"
                                                wire:click="addField">
                                                <i class="bx bx-plus"></i> Add Specification
                                            </button>
                                        </div>

                                        <div class="mt-3">
                                            <h6>Amenities</h6>

                                            <!-- Loop through the specifications array and show input fields for each specification -->
                                            @foreach ($amenities as $index => $amenity)
                                                <div class="mb-3">
                                                    <label class="form-label">Amenity
                                                        {{ $index + 1 }}</label>



                                                    <!-- name Field -->
                                                    <div class="input-group input-group-merge mb-2">
                                                        <span class="input-group-text"><i
                                                                class="bx bx-category"></i></span>
                                                        <input type="text"
                                                            wire:model="amenities.{{ $index }}.name"
                                                            class="form-control @error('amenities.{{ $index }}.name') is-invalid @enderror"
                                                            placeholder="Enter Amenity Name">
                                                    </div>
                                                    @error('amenities.{{ $index }}.name')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror

                                                    <!-- Value Field -->
                                                    <div class="input-group input-group-merge mb-2">
                                                        <span class="input-group-text"><i
                                                                class="bx bx-info-circle"></i></span>
                                                        <input type="text"
                                                            wire:model="amenities.{{ $index }}.description"
                                                            class="form-control @error('amenities.{{ $index }}.description') is-invalid @enderror"
                                                            placeholder="Enter Description">
                                                    </div>
                                                    @error('amenities.{{ $index }}.description')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror

                                                    <!-- Minus Button to remove the field with tooltip -->
                                                    <button type="button" data-bs-toggle="tooltip"
                                                        data-popup="tooltip-custom" data-bs-placement="top"
                                                        title="Remove Field"
                                                        wire:click.prevent="removeAmenityField({{ $index }})"
                                                        class="btn btn-danger mt-2">
                                                        <i class="bx bx-minus"></i>
                                                    </button>
                                                </div>
                                            @endforeach

                                            <!-- Plus Button to add a new field -->
                                            <button type="button" class="btn btn-primary mt-3"
                                                wire:click="addAmenityField">
                                                <i class="bx bx-plus"></i> Add Amenity
                                            </button>
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



    <div wire:ignore.self class="modal fade" id="editVehicleSubCategory" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editVehicleSubCategoryTitle">Edit Category</h5>
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
                                                        for="basic-icon-default-company">Passangers</label>
                                                    <div class="input-group input-group-merge">
                                                        <span class="input-group-text"><i
                                                                class="bx bx-heading"></i></span>
                                                        <input type="number" wire:model="editPassangers"
                                                            class="form-control @error('editPassangers') is-invalid @enderror"
                                                            placeholder="Enter passangers">
                                                    </div>
                                                    @error('editPassangers')
                                                        <div class="error">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label"
                                                        for="basic-icon-default-company">Image</label>
                                                    <div class="input-group input-group-merge">
                                                        <span class="input-group-text"><i
                                                                class="bx bx-link-alt"></i></span>
                                                        <input type="file" wire:model="editImage"
                                                            class="form-control">
                                                    </div>

                                                    @error('editImage')
                                                        <div class="error">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                    @if ($editImage)
                                                        <div class="row my-3">
                                                            <div class="col-4">
                                                                <img class="img-fluid"
                                                                    src="{{ $editImage->temporaryUrl() }}"
                                                                    alt="">
                                                            </div>
                                                        </div>
                                                    @elseif ($oldImage)
                                                        <div class="row my-3">
                                                            <div class="col-4">
                                                                <img class="img-fluid" src="{{ asset($oldImage) }}"
                                                                    alt="">
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label" for="basic-icon-default-company">Short
                                                        Amenties</label>
                                                    <div class="input-group input-group-merge">
                                                        <span class="input-group-text"><i
                                                                class="bx bx-heading"></i></span>
                                                        <input type="text" wire:model="editShortAmenties"
                                                            class="form-control @error('editShortAmenties') is-invalid @enderror"
                                                            placeholder="Enter Short Amenties">
                                                    </div>
                                                    @error('editShortAmenties')
                                                        <div class="error">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label" for="basic-icon-default-company">Vehcile
                                                        Type</label>
                                                    <div class="input-group input-group-merge">
                                                        <span class="input-group-text"><i
                                                                class="bx bxs-car"></i></span>

                                                        <select wire:model="editVehicle_type_id"
                                                            class="form-control @error('editVehicle_type_id') is-invalid @enderror">
                                                            <option value="">Select</option>

                                                            @foreach ($vehicleTypes as $vehicleType)
                                                                <option value="{{ $vehicleType->id }}">
                                                                    {{ $vehicleType->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    @error('editVehicle_type_id')
                                                        <div class="error">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>

                                                <div class="mt-3">
                                                    <h6>Specifications</h6>

                                                    <!-- Loop through the specifications array and show input fields for each specification -->
                                                    @foreach ($specifications as $index => $specification)
                                                        <div class="mb-3">
                                                            <label class="form-label">Specification
                                                                {{ $index + 1 }}</label>

                                                            <!-- Icon Field -->
                                                            <div class="input-group input-group-merge mb-2">
                                                                <span class="input-group-text"><i
                                                                        class="bx bx-image"></i></span>
                                                                <input type="file"
                                                                    wire:model="specifications.{{ $index }}.icon"
                                                                    class="form-control @error('specifications.{{ $index }}.icon') is-invalid @enderror">
                                                            </div>
                                                            @error('specifications.{{ $index }}.icon')
                                                                <div class="error">{{ $message }}</div>
                                                            @enderror

                                                            <!-- Type Field -->
                                                            <div class="input-group input-group-merge mb-2">
                                                                <span class="input-group-text"><i
                                                                        class="bx bx-category"></i></span>
                                                                <input type="text"
                                                                    wire:model="specifications.{{ $index }}.type"
                                                                    class="form-control @error('specifications.{{ $index }}.type') is-invalid @enderror"
                                                                    placeholder="Enter Specification Type (e.g., Connectivity)">
                                                            </div>
                                                            @error('specifications.{{ $index }}.type')
                                                                <div class="error">{{ $message }}</div>
                                                            @enderror

                                                            <!-- Value Field -->
                                                            <div class="input-group input-group-merge mb-2">
                                                                <span class="input-group-text"><i
                                                                        class="bx bx-info-circle"></i></span>
                                                                <input type="text"
                                                                    wire:model="specifications.{{ $index }}.value"
                                                                    class="form-control @error('specifications.{{ $index }}.value') is-invalid @enderror"
                                                                    placeholder="Enter Specification Value (e.g., WiFi)">
                                                            </div>
                                                            @error('specifications.{{ $index }}.value')
                                                                <div class="error">{{ $message }}</div>
                                                            @enderror

                                                            <!-- Minus Button to remove the field with tooltip -->
                                                            <button type="button" data-bs-toggle="tooltip"
                                                                data-popup="tooltip-custom" data-bs-placement="top"
                                                                title="Remove Field"
                                                                wire:click.prevent="removeField({{ $index }})"
                                                                class="btn btn-danger mt-2">
                                                                <i class="bx bx-minus"></i>
                                                            </button>
                                                        </div>
                                                    @endforeach

                                                    <!-- Plus Button to add a new field -->
                                                    <button type="button" class="btn btn-primary mt-3"
                                                        wire:click="addField">
                                                        <i class="bx bx-plus"></i> Add Specification
                                                    </button>
                                                </div>

                                                <div class="mt-3">
                                                    <h6>Amenities</h6>

                                                    <!-- Loop through the specifications array and show input fields for each specification -->
                                                    @foreach ($amenities as $index => $amenity)
                                                        <div class="mb-3">
                                                            <label class="form-label">Amenity
                                                                {{ $index + 1 }}</label>



                                                            <!-- name Field -->
                                                            <div class="input-group input-group-merge mb-2">
                                                                <span class="input-group-text"><i
                                                                        class="bx bx-category"></i></span>
                                                                <input type="text"
                                                                    wire:model="amenities.{{ $index }}.name"
                                                                    class="form-control @error('amenities.{{ $index }}.name') is-invalid @enderror"
                                                                    placeholder="Enter Amenity Name">
                                                            </div>
                                                            @error('amenities.{{ $index }}.name')
                                                                <div class="error">{{ $message }}</div>
                                                            @enderror

                                                            <!-- Value Field -->
                                                            <div class="input-group input-group-merge mb-2">
                                                                <span class="input-group-text"><i
                                                                        class="bx bx-info-circle"></i></span>
                                                                <input type="text"
                                                                    wire:model="amenities.{{ $index }}.description"
                                                                    class="form-control @error('amenities.{{ $index }}.description') is-invalid @enderror"
                                                                    placeholder="Enter Description">
                                                            </div>
                                                            @error('amenities.{{ $index }}.description')
                                                                <div class="error">{{ $message }}</div>
                                                            @enderror

                                                            <!-- Minus Button to remove the field with tooltip -->
                                                            <button type="button" data-bs-toggle="tooltip"
                                                                data-popup="tooltip-custom" data-bs-placement="top"
                                                                title="Remove Field"
                                                                wire:click.prevent="removeAmenityField({{ $index }})"
                                                                class="btn btn-danger mt-2">
                                                                <i class="bx bx-minus"></i>
                                                            </button>
                                                        </div>
                                                    @endforeach

                                                    <!-- Plus Button to add a new field -->
                                                    <button type="button" class="btn btn-primary mt-3"
                                                        wire:click="addAmenityField">
                                                        <i class="bx bx-plus"></i> Add Amenity
                                                    </button>
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
            $wire.on('hideAddVehicleSubCategory', () => {
                $('#addVehicleSubCategory').modal('hide');
            });
            $wire.on('editVehicleSubCategory', () => {
                $('#editVehicleSubCategory').modal('show');
            });
            $wire.on('hideEditVehicleSubCategory', () => {
                $('#editVehicleSubCategory').modal('hide');
            });
        </script>
    @endscript




    @include('livewire.notification')

</div>
