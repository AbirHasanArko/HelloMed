<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HospitalFundTransaction extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'reference_id', 'amount'];
    protected $casts = [
        'amount' => 'decimal:2',
    ];
}
