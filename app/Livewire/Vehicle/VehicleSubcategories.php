<?php

namespace App\Livewire\Vehicle;

use App\Models\Vehicles\Amenity;
use App\Models\Vehicles\VehicleSubcategory;
use App\Models\Vehicles\VehicleType;
use Illuminate\Support\Facades\File;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Livewire\WithPagination;

class VehicleSubcategories extends Component
{
    use WithPagination, WithFileUploads;

    public $search;
    public $name;
    public $vehicle_type_id;
    public $image;
    public $passangers;
    public $short_amenties;
    public $editId;
    public $editName;
    public $editVehicle_type_id;
    public $editImage;
    public $oldImage;
    public $editShortAmenties;
    public $editPassangers;
    public $editSpecifications = [];

    // Handle new image upload
    public $specifications = [];

    // Method to add a new empty specification
    public function addField()
    {
        $this->specifications[] = ['icon' => '', 'type' => '', 'value' => '']; // Add an empty specification field
    }

    // Method to remove a specification field by index
    public function removeField($index)
    {
        if (isset($this->specifications[$index])) {
            unset($this->specifications[$index]);
            $this->specifications = array_values($this->specifications); // Re-index the array
        }
    }

    // Handle new image upload
    public $amenities = [];

    // Method to add a new empty specification
    public function addAmenityField()
    {
        $this->amenities[] = ['name' => '', 'description' => '']; // Add an empty specification field
    }

    // Method to remove a specification field by index
    public function removeAmenityField($index)
    {
        if (isset($this->amenities[$index])) {
            unset($this->amenities[$index]);
            $this->amenities = array_values($this->amenities); // Re-index the array
        }
    }

    public function submit()
    {
        $this->validate([
            'name' => 'required|max:255',
            'vehicle_type_id' => 'required|exists:vehicle_types,id',
            'image' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:5000',
            'short_amenties' => 'nullable|max:255',
            'specifications' => 'nullable|array',
            'amenities' => 'nullable|array'
        ]);

        // Handle vehicle image upload
        $imagePath = null;
        if ($this->image) {
            $imagePath = uploadMedia($this->image, 'vehicle_subcategories');
        }

        // Process specifications: handle optional icon upload and clean data
        $processedSpecifications = array_map(function ($specification) {
            // If the icon field is not empty, handle the image upload
            if (!empty($specification['icon'])) {
                $specification['icon'] = uploadMedia($specification['icon'], 'specifications');
            } else {
                $specification['icon'] = null; // Set null if no icon provided
            }

            // Clean other fields if empty
            $specification['type'] = $specification['type'] ?? null;
            $specification['value'] = $specification['value'] ?? null;

            return $specification;
        }, $this->specifications);



        // Save data to the database
        $vehicleSubcategory = VehicleSubcategory::create([
            'name' => $this->name,
            'image' => $imagePath,
            'vehicle_type_id' => $this->vehicle_type_id,
            'short_amenties' => $this->short_amenties,
            'passangers' => $this->passangers,
            'specifications' => json_encode($processedSpecifications),
        ]);

        // Process and associate amenities
        foreach ($this->amenities as $amenityData) {
            if (!empty($amenityData['name'])) {
                // Find or create the amenity
                $amenity = Amenity::firstOrCreate(
                    ['name' => $amenityData['name']],
                    ['description' => $amenityData['description'] ?? null]
                );

                // Attach the amenity to the vehicle subcategory via the pivot table
                $vehicleSubcategory->amenities()->attach($amenity->id);
            }
        }

        // Reset and flash success message
        $this->dispatch('hideAddVehicleSubCategory');
        $this->reset();
        session()->flash('success', 'Vehicle SubCategory added successfully!');
    }


    // Edit vehicle type
    public function editVehicleSubCategory($id)
    {
        $vehicleSubCategory = VehicleSubcategory::with('amenities')->find($id); // Eager load amenities relationship
        if ($vehicleSubCategory) {
            $this->editId = $vehicleSubCategory->id;
            $this->editName = $vehicleSubCategory->name;
            $this->editVehicle_type_id = $vehicleSubCategory->vehicle_type_id;
            $this->oldImage = $vehicleSubCategory->image;
            $this->editVehicle_type_id = $vehicleSubCategory->vehicle_type_id;
            $this->editShortAmenties = $vehicleSubCategory->short_amenties;
            $this->editPassangers = $vehicleSubCategory->passangers;

            // Decode the JSON specifications from the database into an array
            $this->specifications = !empty($vehicleSubCategory->specifications)
                ? json_decode($vehicleSubCategory->specifications, true)
                : [];

            // Fetch amenities associated with the vehicle subcategory
            $this->amenities = $vehicleSubCategory->amenities->map(function ($amenity) {
                return [
                    'name' => $amenity->name,
                    'description' => $amenity->description ?? '',
                ];
            })->toArray();

            $this->dispatch('editVehicleSubCategory');
        } else {
            session()->flash('error', 'Vehicle SubCategory not found!');
        }
    }


