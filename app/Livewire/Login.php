<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Login extends Component
{

    #[Layout('layouts.login')]

    #[Validate('required|exists:admins,username|max:10|min:1')]
    public $username;

    #[Validate('required|max:20|min:1')]
    public $password;

    public $remember;

    function loginUser()
    {
        $attemptsKey = 'login_attempts_' . sha1(request()->ip());

        if (cache()->has($attemptsKey) && cache($attemptsKey) >= 4) {
            session()->flash('error', 'Too many login attempts. Try again later.');
        } else {
            $this->validate();
            if (auth('admin')->attempt(['username' => $this->username, 'password' => $this->password], $this->remember)) {
                return redirect()->route('dashboard');
            } else {
                $errorMessage = 'Invalid Password!';
                session()->flash('error', $errorMessage);
                $this->incrementLoginAttempts($attemptsKey);
                return ['error' => $errorMessage];
            }
        }
    }

    function incrementLoginAttempts($key)
    {
        $attempts = cache($key, 0) + 1;
        cache([$key => $attempts], now()->addMinutes(10)); // Lock for 10 minutes
    }

    function render()
    {
        return view('livewire.login');
    }
}