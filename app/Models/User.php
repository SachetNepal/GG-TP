<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    protected $connection = 'oracle';
    protected $table = 'USER';
    protected $primaryKey = 'user_id';
    public $timestamps = false;
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone_num',
        'address',
        'created_at',
        'role',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'password' => 'hashed',
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
        return $this->hasOne(Customer::class, 'user_id', 'user_id');
    }

    public function trader(): HasOne
    {
        return $this->hasOne(Trader::class, 'user_id', 'user_id');
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
