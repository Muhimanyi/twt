<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    protected $fillable = [
        'reference',
        'taxe_id',
        'payable_type',
        'payable_id',
        'amount',
        'currency',
        'user_id',
        'province_id',
        'payment_method',
        'notes',
        'is_printed',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_printed' => 'boolean',
        'paid_at' => 'datetime',
    ];

    public function taxe()
    {
        return $this->belongsTo(Taxe::class);
    }

    public function payable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }
}
