<?php

namespace App\Livewire\Zone;

use App\Models\Country;
use App\Models\Service\ServiceLocation;
use App\Models\TimeZone;
use Livewire\Component;
use Livewire\WithPagination;

class ServiceLocations extends Component
{
    use WithPagination;

    public $name;
    public $country_id;
    public $timezone;
    public $active;
    public $editId;
    public $editName;
    public $editCountryId;
    public $editTimezone;
    public $editActive;
    public $search;

    // Validation rules
    protected $rules = [
        'name' => 'required|max:255',
        'country_id' => 'required|exists:countries,id',
        'timezone' => 'required|string',
    ];

    // Reset form fields
    public function resetFields()
    {
        $this->reset(['name', 'country_id', 'timezone', 'active']);
    }

    public function changeStatus($id, $status)
    {
        $ServiceLocation = ServiceLocation::find($id);
        if ($ServiceLocation) {
            $ServiceLocation->active = $status;
            $ServiceLocation->save();

            session()->flash('success', 'Service Location status updated successfully!');
        }
    }


    // Create a new service location
    public function submit()
    {
        $this->validate();

        ServiceLocation::create([
            'name' => $this->name,
            'country_id' => $this->country_id,
            'timezone' => $this->timezone,
            'active' => $this->active ? true : false,
        ]);

        $this->dispatch('hideAddServiceLocation');

        $this->resetFields();
        session()->flash('success', 'Service location added successfully!');
    }

    // Edit a service location
    public function editServiceLocation($id)
    {
        $serviceLocation = ServiceLocation::find($id);

        if ($serviceLocation) {
            $this->editId = $serviceLocation->id;
            $this->editName = $serviceLocation->name;
            $this->editCountryId = $serviceLocation->country_id;
            $this->editTimezone = $serviceLocation->timezone;
            $this->editActive = $serviceLocation->active ? true : false;

            $this->dispatch('editServiceLocation');
        } else {
            session()->flash('error', 'Service location not found!');
        }
    }

    // Update a service location
    public function update()
    {
        $this->validate([
            'editName' => 'required|max:255',
            'editCountryId' => 'required|exists:countries,id',
            'editTimezone' => 'required|string',
        ]);

        $serviceLocation = ServiceLocation::find($this->editId);
        if ($serviceLocation) {
            $serviceLocation->update([
                'name' => $this->editName,
                'country_id' => $this->editCountryId,
                'timezone' => $this->editTimezone,
                'active' => $this->editActive ? true : false,
            ]);

            $this->dispatch('hideEditServiceLocation');
            session()->flash('success', 'Service location updated successfully!');
            $this->reset(['editId', 'editName', 'editCountryId', 'editTimezone', 'editActive']);
        }
    }

    // Delete a service location
    public function deleteServiceLocation($id)
    {
        $serviceLocation = ServiceLocation::find($id);
        if ($serviceLocation) {
            $serviceLocation->delete();
            session()->flash('success', 'Service location deleted successfully!');
        } else {
            session()->flash('error', 'Service location not found!');
        }
    }

    // Render view with search and pagination
    public function render()
    {

        $serviceLocations = ServiceLocation::query()
            ->with('country')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->paginate(10);

        return view('livewire.zone.service-locations', [
            'serviceLocations' => $serviceLocations,
            'countries' => Country::all(),
        ]);
    }
}
