<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    use \App\Traits\HasAppMessages;

    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        $userRole = Role::firstWhere('slug', 'user');

        if ($userRole) {
            $user->roles()->attach($userRole);
        }

        $mailFailed = false;
        try {
            event(new Registered($user));
        } catch (\Exception $e) {
            \Log::error('Registration email could not be sent: ' . $e->getMessage());
            $mailFailed = true;
        }

        Auth::login($user);

        $request->session()->regenerate();

        if ($mailFailed) {
            return $this->checkWarning(
                'messages.auth.registration_mail_failed', 
                'home'
            );
        }

        return $this->checkSuccess(
            $user, 
            'messages.auth.registration_success', 
            'verification.notice',
            [],
            [],
            ['name' => $user->name]
        );
    }
}
