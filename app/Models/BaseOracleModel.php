<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class BaseOracleModel extends Model
{
    protected $connection = 'oracle';
}

