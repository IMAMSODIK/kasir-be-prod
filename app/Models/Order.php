<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    public function meja(): BelongsTo{
        return $this->belongsTo(Meja::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
