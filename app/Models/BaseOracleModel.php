<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class BaseOracleModel extends Model
{
    protected $connection = 'oracle';

    /** Oracle IDs are strings (e.g. SH1, P1, U1), not auto-increment integers. */
    public $incrementing = false;

    protected $keyType = 'string';
}

