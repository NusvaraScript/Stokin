<?php

namespace App\Models;

use Database\Factories\TransactionPhotoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionPhoto extends Model
{
    /** @use HasFactory<TransactionPhotoFactory> */
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'image',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
