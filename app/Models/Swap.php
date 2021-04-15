<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Swap extends Model
{
    use HasFactory;

    protected $fillable = ['txid','fee', 'block_number', 'block_timestamp', 'before_balance_trx', 'after_balance_trx'];

}
