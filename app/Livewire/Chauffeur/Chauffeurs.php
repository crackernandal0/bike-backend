<?php

namespace App\Livewire\Chauffeur;

use App\Models\Chauffeur\Chauffeur;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Chauffeurs extends Component
{
    use WithPagination, WithFileUploads;


    public $search;
    public $chauffeur_id;
    public $selectedRecordId;

    public $image;
    public $tagline;
    public $description;
    public $skills_certifications = [];
    public $additional_services = [];
    public $availability;
    public $status;
    public $old_image;


    public function changeStatus($id, $status)
    {
        $user = Chauffeur::find($id);
        if ($user) {
            $user->status = $status;
            $user->save();

            session()->flash('success', 'Chauffeur status updated successfully!');
        }
    }
    public function deleteChauffeur($id)
    {
        $user = Chauffeur::find($id);
        if ($user) {
            $user->delete();
            $user->save();

            session()->flash('success', 'Chauffeur deleted successfully!');
        }else{
            session()->flash('error', 'Chauffeur not found');
        }
    
    }
    public function editChauffeur($id)
    {
        $chauffeur = Chauffeur::find($id);

        if ($chauffeur) {
            // Populate Chauffeur basic data
            $this->chauffeur_id = $chauffeur->id;
            $this->tagline = $chauffeur->tagline;
            $this->description = $chauffeur->description;
            $this->availability = $chauffeur->availability;
            $this->status = $chauffeur->status;
            $this->old_image = $chauffeur->image;

            // Populate skills and certifications (JSON fields)
            $this->skills_certifications = $chauffeur->skills_certifications ? json_decode($chauffeur->skills_certifications, true) : [];
            $this->additional_services = $chauffeur->additional_services ? json_decode($chauffeur->additional_services, true) : [];

            // Dispatch the editChauffeur event
            $this->dispatch('editChauffeur');
        } else {
            session()->flash('error', 'Chauffeur not found!');
        }
    }

    public function showRecord($recordId)
    {
        $this->selectedRecordId = $recordId;
    }


    public function addSkill()
    {
        $this->skills_certifications[] = '';
    }

    public function removeSkill($index)
    {
        unset($this->skills_certifications[$index]);
        $this->skills_certifications = array_values($this->skills_certifications); // Re-index the array
    }

    public function addService()
    {
        $this->additional_services[] = '';
    }

    public function removeService($index)
    {
        unset($this->additional_services[$index]);
        $this->additional_services = array_values($this->additional_services); // Re-index the array
    }

    public function updateChauffeur()
    {
        $this->validate([
            'tagline' => 'required|string|max:255',
            'description' => 'required|string',
            'availability' => 'nullable|string',
            'status' => 'required|in:pending,approved,declined',
            'skills_certifications' => 'required|array',
            'additional_services' => 'required|array',
            'image' => 'nullable|image|max:5000',
        ]);

        // Find the Chauffeur to update
        $chauffeur = Chauffeur::find($this->chauffeur_id);

        if ($chauffeur) {
            $chauffeur->tagline = $this->tagline;
            $chauffeur->description = $this->description;
            $chauffeur->availability = $this->availability;
            $chauffeur->status = $this->status;
            $chauffeur->skills_certifications = json_encode($this->skills_certifications);
            $chauffeur->additional_services = json_encode($this->additional_services);

            // Handle image upload if new image exists
            if ($this->image) {
                $chauffeur->image = uploadMedia($this->image, 'chauffeurs', 80, $chauffeur->image);
            }

            $chauffeur->save();

            $this->dispatch('hideEditChauffeur');


            session()->flash('success', 'Chauffeur updated successfully!');
        } else {
            session()->flash('error', 'Chauffeur not found!');
        }
    }





    public function render()
    {
        // Query with the 'driver' relation
        $chauffeurs = Chauffeur::query()->with('driver:id,full_name,country_code,phone_number');

        // Check if there is a search input
        if ($this->search) {
            $chauffeurs->where(function ($query) {
                // Search in Chauffeur fields
                $query->where('tagline', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhere('availability', 'like', '%' . $this->search . '%');

                // Search in related Driver fields
                $query->orWhereHas('driver', function ($q) {
                    $q->where('full_name', 'like', '%' . $this->search . '%')
                        ->orWhere('phone_number', 'like', '%' . $this->search . '%');
                });
            });
        }

        // Add the 'withCount' method to count completed rides (optional)
        $chauffeurs = $chauffeurs->latest()->paginate(10);

        $selectedRecord = null;
        if ($this->selectedRecordId) {
            $selectedRecord = Chauffeur::where('id', $this->selectedRecordId)
                ->with('driver:id,full_name,country_code,phone_number')
                ->first();
            $this->selectedRecordId = null;
            $this->dispatch('show-record');
        }


        return view('livewire.chauffeur.chauffeurs', compact('chauffeurs', 'selectedRecord'));
    }
}
