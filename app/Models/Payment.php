<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Enums\PaymentStatus;
use App\Enums\PaymentMethod;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'amount',
        'payment_method',
        'reference_no',
        'status',
        'payment_date',
        'verified_by',
        'verified_at',
        'month',
        'year',
        'household_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'verified_at' => 'datetime',
        'payment_method' => PaymentMethod::class,
        'status' => PaymentStatus::class,
    ];

    /**
     * Get the user that owns the payment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the receipt associated with the payment.
     */
    public function receipt(): HasOne
    {
        return $this->hasOne(Receipt::class);
    }

    /**
     * Get the admin that verified the payment.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Scope a query to filter payments by month.
     */
    public function scopeMonth($query, string $month)
    {
        return $query->where('month', $month);
    }

    /**
     * Scope a query to filter payments by year.
     */
    public function scopeYear($query, int $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Scope a query to filter payments by status.
     */
    public function scopeStatus($query, PaymentStatus $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Check if the payment has been verified.
     */
    public function isVerified(): bool
    {
        return $this->status === PaymentStatus::Verified && $this->verified_at !== null;
    }

    /**
     * Check if the payment is pending verification.
     */
    public function isPending(): bool
    {
        return $this->status === PaymentStatus::Pending;
    }

    /**
     * Get the period (month and year) as a formatted string.
     */
    public function getPeriodAttribute(): string
    {
        return $this->month . ' ' . $this->year;
    }
}
