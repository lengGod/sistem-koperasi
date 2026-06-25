<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Savings extends Model
{
    /** @use HasFactory<\Database\Factories\SavingsFactory> */
    use HasFactory;

    protected $fillable = [
        'member_id',
        'savings_type_id',
        'transaction_type',
        'amount',
        'transaction_date',
        'notes',
        'reference_number',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'transaction_date' => 'date',
        ];
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function savingsType()
    {
        return $this->belongsTo(SavingsType::class);
    }
}
