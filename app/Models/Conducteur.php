<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Conducteur extends Model
{
    use HasFactory;

    protected $fillable = [
        'identification_number',
        'sector',
        'engin_id',
        'province_id',
        'name',
        'gender',
        'birth_place',
        'birth_date',
        'father_name',
        'mother_name',
        'marital_status',
        'nationality',
        'profession',
        'license_number',
        'license_category',
        'license_issued_at',
        'license_expires_at',
        'id_piece_type',
        'id_piece_number',
        'id_piece_issued_place',
        'id_piece_issued_at',
        'migratory_document_type',
        'visa_type',
        'phone',
        'email',
        'address',
        'affiliation',
        'transport_mode',
        'expiration_date',
        'photo_path',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'license_issued_at' => 'date',
        'license_expires_at' => 'date',
        'id_piece_issued_at' => 'date',
        'expiration_date' => 'date',
    ];

    public function engin()
    {
        return $this->belongsTo(Engin::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    protected $appends = ['photo_url'];

    public function getPhotoUrlAttribute()
    {
        return $this->photo_path ? asset('storage/'.$this->photo_path) : null;
    }

    public function permis()
    {
        return $this->hasOne(Permis::class);
    }

    protected static function booted()
    {
        static::creating(function ($conducteur) {
            if (! $conducteur->identification_number) {
                $prefix = 'CON';
                $year = date('Y');
                $latest = static::whereYear('created_at', $year)
                    ->latest()
                    ->first();

                $sequence = $latest ? ((int) substr($latest->identification_number, -5)) + 1 : 1;
                $conducteur->identification_number = $prefix.'-'.$year.'-'.str_pad($sequence, 5, '0', STR_PAD_LEFT);
            }
        });

        static::created(function ($conducteur) {
            // Create a default driving license for the driver
            $licenseNumber = $conducteur->license_number ?? ('PER-'.strtoupper(Str::random(8)));

            $conducteur->permis()->create([
                'license_number' => $licenseNumber,
                'category' => $conducteur->license_category ?? 'B',
                'issued_at' => $conducteur->license_issued_at ?? now(),
                'expires_at' => $conducteur->license_expires_at ?? now()->addYears(5),
                'status' => 'Valide',
            ]);
        });
    }
}
