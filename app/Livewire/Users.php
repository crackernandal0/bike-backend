<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Users extends Component
{
    use WithPagination;
    public $search;

    public function changeStatus($id, $status)
    {
        $user = User::find($id);
        if ($user) {
            $user->active = $status;
            $user->save();

            session()->flash('success', 'User account status updated successfully!');
        }
    }


    public function render()
    {

        $user = User::query();

        if ($this->search) {
            $user->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('email', 'like', '%' . $this->search . '%')
                ->orWhere('phone_number', 'like', '%' . $this->search . '%');
        }
        // Add the 'withCount' method to count completed rides
        $users = $user->withCount(['rides as completed_rides_count' => function ($query) {
            $query->where('ride_status', 'completed');
        }])->latest()->paginate(10);



        return view('livewire.users', compact('users'));
    }
}
