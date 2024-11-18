<?php

namespace App\Livewire;

use App\Jobs\SendNotificationJob;
use App\Models\Driver\Driver;
use App\Models\User;
use Livewire\Component;

class PushNotification extends Component
{
    public $title;
    public $description;
    public $type = 'all';

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'type' => 'required|in:users,drivers,instructors,all',
    ];

    public function send()
    {
        $this->validate();

        $tokens = [];

        // Fetch tokens based on the type of recipients
        switch ($this->type) {
            case 'users':
                // Fetch active and verified users' FCM tokens
                $tokens = User::where('active', 1)
                    ->whereNotNull('fcm_token')
                    ->pluck('fcm_token')
                    ->toArray();
                break;

            case 'drivers':
                // Fetch active drivers (excluding instructors)
                $tokens = Driver::where('role', '!=', 'instructor')
                    ->where('active', 1)
                    ->whereNotNull('fcm_token')
                    ->pluck('fcm_token')
                    ->toArray();
                break;

            case 'instructors':
                // Fetch active instructors (excluding regular drivers)
                $tokens = Driver::where('role', '!=', 'driver')
                    ->where('active', 1)
                    ->whereNotNull('fcm_token')
                    ->pluck('fcm_token')
                    ->toArray();
                break;

            case 'all':
                // Fetch all active users and drivers' FCM tokens
                $userTokens = User::where('active', 1)
                    ->whereNotNull('fcm_token')
                    ->pluck('fcm_token')
                    ->toArray();

                $driverTokens = Driver::where('active', 1)
                    ->whereNotNull('fcm_token')
                    ->pluck('fcm_token')
                    ->toArray();

                // Merge both token arrays
                $tokens = array_merge($userTokens, $driverTokens);
                break;
        }

        if (!empty($tokens)) {
            // Dispatch the job with all tokens
            SendNotificationJob::dispatch($tokens, $this->title, $this->description, 'notifications');

            // Optionally, start the queue worker
            $descriptionProcOpen = [
                ["pipe", "r"],
                ["pipe", "r"],
                ["pipe", "r"]
            ];

            proc_open("php " . base_path() . "/artisan queue:work --stop-when-empty", $descriptionProcOpen, $pipes);

            session()->flash('success', 'Notifications sent successfully!');
        } else {
            session()->flash('error', 'No users found for the selected recipients.');
        }

        $this->reset();
    }


    public function render()
    {
        return view('livewire.push-notification');
    }
}
