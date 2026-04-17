<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\LogsAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, LogsAudit;

    /**
     * Fields excluded from audit logs (sensitive).
     */
    public array $auditOmit = [
        'password', 'remember_token', 'failed_login_attempts',
        'locked_until', 'last_login_at', 'last_login_ip',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'current_institution_id',
        'must_change_password',
        'failed_login_attempts',
        'locked_until',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
            'locked_until' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(
            \App\Modules\Settings\Models\Role::class,
            'user_roles'
        )->withPivot('institution_id');
    }

    public function currentInstitution()
    {
        return $this->belongsTo(\App\Modules\Settings\Models\Institution::class, 'current_institution_id');
    }

    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    public function student()
    {
        return $this->hasOne(\App\Modules\Student\Models\Student::class);
    }

    public function staff()
    {
        return $this->hasOne(\App\Modules\Staff\Models\Staff::class);
    }
}
