<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    protected $connection = 'oracle';
    protected $table = 'USERS';
    protected $primaryKey = 'user_id';
    public $timestamps = false;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'password',
        'phone_num',
        'address',
        'created_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => trim(($this->first_name ?? '').' '.($this->last_name ?? ''))
        );
    }

    public function verification(): HasOne
    {
        return $this->hasOne(Verification::class, 'user_id', 'user_id');
    }

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class, 'customer_id', 'user_id');
    }

    /** USERS table has no role column; customers have a CUSTOMER row with matching id. */
    public function getRoleAttribute(): string
    {
        if ($this->relationLoaded('customer')) {
            return $this->customer ? 'customer' : 'guest';
        }

        if (Trader::query()->where('trader_id', $this->user_id)->exists()) {
            return 'trader';
        }

        return Customer::query()->where('customer_id', $this->user_id)->exists()
            ? 'customer'
            : 'guest';
    }

    public function trader(): HasOne
    {
        return $this->hasOne(Trader::class, 'trader_id', 'user_id');
    }

    public function admin(): HasOne
    {
        return $this->hasOne(Admin::class, 'user_id', 'user_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id', 'user_id');
    }
}
