<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meja extends Model
{
    /** @use HasFactory<\Database\Factories\MejaFactory> */
    use HasFactory;

    protected $fillable = [
        'nama_meja',
        'status',
        'qrcode',
        'slug'
    ];

    public function order(): HasMany{
        return $this->hasMany(Order::class);
    }
}
