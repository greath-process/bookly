<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\RegisterSendLinkRequest;
use App\Http\Requests\FormUsernameRequest;
use App\Http\Requests\Auth\LoginSendLinkRequest;
use App\Http\Requests\Auth\VerifyLoginRequest;
use App\Models\LoginToken;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __invoke(
        FormUsernameRequest $request
    ): View
    {
        return view('auth.login');
    }

    public function login(
        LoginSendLinkRequest $request
    ): void
    {
        User::whereEmail($request->email)->first()->sendLoginLink();
    }

    public function register(
        RegisterSendLinkRequest $request
    ): User
    {
        $user = User::create([
            'email' => $request->email,
            'slug' => $request->userName,
        ]);

        $user->sendLoginLink();

        return $user;
    }

    public function registerUpdate(
        RegisterSendLinkRequest $request
    ): User
    {
        $user = $request->user;

        $user->update([
            'email' => $request->email,
            'slug' => $request->userName,
        ]);

        $user->sendLoginLink();

        return $user;
    }

    public function verifyLogin(
        VerifyLoginRequest $request,
        $token
    ): RedirectResponse
    {
        $token = LoginToken::whereToken(hash('sha256', $token))->first();
        if (!$token || !$request->hasValidSignature() || !$token->isValid()) {
            return redirect(route('login', ['error' => 'link']));
        }

        $token->consume();
        $user = $token->user;
        if (!$user->emailIsConfirmed()) {
            $user->emailConfirm();
        }

        Auth::login($user);

        return redirect(route('profile'));
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();

        return redirect(route('main.page'));
    }
}
