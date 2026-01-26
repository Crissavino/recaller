<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'is_active',
        'terms_accepted_at',
        'notification_preferences',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'terms_accepted_at' => 'datetime',
            'notification_preferences' => 'array',
        ];
    }

    /**
     * Default notification preferences.
     */
    public static function defaultNotificationPreferences(): array
    {
        return [
            'email_new_lead' => true,
            'email_lead_responded' => true,
            'email_daily_summary' => false,
        ];
    }

    /**
     * Get notification preferences with defaults.
     */
    public function getNotificationPreferences(): array
    {
        return array_merge(
            self::defaultNotificationPreferences(),
            $this->notification_preferences ?? []
        );
    }

    /**
     * Check if a specific notification is enabled.
     */
    public function wantsNotification(string $type): bool
    {
        $prefs = $this->getNotificationPreferences();
        return $prefs[$type] ?? true;
    }

    public function clinics(): BelongsToMany
    {
        return $this->belongsToMany(Clinic::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function assignedLeads(): HasMany
    {
        return $this->hasMany(Lead::class, 'assigned_to');
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sent_by_user_id');
    }

    public function resolvedOutcomes(): HasMany
    {
        return $this->hasMany(MissedCallOutcome::class, 'resolved_by_user_id');
    }

    public function roleInClinic(Clinic $clinic): ?UserRole
    {
        $pivot = $this->clinics()->where('clinic_id', $clinic->id)->first()?->pivot;

        return $pivot ? UserRole::from($pivot->role) : null;
    }

    public function belongsToClinic(int $clinicId): bool
    {
        return $this->clinics()->where('clinic_id', $clinicId)->exists();
    }
}
