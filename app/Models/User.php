<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'address',
        'phone',
        'identification_number',
        'is_admin',
        'language',
        'is_active',
        'membership_expires_at',
        'membership_type',
        'last_payment_date',
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
            'is_admin' => 'boolean',
            'is_active' => 'boolean',
            'membership_expires_at' => 'date',
            'last_payment_date' => 'date',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    /**
     * Get the user's dependents
     */
    public function dependents()
    {
        return $this->hasMany(Dependent::class);
    }

    /**
     * Check if the user's membership is active
     */
    public function isActive(): bool
    {
        return $this->is_active && $this->membership_expires_at && $this->membership_expires_at->isFuture();
    }

    /**
     * Check if the user's membership has expired
     */
    public function isMembershipExpired(): bool
    {
        return $this->membership_expires_at && $this->membership_expires_at->isPast();
    }

    /**
     * Calculate the amount to pay based on user status
     */
    public function calculatePaymentAmount(): float
    {
        // If new registration or expired membership - RM 50
        if (!$this->is_active || $this->isMembershipExpired()) {
            return config('khairat.registration_fee', 50);
        }

        // If renewal - RM 40
        return config('khairat.renewal_fee', 40);
    }

    /**
     * Get payment type based on user status
     */
    public function getPaymentType(): string
    {
        if (!$this->is_active || $this->isMembershipExpired()) {
            return 'registration';
        }

        return 'renewal';
    }

    /**
     * Activate membership after successful payment
     */
    public function activateMembership(string $paymentType): void
    {
        $this->is_active = true;
        $this->last_payment_date = now();

        // Set expiry date - 1 year from today or from current expiry if renewal
        if ($paymentType === 'renewal' && $this->membership_expires_at && $this->membership_expires_at->isFuture()) {
            $this->membership_expires_at = $this->membership_expires_at->addYear();
        } else {
            $this->membership_expires_at = now()->addYear();
        }

        $this->save();
    }
}
