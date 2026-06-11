<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicineOrder extends Model
{
    use HasFactory, \App\Traits\Searchable;

    protected array $searchableFields = ['order_number', 'user.email', 'user.phone', 'user.name'];

    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_email',
        'order_number',
        'status',
        'total_amount',
        'payment_method',
        'payment_status',
        'transaction_id',
        'sender_number',
        'payment_callback_token',
        'payment_reference',
        'delivery_address',
        'phone',
        'notes',
        'prescription_path',
        'contains_prescription_items',
        'inventory_committed_at',
        'inventory_released_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'contains_prescription_items' => 'boolean',
        'inventory_committed_at' => 'datetime',
        'inventory_released_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(MedicineOrderItem::class);
    }
}
