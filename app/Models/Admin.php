<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Admin extends BaseOracleModel
{
    protected $table = 'ADMIN';
    protected $primaryKey = 'admin_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}

