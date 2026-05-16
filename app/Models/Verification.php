<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Verification extends BaseOracleModel
{
    protected $table = 'VERIFICATION';
    protected $primaryKey = 'verification_id';
    public $timestamps = false;

    protected $fillable = [
        'verification_id',
        'verification_token',
        'verification_code',
        'email',
        'purpose',
        'status',
        'created_at',
        'expires_at',
        'is_verified',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_verified' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}

