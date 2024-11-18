<?php

namespace App\Livewire\Zone;

use App\Models\Service\Zone;
use Livewire\Component;
use Livewire\WithPagination;

class Zones extends Component
{
    use WithPagination;

    public $search;

  

    public function changeStatus($id, $status)
    {
        $zone = Zone::find($id);
        if ($zone) {
            $zone->active = $status;
            $zone->save();

            session()->flash('success', 'Zone status updated successfully!');
        }
    }

    public function deleteZone($id)
    {
        $zone = Zone::find($id);
        if ($zone) {
            $zone->delete();

            session()->flash('success', 'Zone deleted successfully!');
        }
    }


    public function render()
    {
        $zones = Zone::query()
            ->with('serviceLocation')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.zone.zones', compact('zones'));
    }
}
