<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificat extends Model
{
    use HasFactory;

    protected $fillable = [
        'engin_id',
        'type',
        'certificate_number',
        'issued_at',
        'expires_at',
        'owner_name',
        'status',
        'qr_code',
    ];

    /**
     * Get the engine associated with the certificate.
     */
    public function engin(): BelongsTo
    {
        return $this->belongsTo(Engin::class);
    }
}
