<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'payment_type',
        'reference_no',
        'status',
        'payment_date',
        'verified_by',
        'verified_at',
        'month',
        'year',
        'household_count',
        'notes',
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
        'payment_type' => PaymentType::class,
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
     * Get the payment proofs associated with the payment.
     */
    public function proofs(): HasMany
    {
        return $this->hasMany(PaymentProof::class);
    }

    /**
     * Scope a query to filter payments by month.
     *
     * @param  mixed  $query
     */
    public function scopeMonth($query, string $month)
    {
        return $query->where('month', $month);
    }

    /**
     * Scope a query to filter payments by year.
     *
     * @param  mixed  $query
     */
    public function scopeYear($query, int $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Scope a query to filter payments by status.
     *
     * @param  mixed  $query
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
        return $this->status === PaymentStatus::VERIFIED && $this->verified_at !== null;
    }

    /**
     * Check if the payment is pending verification.
     */
    public function isPending(): bool
    {
        return $this->status === PaymentStatus::PENDING;
    }

    /**
     * Get the period (month and year) as a formatted string.
     */
    public function period(): Attribute
    {
        return Attribute::get(get: fn () => $this->month.' '.$this->year);
    }
}
