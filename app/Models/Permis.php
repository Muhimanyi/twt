<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Permis extends Model
{
    use HasFactory;

    protected $fillable = [
        'conducteur_id',
        'license_number',
        'category',
        'issued_at',
        'expires_at',
        'status',
        'restrictions',
        'qr_code',
    ];

    /**
     * Get the driver that owns the license.
     */
    public function conducteur(): BelongsTo
    {
        return $this->belongsTo(Conducteur::class);
    }
}
