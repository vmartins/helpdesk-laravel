<?php

namespace App\Models;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Translation\HasLocalePreference;
use DutchCodingCompany\FilamentSocialite\Models\SocialiteUser;
use Althinect\FilamentSpatieRolesPermissions\Concerns\HasSuperAdmin;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail, HasLocalePreference
{
    use SoftDeletes;
    use HasRoles;
    use HasSuperAdmin;
    use HasFactory;
    use Notifiable;
    use LogsActivity;

    protected $casts = [
        'email_verified_at' => 'datetime',
        'two_factor_confirmed_at' => 'datetime',
        'is_active' => 'bool',
    ];

    protected $hidden = [
        'password',
        'two_factor_secret',
        'remember_token',
    ];

    protected $fillable = [
        'unit_id',
        'name',
        'email',
        'email_verified_at',
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'remember_token',
        'identity',
        'phone',
        'user_level_id',
        'is_active',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                '*',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the user's preferred locale.
     */
    public function preferredLocale(): string
    {
        return app(\App\Settings\GeneralSettings::class)->site_locale;
    }

    /**
     * Get the unit that owns the User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get all of the comments for the User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get all of the tickets for the User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'owner_id');
    }

    /**
     * Get all of the ticekt responsibility for the User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ticektResponsibility()
    {
        return $this->hasMany(Ticket::class, 'responsible_id');
    }

    /**
     * Determine who has access.
     *
     * Only active users can access the filament
     */
    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return auth()->user()->is_active;
    }

    /**
     * Add scope to display users based on their role.
     *
     * If the role is as an admin unit, then display the user based on their unit ID.
     */
    public function scopeByRole($query)
    {
        if (auth()->user()->hasRole('Admin Unit')) {
            return $query->where('users.unit_id', auth()->user()->unit_id);
        }
    }

    /**
     * Get all of the socialiteUsers for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function socialiteUsers()
    {
        return $this->hasMany(SocialiteUser::class);
    }
}
