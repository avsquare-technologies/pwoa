<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Spatie\Permission\Traits\HasRoles;
use App\Models\QuizResult;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Quiz;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use Billable, HasFactory, HasPanelShield, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'last_login_at',
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

    public function quizResults()
{
    return $this->hasMany(QuizResult::class);
}

public function quiz()
{
    return $this->belongsTo(Quiz::class);
}
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
            'last_login_at' => 'datetime',
        ];
    }

    public function detail(): HasOne
    {
        return $this->hasOne(UserDetail::class);
    }

    public function membershipStatus(): HasOne
    {
        return $this->hasOne(MembershipStatus::class);
    }

    /**
     * Get the business listing owned by the user.
     */
    public function business(): HasOne
    {
        return $this->hasOne(Business::class);
    }

    /**
     * Get the businesses managed by the user.
     */
    public function businesses(): HasMany
    {
        return $this->hasMany(Business::class);
    }

    /**
     * Check if user has a registered business.
     */
    public function hasBusiness(): bool
    {
        return $this->businesses()->exists();
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function isActiveMember(): bool
    {
        return $this->subscribed('default') || ($this->membershipStatus?->is_active ?? false);
    }

    /**
     * Determine if the user has premium access based on active membership and token balance.
     */
    public function hasPremiumAccess(): bool
    {
        return $this->isActiveMember() && app(\App\Services\WashBalanceService::class)->hasRequiredBalance($this);
    }

    public function membershipOnGracePeriod(): bool
    {
        // Null safe operator
        return $this->subscription('default')?->onGracePeriod() ?? false;
    }

    /**
     * Courses this user has enrolled in.
     */
    public function enrolledCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class)
            ->withPivot(['joined_at', 'completed_at'])
            ->withTimestamps();
    }

    public function completedLessons(): BelongsToMany
    {
        return $this->belongsToMany(Lesson::class)
            ->withPivot('completed_at')
            ->withTimestamps();
    }

    /**
     * Certificates earned by this user.
     */
    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    public function tokenTransactions(): HasMany
    {
        return $this->hasMany(TokenTransaction::class);
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(EventAttendee::class);
    }

    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return $this->hasRole('super_admin') || $this->hasRole('admin');
    }

    public function ticketOrders(): \Illuminate\Database\Eloquent\Relations\HasMany 
    {
        return $this->hasMany(TicketOrder::class);
    }
}
