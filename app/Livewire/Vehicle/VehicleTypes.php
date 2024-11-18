<?php

namespace App\Livewire\Vehicle;

use App\Models\Vehicles\VehicleType;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManagerStatic;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class VehicleTypes extends Component
{
    use WithPagination, WithFileUploads;

    public $search;
    public $name;
    public $icon;
    public $editId;
    public $editName;
    public $editIcon;
    public $oldIcon;

    // Handle new image upload

    // Create vehicle type
    public function submit()
    {
        $this->validate([
            'name' => 'required|max:255|unique:vehicle_types,name',
            'icon' => 'required|image|max:1024',
        ]);

        $iconPath = uploadMedia($this->icon, 'vehicle_types');

        VehicleType::create([
            'name' => $this->name,
            'icon' => $iconPath,
        ]);

        $this->dispatch('hideAddVehicleType');

        $this->reset(['name', 'icon']);
        session()->flash('success', 'Vehicle type added successfully!');
    }

    // Edit vehicle type
    public function editVehicleType($id)
    {
        $vehicleType = VehicleType::find($id);
        if ($vehicleType) {
            $this->editId = $vehicleType->id;
            $this->editName = $vehicleType->name;
            $this->oldIcon = $vehicleType->icon;

            $this->dispatch('editVehicleType');
        } else {
            session()->flash('error', 'Vehicle type not found!');
        }
    }

    // Update vehicle type
    public function update()
    {
        $this->validate([
            'editName' => 'required|max:255|unique:vehicle_types,name,' . $this->editId,
            'editIcon' => 'nullable|image|max:1024',
        ]);

        $vehicleType = VehicleType::find($this->editId);
        if ($this->editIcon) {
            $iconPath = uploadMedia($this->editIcon, 'vehicle_types', 80, $vehicleType->icon);
        } else {
            $iconPath = $vehicleType->icon;
        }

        $vehicleType->update([
            'name' => $this->editName,
            'icon' => $iconPath,
        ]);

        $this->dispatch('hideEditVehicleType');


        session()->flash('success', 'Vehicle type updated successfully!');
        $this->reset(['editId', 'editName', 'editIcon']);
    }

    // Delete vehicle type
    public function deleteVehicleType($id)
    {
        $vehicleType = VehicleType::find($id);
        if ($vehicleType) {
            if (File::exists(public_path($vehicleType->icon))) {
                File::delete(public_path($vehicleType->icon));
            }
            $vehicleType->delete();
            session()->flash('success', 'Vehicle type deleted successfully!');
        } else {
            session()->flash('error', 'Vehicle type not found!');
        }
    }

    // Render view with search and pagination
    public function render()
    {
        $vehicleTypes = VehicleType::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->paginate(10);


        return view('livewire.vehicle.vehicle-types', ['vehicleTypes' => $vehicleTypes]);
    }
}
