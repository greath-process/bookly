<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;

class UsernameOnMain extends Component
{
    public $userName;
    public $notAllow;

    public function render()
    {
        $this->notAllow = !!User::where('slug', $this->userName)->count()
            || in_array($this->userName, config('reserved-usernames'));

        return view('livewire.username-on-main');
    }
}
