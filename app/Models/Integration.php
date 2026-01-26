<?php

namespace App\Models;

use App\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Integration extends Model
{
    use HasFactory, BelongsToClinic;

    protected $fillable = [
        'clinic_id',
        'provider',
        'credentials',
        'is_active',
        'last_verified_at',
    ];

    protected $casts = [
        'credentials' => 'encrypted:array',
        'is_active' => 'boolean',
        'last_verified_at' => 'datetime',
    ];

    protected $hidden = [
        'credentials',
    ];

    public function phoneNumbers(): HasMany
    {
        return $this->hasMany(ClinicPhoneNumber::class);
    }
}
