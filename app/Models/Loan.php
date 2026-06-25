<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    /** @use HasFactory<\Database\Factories\LoanFactory> */
    use HasFactory;

    protected $fillable = [
        'member_id',
        'loan_number',
        'principal_amount',
        'interest_rate',
        'term_months',
        'monthly_installment',
        'remaining_balance',
        'disbursed_at',
        'due_date',
        'status',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'principal_amount' => 'decimal:2',
            'interest_rate' => 'decimal:2',
            'monthly_installment' => 'decimal:2',
            'remaining_balance' => 'decimal:2',
            'disbursed_at' => 'date',
            'due_date' => 'date',
        ];
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function installments()
    {
        return $this->hasMany(Installment::class);
    }
}
