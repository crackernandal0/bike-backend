<?php

namespace App\Livewire\Chauffeur;

use App\Models\Chauffeur\Chauffeur;
use App\Models\Chauffeur\ChauffeurHire;
use Livewire\Component;
use Livewire\WithPagination;

class ChauffeurBookings extends Component
{
    use WithPagination;


    public $search;
    public $chauffeur_booking_id;
    public $selectedRecordId;

    public $bookingId;  // Store booking ID to be edited

    // Define public variables for each field
    public $chauffeur_id, $pickup, $dropoff, $pickup_location_type, $destination_location_type, $date, $start_time, $end_time, $vehicle_type, $preferred_vehicle, $chauffeur_type, $hire_type, $event_type, $child_seats, $specific_vehicle_models, $additional_amenities, $additional_requests, $price, $admin_commission, $gst, $service_tax, $status, $payment_status;


    public function showRecord($recordId)
    {
        $this->selectedRecordId = $recordId;
    }

    public function editBooking($id)
    {
        // Fetch the booking record by ID
        $booking = ChauffeurHire::findOrFail($id);

        // Assign values to public variables
        $this->bookingId = $booking->id;
        $this->chauffeur_id = $booking->chauffeur_id;
        $this->pickup = $booking->pickup;
        $this->dropoff = $booking->dropoff;
        $this->pickup_location_type = $booking->pickup_location_type;
        $this->destination_location_type = $booking->destination_location_type;
        $this->date = $booking->date;
        $this->start_time = $booking->start_time;
        $this->end_time = $booking->end_time;
        $this->vehicle_type = $booking->vehicle_type;
        $this->preferred_vehicle = $booking->preferred_vehicle;
        $this->chauffeur_type = $booking->chauffeur_type;
        $this->hire_type = $booking->hire_type;
        $this->event_type = $booking->event_type;
        $this->child_seats = $booking->child_seats;
        $this->specific_vehicle_models = $booking->specific_vehicle_models;
        $this->additional_amenities = $booking->additional_amenities;
        $this->additional_requests = $booking->additional_requests;
        $this->price = $booking->price;
        $this->admin_commission = $booking->admin_commission;
        $this->gst = $booking->gst;
        $this->service_tax = $booking->service_tax;
        $this->status = $booking->status;
        $this->payment_status = $booking->payment_status;

        // Dispatch event to open modal
        $this->dispatch('openEditModal');
    }

    public function updateBooking()
    {
        // Validation rules
        $this->validate([
            'chauffeur_id' => 'required|exists:chauffeurs,id',
            'pickup' => 'required|max:500',
            'dropoff' => 'required|max:500',
            'pickup_location_type' => 'nullable|string|max:255',
            'chauffeur_id' => 'required|exists:chauffeurs,id',
            'destination_location_type' => 'nullable|string|max:255',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'nullable',
            'vehicle_type' => 'required|string|max:255',
            'preferred_vehicle' => 'nullable|string|max:255',
            'chauffeur_type' => 'required|in:with_vehicle,without_vehicle',
            'hire_type' => 'required|string|max:255',
            'event_type' => 'nullable|string|max:255',
            'child_seats' => 'nullable|integer|min:0',
            'specific_vehicle_models' => 'nullable|string',
            'additional_amenities' => 'nullable|string',
            'additional_requests' => 'nullable|string',
            'price' => 'required',
            'admin_commission' => 'required',
            'gst' => 'nullable',
            'service_tax' => 'nullable',
            'status' => 'required|string',
            'payment_status' => 'required|string',
        ]);

        // Find the booking record and update it
        $booking = ChauffeurHire::findOrFail($this->bookingId);

        $booking->update([
            'chauffeur_id' => $this->chauffeur_id,
            'pickup' => $this->pickup,
            'dropoff' => $this->dropoff,
            'pickup_location_type' => $this->pickup_location_type,
            'destination_location_type' => $this->destination_location_type,
            'date' => $this->date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'vehicle_type' => $this->vehicle_type,
            'preferred_vehicle' => $this->preferred_vehicle,
            'chauffeur_type' => $this->chauffeur_type,
            'hire_type' => $this->hire_type,
            'event_type' => $this->event_type,
            'child_seats' => $this->child_seats,
            'specific_vehicle_models' => $this->specific_vehicle_models,
            'additional_amenities' => $this->additional_amenities,
            'additional_requests' => $this->additional_requests,
            'price' => $this->price,
            'admin_commission' => $this->admin_commission,
            'gst' => $this->gst,
            'service_tax' => $this->service_tax,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
        ]);

        // Close modal and refresh the booking list
        $this->dispatch('closeEditModal');
        session()->flash('message', 'Booking updated successfully.');
    }




    public function render()
    {
        // Query with the 'driver' relation
        $chauffeurs = ChauffeurHire::query()->with('user:id,name,country_code,phone_number', 'chauffeur.driver:id,full_name,phone_number');

        // Check if there is a search input
        if ($this->search) {
            $chauffeurs->where(function ($query) {
                // Search in Chauffeur fields
                $query->where('pickup', 'like', '%' . $this->search . '%')
                    ->orWhere('dropoff', 'like', '%' . $this->search . '%');

                // Search in related Driver fields
                $query->orWhereHas('user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('phone_number', 'like', '%' . $this->search . '%');
                });
            });
        }

        // Add the 'withCount' method to count completed rides (optional)
        $chauffeurBookings = $chauffeurs->latest()->paginate(10);

        $selectedRecord = null;
        if ($this->selectedRecordId) {
            $selectedRecord = ChauffeurHire::where('id', $this->selectedRecordId)
                ->with('user:id,name,country_code,phone_number', 'chauffeur.driver:id,full_name,phone_number')
                ->first();
            $this->selectedRecordId = null;
            $this->dispatch('show-record');
        }

        $chauffeurs = Chauffeur::with('driver:id,full_name')->where('status', 'approved')->latest()->get();

        return view('livewire.chauffeur.chauffeur-bookings', compact('chauffeurBookings', 'selectedRecord', 'chauffeurs'));
    }
}
