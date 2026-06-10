<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Debardeur extends Model
{
    use HasFactory;

    protected $fillable = [
        'province_id',
        'sector',
        'last_name',
        'middle_name',
        'first_name',
        'birth_place',
        'birth_date',
        'gender',
        'father_name',
        'mother_name',
        'marital_status',
        'nationality',
        'id_type',
        'id_number',
        'id_expiration_date',
        'migratory_doc_type',
        'migratory_doc_number',
        'profession',
        'assignment_place',
        'association_cooperative',
        'member_card_number',
        'vest_number',
        'phone',
        'residence_address',
        'permanent_number',
        'photo_path',
    ];

    protected $appends = ['photo_url'];

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path ? asset('storage/'.$this->photo_path) : null;
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    protected static function booted(): void
    {
        static::creating(function (Debardeur $debardeur) {
            if (! $debardeur->permanent_number) {
                $year = now()->format('Y');
                $count = static::whereYear('created_at', $year)->count() + 1;
                $debardeur->permanent_number = "DEB-{$year}-".str_pad($count, 5, '0', STR_PAD_LEFT);
            }
        });
    }
}
