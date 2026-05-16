<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterCustomerRequest;
use App\Http\Requests\Auth\ResendVerificationRequest;
use App\Http\Requests\Auth\VerifyEmailRequest;
use App\Models\User;
use App\Services\Auth\AuthService;
use App\Services\Auth\EmailVerificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;

class AuthWebController extends Controller
{
    public function __construct(
        protected AuthService $authService,
        protected EmailVerificationService $emailVerificationService,
    ) {
    }

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $email = strtolower(trim($request->validated('email')));
        $user = User::query()->whereRaw('LOWER(email) = ?', [$email])->first();

        if ($user && $this->authService->passwordMatches($request->validated('password'), (string) $user->password)) {
            if ($this->authService->userNeedsEmailVerification($user)) {
                return redirect()
                    ->route('verify-email', ['email' => $user->email])
                    ->withErrors(['email' => 'Please verify your email before logging in.']);
            }
        }

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
        try {
            $result = $this->authService->registerCustomer($request->validated());
        } catch (RuntimeException $e) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['email' => $e->getMessage()]);
        }

        return redirect()
            ->route('verify-email', ['email' => $result['user']->email])
            ->with('status', 'Account created. Enter the 6-digit code sent to your email.');
    }

    public function showVerifyEmail(): View
    {
        return view('auth.verify-email', [
            'email' => request('email', session('pending_verification_email')),
        ]);
    }

    public function verifyEmail(VerifyEmailRequest $request): RedirectResponse
    {
        $this->emailVerificationService->verifySignupCode(
            $request->validated('email'),
            $request->validated('code'),
        );

        return redirect()
            ->route('login')
            ->with('status', 'Email verified successfully. You can sign in now.');
    }

    public function resendVerification(ResendVerificationRequest $request): RedirectResponse
    {
        try {
            $this->emailVerificationService->resendSignupCode($request->validated('email'));
        } catch (RuntimeException $e) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => $e->getMessage()]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'No account found for this email.']);
        }

        return redirect()
            ->route('verify-email', ['email' => $request->validated('email')])
            ->with('status', 'A new verification code has been sent to your email.');
    }

    public function logout(): RedirectResponse
    {
        $this->authService->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('home');
    }
}
