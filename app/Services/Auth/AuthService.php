<?php

namespace App\Services\Auth;

use App\Models\Customer;
use App\Models\Shop;
use App\Models\Trader;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService
{
    public function registerCustomer(array $data): array
    {
        return DB::connection('oracle')->transaction(function () use ($data) {
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => strtolower($data['email']),
                'password' => $data['password'],
                'phone_num' => $data['phone_num'],
                'address' => $data['address'],
                'created_at' => now(),
                'role' => 'customer',
            ]);

            $customer = Customer::create(['user_id' => $user->user_id]);
            $verification = $this->createVerification($user->user_id);

            return compact('user', 'customer', 'verification');
        });
    }

    public function registerTrader(array $data): array
    {
        return DB::connection('oracle')->transaction(function () use ($data) {
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => strtolower($data['email']),
                'password' => $data['password'],
                'phone_num' => $data['phone_num'],
                'address' => $data['address'],
                'created_at' => now(),
                'role' => 'trader',
            ]);

            $trader = Trader::create(['user_id' => $user->user_id]);
            $shop = Shop::create([
                'shop_name' => $data['shop_name'],
                'location' => $data['location'],
                'trader_id' => $trader->trader_id,
            ]);
            $verification = $this->createVerification($user->user_id);

            return compact('user', 'trader', 'shop', 'verification');
        });
    }

    public function login(array $credentials): User
    {
        $user = User::query()->where('email', strtolower($credentials['email']))->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            abort(401, 'Invalid credentials');
        }

        Auth::login($user);
        return $user;
    }

    public function logout(): void
    {
        Auth::logout();
    }

    public function verifyEmailToken(string $token): Verification
    {
        $verification = Verification::query()
            ->where('verification_token', $token)
            ->firstOrFail();

        if ($verification->expires_at && $verification->expires_at->isPast()) {
            abort(422, 'Verification token expired');
        }

        $verification->is_verified = true;
        $verification->save();

        return $verification;
    }

    protected function createVerification(int $userId): Verification
    {
        return Verification::create([
            'verification_token' => Str::uuid()->toString(),
            'created_at' => now(),
            'expires_at' => now()->addHours(24),
            'is_verified' => false,
            'user_id' => $userId,
        ]);
    }
}

