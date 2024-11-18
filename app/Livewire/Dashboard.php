<?php

namespace App\Livewire;

use App\Models\Chauffeur\ChauffeurHire;
use App\Models\Common\ContactQuery;
use App\Models\DrivingSchool\Enrollment;
use App\Models\Ride\Ride;
use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Component;

class Dashboard extends Component
{
    #[Title('Dashboard - Femirides APP')]
    public function render()
    {
        $users = User::count();
        $rides = Ride::count();
        $chauffeurHires = ChauffeurHire::count();
        $drivingSchools = Enrollment::count();
        $contactQueries = ContactQuery::count();
        return view('livewire.dashboard', compact('users', 'rides', 'chauffeurHires', 'drivingSchools', 'contactQueries'));
    }
}
