<?php

namespace App\Http\Livewire;

use App\Http\Controllers\AuthController;
use App\Http\Requests\Auth\RegisterSendLinkRequest;
use App\Http\Requests\FormUsernameRequest;
use App\Http\Requests\Auth\LoginSendLinkRequest;
use App\Models\User;
use App\Services\CustomerIO;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Livewire\Component;

class RegisterLogin extends Component
{
    public $userName;
    public $notAllow;
    public $email;
    public $sended = false;
    public $isLogin = true;
    public $error;
    public $user;
    public $emailValidation = 'required|email';
    public $nameValidation = 'required';

    public function mount(
        FormUsernameRequest $request
    ): void
    {
        if ($request->userName) {
            $this->userName = $request->userName;
        }
        if ($request->error) {
            $this->error = __('auth.error');
        }
    }

    public function tryAgain(): void
    {
        $this->sended = false;
    }

    public function validationEmail(): void
    {
        Validator::make(
            ['email' => $this->email],
            ['email' => $this->emailValidation],
        )->validate();
    }

    public function validationUserName(): void
    {
        Validator::make(
            ['userName' => $this->userName],
            ['userName' => $this->nameValidation],
        )->validate();
    }

    public function clearErrors(): void
    {
        $this->error = '';
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function sendForm(): void
    {
        $this->checkAllow();

        if (!$this->notAllow) {
            $request = $this->isLogin ? new LoginSendLinkRequest() : new RegisterSendLinkRequest();
            $request->email = $this->email;
            $this->validationEmail();

            if ($this->isLogin) {
                (new AuthController)->login($request);
            } else {
                $request->userName = $this->userName;
                $this->validationUserName();

                if ($this->user) {
                    $request->user = $this->user;
                    $this->user = (new AuthController)->registerUpdate($request);
                } else {
                    $this->user = (new AuthController)->register($request);
                    (new CustomerIO($this->user))->create();
                }
            }

            $this->sended = true;
            $this->clearErrors();
        }
    }

    public function checkAllow(): void
    {
        $this->notAllow =
            !!User::where('slug', $this->userName)->count() &&
            (!$this->user || $this->user && $this->user->id !== User::where('slug', $this->userName)->first()->id)
            || in_array($this->userName, config('reserved-usernames'));

        $this->isLogin = User::where('email', $this->email)->count() > 0 || !$this->email && !$this->userName;
    }

    public function render(): View
    {
        $this->checkAllow();

        return view('livewire.register-login');
    }
}
