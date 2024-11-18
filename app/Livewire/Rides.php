<?php

namespace App\Livewire;

use App\Models\Ride\Ride;
use Livewire\Component;
use Livewire\WithPagination;

class Rides extends Component
{
    use WithPagination;
    public $no_of_records = 10;

    public $search;
    public $ride_status;
    public $payment_status;
    public $dateFrom;
    public $dateTo;

    public $selectedRecordId;

    public function showRecord($recordId)
    {
        $this->selectedRecordId = $recordId;
    }

    public function deleteApplication($id)
    {
        Ride::findOrFail($id)->delete();
        session('success', 'Ride Record deleted successfully');
    }


    public function render()
    {

        $rides = Ride::with('user', 'zone', 'driver')->when($this->search, function ($query) {
            $query->where('ride_number', 'like', '%' . $this->search . '%')
                ->orWhere('ride_status', 'like', '%' . $this->search . '%');
        })
            ->when($this->ride_status, function ($query) {
                $query->where('ride_status', 'like', '%' . $this->ride_status . '%');
            })
            ->when($this->payment_status, function ($query) {
                $query->where('payment_status', 'like', '%' . $this->payment_status . '%');
            })
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->latest();


        // Adjust pagination based on the selected number of records
        if ($this->no_of_records === 'all') {
            $rides = $rides->get(); // Fetch all records without pagination
        } else {
            $rides = $rides->paginate((int)$this->no_of_records ?? 10); // Default to 10 if not set
        }


        $selectedRecord = null;
        if ($this->selectedRecordId) {
            $selectedRecord = Ride::where('id', $this->selectedRecordId)->first();
            $this->selectedRecordId = null;
            $this->dispatch('show-record');
        }

        return view('livewire.rides', compact('rides', 'selectedRecord'));
    }
}
