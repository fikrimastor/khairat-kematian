<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentProof extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'payment_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'notes',
    ];

    /**
     * Get the payment that owns the proof.
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Get the full URL for the file.
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/'.$this->file_path);
    }
}
