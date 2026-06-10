<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Engin extends Model
{
    use HasFactory;

    protected $fillable = [
        'identification_number',
        'category',
        'sub_category',
        'construction_type',
        'owner_name',
        'owner_gender',
        'owner_birth_place',
        'owner_father_name',
        'owner_mother_name',
        'owner_marital_status',
        'owner_nationality',
        'owner_phone',
        'owner_email',
        'owner_address',
        'vehicle_genre',
        'vehicle_brand',
        'vehicle_type',
        'vehicle_color',
        'plate_number',
        'engine_number',
        'engine_brand',
        'chassis_number',
        'manufacture_year',
        'horsepower',
        'identification_place',
        'identification_date',
        'owner_photo_path',
        'province_id',
        // Maritime fields
        'vessel_name',
        'registration_number',
        'registration_place',
        'registration_date',
        'capacity',
        'navigation_line',
        'height',
        'length',
        'width',
        'propulsion_type',
        'usage',
        'crew_count',
        'driver_count',
    ];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    protected $appends = ['owner_photo_url'];

    public function getOwnerPhotoUrlAttribute()
    {
        return $this->owner_photo_path ? asset('storage/'.$this->owner_photo_path) : null;
    }

    protected $casts = [
        'identification_date' => 'date',
        'registration_date' => 'date',
        'manufacture_year' => 'integer',
        'horsepower' => 'integer',
        'height' => 'float',
        'length' => 'float',
        'width' => 'float',
        'crew_count' => 'integer',
        'driver_count' => 'integer',
        'usage' => 'array',
    ];

    public function certificats()
    {
        return $this->hasMany(Certificat::class);
    }

    protected static function booted()
    {
        static::creating(function ($engin) {
            if (! $engin->identification_number) {
                $prefix = strtoupper(substr($engin->category, 0, 3));
                $year = date('Y');
                $latest = static::whereYear('created_at', $year)
                    ->where('category', $engin->category)
                    ->latest()
                    ->first();

                $sequence = $latest ? ((int) substr($latest->identification_number, -5)) + 1 : 1;
                $engin->identification_number = $prefix.'-'.$year.'-'.str_pad($sequence, 5, '0', STR_PAD_LEFT);
            }
        });

        static::created(function ($engin) {
            // 1. Fiche d'identification
            $engin->certificats()->create([
                'type' => 'Identification',
                'certificate_number' => 'ID-'.strtoupper(Str::random(10)),
                'issued_at' => now(),
                'expires_at' => null, // Permanent identification
                'owner_name' => $engin->owner_name,
                'status' => 'Actif',
            ]);

            // 2. Certificat de possession
            $engin->certificats()->create([
                'type' => 'Possession',
                'certificate_number' => 'POS-'.strtoupper(Str::random(10)),
                'issued_at' => now(),
                'expires_at' => null,
                'owner_name' => $engin->owner_name,
                'status' => 'Actif',
            ]);
        });
    }
}
