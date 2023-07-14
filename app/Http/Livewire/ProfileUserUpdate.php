<?php

namespace App\Http\Livewire;

use App\Models\User;
use App\Services\ImageGenerate;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class ProfileUserUpdate extends Component
{
    public $user;
    public $username;
    public $name;
    public $usernameEdit = false;
    public $taken;

    public function mount(): void
    {
        $this->user = Auth::user();
        $this->username = $this->user->slug;
        $this->name = $this->user->name;
    }

    public function usernameEdit(): void
    {
        $this->usernameEdit = true;
    }

    public function saveUserName(): void
    {
        if (!$this->taken) {
            if ($this->user->slug != $this->username) {
                $this->user->slug = $this->username;
                $this->user->save();
                $this->showSaveToast(__('profile.slug_saved'));
                Auth::setUser($this->user);
                (new ImageGenerate)->generateImageOpenGraph($this->user);
            }

            $this->usernameEdit = false;
        }
    }

    public function saveName(): void
    {
        $this->emit('set-focus');
        if($this->user->name != $this->name) {
            $this->user->name = $this->name;
            $this->user->save();
            $this->showSaveToast(__('profile.name_saved'));
        }
    }

    public function showSaveToast($text): void
    {
        $this->dispatchBrowserEvent('alert',[
            'type' => 'success',
            'message' => $text
        ]);
    }

    public function updated(): void
    {
        $this->taken = !!User::where('slug', $this->username)->count()
            && $this->user->id !== User::where('slug', $this->username)->first()->id
            || in_array($this->username, config('reserved-usernames'));
    }

    public function render(): View
    {
        return view('livewire.profile-user-update');
    }
}
