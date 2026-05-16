<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterCustomerRequest;
use App\Http\Requests\Auth\RegisterTraderRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService)
    {
    }

    public function registerCustomer(RegisterCustomerRequest $request): JsonResponse
    {
        $result = $this->authService->registerCustomer($request->validated());

        return response()->json([
            'message' => 'Customer registration successful. Check your email for a 6-digit verification code.',
            'user_id' => $result['user']->user_id,
            'customer_id' => $result['customer']->customer_id,
            'email' => $result['user']->email,
        ], 201);
    }

    public function registerTrader(RegisterTraderRequest $request): JsonResponse
    {
        $result = $this->authService->registerTrader($request->validated());

        return response()->json([
            'message' => 'Trader registration successful',
            'user_id' => $result['user']->user_id,
            'trader_id' => $result['trader']->trader_id,
            'shop_id' => $result['shop']->shop_id,
            'verification_token' => $result['verification']->verification_token,
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $email = strtolower(trim($request->validated('email')));
        $candidate = \App\Models\User::query()->whereRaw('LOWER(email) = ?', [$email])->first();

        if ($candidate
            && $this->authService->passwordMatches($request->validated('password'), (string) $candidate->password)
            && $this->authService->userNeedsEmailVerification($candidate)) {
            return response()->json([
                'message' => 'Please verify your email before logging in.',
            ], 403);
        }

        $user = $this->authService->login($request->validated());

        return response()->json([
            'message' => 'Login successful',
            'user' => [
                'user_id' => $user->user_id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logout successful']);
    }

    public function verifyEmail(string $token): JsonResponse
    {
        $verification = $this->authService->verifyEmailToken($token);

        return response()->json([
            'message' => 'Email verified',
            'verification_id' => $verification->verification_id,
        ]);
    }
}

