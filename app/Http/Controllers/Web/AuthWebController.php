<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterCustomerRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AuthWebController extends Controller
{
    public function __construct(protected AuthService $authService)
    {
    }

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $user = $this->authService->attemptLogin(
            $request->validated(),
            $request->boolean('remember')
        );

        if (!$user) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Invalid email or password.']);
        }

        if ($user->role === 'trader') {
            return redirect('/GG-TP/trader-portal/trader/dashboard.php');
        }

        return redirect()->intended(route('home'));
    }

    public function showRegister(): View
    {
        return view('auth.register', [
            'type' => request('type', 'customer'),
        ]);
    }

    public function register(RegisterCustomerRequest $request): RedirectResponse
    {
        $this->authService->registerCustomer($request->validated());

        return redirect()->route('login')->with('status', 'Account created. You can sign in now.');
    }

    public function logout(): RedirectResponse
    {
        $this->authService->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('home');
    }
}