    // Update vehicle type
    public function update()
    {
        $this->validate([
            'editName' => 'required|max:255',
            'editVehicle_type_id' => 'required|exists:vehicle_types,id',
            'editImage' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:5000',
            'editShortAmenties' => 'nullable|max:255',
            'editSpecifications' => 'nullable|array',
            'amenities' => 'nullable|array'
        ]);

        // Find the vehicle subcategory to update
        $vehicleSubcategory = VehicleSubcategory::find($this->editId);

        // Handle image upload for vehicle subcategory
        $imagePath = $vehicleSubcategory->image;
        if ($this->editImage) {
            // If new image is uploaded, replace the old image
            $imagePath = uploadMedia($this->editImage, 'vehicle_subcategories', 80, $vehicleSubcategory->image);
        }
        // Process specifications: handle icon upload only if a new icon is provided
        $processedSpecifications = array_map(function ($specification) use ($vehicleSubcategory) {
            // Check if the specification has an icon and handle it accordingly
            if (!empty($specification['icon']) && is_file($specification['icon']) && !is_string($specification['icon'])) {
                // Upload new icon if provided and it's a file
                $specification['icon'] = uploadMedia($specification['icon'], 'specifications');
            } else {
                // Keep the existing icon from the previous data if no new icon is uploaded
                $specification['icon'] = $specification['icon'] ?? null;
            }

            // Ensure the other fields are properly set or null
            $specification['type'] = $specification['type'] ?? null;
            $specification['value'] = $specification['value'] ?? null;

            return $specification;
        }, $this->specifications);

        // Update the vehicle subcategory details
        $vehicleSubcategory->update([
            'name' => $this->editName,
            'image' => $imagePath,
            'vehicle_type_id' => $this->editVehicle_type_id,
            'short_amenties' => $this->editShortAmenties,
            'passangers' => $this->editPassangers,
            'specifications' => json_encode($processedSpecifications),
        ]);

        // Handle amenities: Delete existing and save new
        // Detach all previous amenities
        $vehicleSubcategory->amenities()->detach();

        // Attach new amenities (create new if they don't exist)
        foreach ($this->amenities as $amenityData) {
            if (!empty($amenityData['name'])) {
                // Find or create the amenity
                $amenity = Amenity::firstOrCreate(
                    ['name' => $amenityData['name']],
                    ['description' => $amenityData['description'] ?? null]
                );

                // Attach the new amenity to the vehicle subcategory
                $vehicleSubcategory->amenities()->attach($amenity->id);
            }
        }

        // Dispatch event to hide the edit modal
        $this->dispatch('hideEditVehicleSubCategory');

        // Reset the component and show success message
        session()->flash('success', 'Vehicle Subcategory updated successfully!');
        $this->reset();
    }


    // Delete vehicle type
    public function deletevehicleSubcategory($id)
    {
        $vehicleSubcategory = vehicleSubcategory::find($id);
        if ($vehicleSubcategory) {
            if (File::exists(public_path($vehicleSubcategory->image))) {
                File::delete(public_path($vehicleSubcategory->image));
            }
            $vehicleSubcategory->delete();
            session()->flash('success', 'Vehicle subcategory deleted successfully!');
        } else {
            session()->flash('error', 'Vehicle subcategory not found!');
        }
    }

    // Render view with search and pagination
    public function render()
    {
        $vehicleSubcategorys = VehicleSubcategory::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('short_amenties', 'like', '%' . $this->search . '%')
                    ->orWhere('specifications', 'like', '%' . $this->search . '%');
            })
            ->paginate(10);

        $vehicleTypes = VehicleType::get();

        return view('livewire.vehicle.vehicle-subcategories', compact('vehicleSubcategorys', 'vehicleTypes'));
    }
}
