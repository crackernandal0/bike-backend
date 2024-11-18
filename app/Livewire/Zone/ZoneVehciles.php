<?php

namespace App\Livewire\Zone;

use App\Models\Service\Zone;
use App\Models\Service\ZoneTypePrice;
use App\Models\Vehicles\VehicleSubcategory;
use App\Models\Vehicles\VehicleType;
use Livewire\Component;
use Livewire\WithPagination;

class ZoneVehciles extends Component
{
    use WithPagination;


    public $selectedRecordId;

    public $zone_id;
    public $vehicle_type_id;
    public $vehicle_subcategory_id;
    public $payment_types = []; // Array to store selected payment types (Cash, Gateway, Wallet)
    public $payment_type; // Array to store selected payment types (Cash, Gateway, Wallet)

    public $base_price;
    public $base_distance;
    public $price_per_distance;
    public $waiting_charge;
    public $price_per_time;
    public $cancellation_fee;
    public $admin_commision;
    public $service_tax;
    public $gst_tax;
    public $active = 1;

    public $editId;
    public $editMode = false;

    protected $rules = [
        'zone_id' => 'required|exists:zones,id',
        'vehicle_type_id' => 'required|exists:vehicle_types,id',
        'vehicle_subcategory_id' => 'required|exists:vehicle_subcategories,id',
        'payment_types' => 'required|array',
        'base_price' => 'required|numeric',
        'price_per_distance' => 'required|numeric',
        'waiting_charge' => 'required|numeric',
        'price_per_time' => 'required|numeric',
        'cancellation_fee' => 'required|numeric',
        'admin_commision' => 'required|numeric',
        'service_tax' => 'required|numeric',
        'gst_tax' => 'required|numeric',
        'active' => 'required|boolean',
    ];

    public function changeStatus($id, $status)
    {
        $ZoneTypePrice = ZoneTypePrice::find($id);
        if ($ZoneTypePrice) {
            $ZoneTypePrice->active = $status;
            $ZoneTypePrice->save();

            session()->flash('success', 'Zone Vehicle status updated successfully!');
        }
    }


    // Store new ZoneTypePrice
    public function submit()
    {
        $this->validate();

        // Convert the array of selected payment types into a comma-separated string
        $this->payment_type = implode(', ', $this->payment_types);

        // Store in the database
        ZoneTypePrice::create(array_merge($this->validatedData(), [
            'payment_type' => $this->payment_type
        ]));
        session()->flash('success', 'Zone Type Price added successfully!');

        $this->dispatch('hideAddVehiclePrice');
        $this->resetInputFields();
    }

    // Edit ZoneTypePrice
    public function editZoneTypePrice($id)
    {
        $zoneTypePrice = ZoneTypePrice::findOrFail($id);
        $this->editMode = true;

        // Set form fields
        $this->editId = $zoneTypePrice->id;
        $this->fill($zoneTypePrice->toArray());

        $this->dispatch('editVehiclePrice');
    }

    // Update ZoneTypePrice
    public function update()
    {
        $this->validate();

        $this->validate([
            'payment_types' => 'required|array',
        ]);
    
        // Convert the array of selected payment types into a comma-separated string
        $this->payment_type = implode(', ', $this->payment_types);
    
        $zoneTypePrice = ZoneTypePrice::findOrFail($this->editId);
        
        // Update the record
        $zoneTypePrice->update(array_merge($this->validatedData(), [
            'payment_type' => $this->payment_type
        ]));
    
        session()->flash('success', 'Zone Type Price updated successfully!');

        $this->resetInputFields();
        $this->dispatch('hideEditVehiclePrice');
    }

    // Delete ZoneTypePrice
    public function deleteZoneTypePrice($id)
    {
        ZoneTypePrice::findOrFail($id)->delete();

        session()->flash('success', 'Zone Type Price deleted successfully!');
    }

    public function showRecord($recordId)
    {
        $this->selectedRecordId = $recordId;
    }


    // Helper function to reset input fields after form submission or cancellation
    private function resetInputFields()
    {
        $this->reset([
            'zone_id',
            'vehicle_type_id',
            'vehicle_subcategory_id',
            'payment_type',
            'base_price',
            'price_per_distance',
            'waiting_charge',
            'price_per_time',
            'cancellation_fee',
            'admin_commision',
            'service_tax',
            'gst_tax',
            'active',
            'editId',
            'editMode'
        ]);
    }

    public function render()
    {
        // Fetch zones, vehicle types, and subcategories
        $zones = Zone::where('active', 1)->get();
        $vehicleTypes = VehicleType::all();
        $vehicleSubcategories = VehicleSubcategory::all();

        // Paginated zone type prices for listing
        $zoneTypePrices = ZoneTypePrice::with(['zone', 'vehicleType', 'vehicleSubcategory'])
            ->latest()
            ->paginate(10);



        $selectedRecord = null;
        if ($this->selectedRecordId) {
            $selectedRecord = ZoneTypePrice::where('id', $this->selectedRecordId)
                ->with(['zone', 'vehicleType', 'vehicleSubcategory'])
                ->first();
            $this->selectedRecordId = null;
            $this->dispatch('show-record');
        }



        return view('livewire.zone.zone-vehciles', [
            'zones' => $zones,
            'vehicleTypes' => $vehicleTypes,
            'vehicleSubcategories' => $vehicleSubcategories,
            'zoneTypePrices' => $zoneTypePrices,
            'selectedRecord' => $selectedRecord,
        ]);
    }

    // Utility function to get the validated data
    private function validatedData()
    {
        return $this->validate();
    }
}
