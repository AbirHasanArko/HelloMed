<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeePayout extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'month', 'amount', 'status', 'paid_at', 'confirmed_at'];
    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'confirmed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
