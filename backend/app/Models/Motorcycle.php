<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Motorcycle extends Model
{
    use HasFactory;

    protected $fillable = [
        'make',
        'model',
        'year',
        'price_per_day',
        'status',
        'image_url',
        'description',
    ];

    protected $casts = [
        'year' => 'integer',
        'price_per_day' => 'decimal:2',
        'status' => 'string',
    ];

    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }
}
