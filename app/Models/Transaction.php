<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    protected $fillable = [
        'customer_id',
        'transaction_id',
        'total',
        'status',
        'amount_paid',
        'remaining_debt',
        'note',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'status' => 'string',
        'amount_paid' => 'decimal:2',
        'remaining_debt' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function parentTransaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function childTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'transaction_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(TransactionPhoto::class);
    }

    public function debtPayments(): HasMany
    {
        return $this->hasMany(DebtPayment::class);
    }
}
