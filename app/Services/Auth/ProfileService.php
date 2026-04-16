<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProfileService
{
    public function getProfile(User $user): array
    {
        $user->load(['customer', 'trader.shop']);

        return [
            'user' => $user,
            'customer' => $user->customer,
            'trader' => $user->trader,
            'shop' => $user->trader?->shop,
        ];
    }

    public function updateProfile(User $user, array $data): User
    {
        DB::connection('oracle')->transaction(function () use ($user, $data): void {
            $user->update($data);
        });

        return $user->fresh();
    }
}

