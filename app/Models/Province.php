<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'postal_code',
        'contacts_email',
        'contacts_phone',
        'language_lingala',
        'language_kikongo',
        'language_kiluba',
        'language_kiswahili',
        'optional_languages',
        'creation_date',
    ];

    protected $casts = [
        'creation_date' => 'date',
        'language_lingala' => 'boolean',
        'language_kikongo' => 'boolean',
        'language_kiluba' => 'boolean',
        'language_kiswahili' => 'boolean',
        'optional_languages' => 'array',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
