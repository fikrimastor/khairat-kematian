<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use function Pest\Laravel\get;

class Receipt extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'payment_id',
        'receipt_number',
        'receipt_path',
        'generated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'generated_at' => 'datetime',
    ];

    /**
     * Get the payment that owns the receipt.
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Get the user associated with this receipt through payment.
     */
    public function user()
    {
        return $this->payment->user;
    }

    /**
     * Generate a unique receipt number.
     */
    public static function generateReceiptNumber(): string
    {
        $prefix = 'RESIT';
        $date = now()->format('Ymd');
        $random = mt_rand(1000, 9999);

        return "{$prefix}-{$date}-{$random}";
    }

    /**
     * Get the full download URL for the receipt.
     */
    public function downloadUrl(): Attribute
    {
        return Attribute::get(get: function () {
            if (!$this->receipt_path) {
                return null;
            }

            return url("storage/{$this->receipt_path}");
        });
    }

    /**
     * Get a formatted date for the receipt.
     */
    public function formattedDate(): Attribute
    {
        return Attribute::get(get: fn () => $this->generated_at->format('d M Y'));
    }
}
