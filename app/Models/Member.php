<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    /** @use HasFactory<\Database\Factories\MemberFactory> */
    use HasFactory;

    protected $fillable = [
        'member_number',
        'account_number',
        'nik',
        'name',
        'gender',
        'work_unit',
        'birth_place',
        'birth_date',
        'phone',
        'email',
        'address',
        'joined_at',
        'status',
        'employment_status',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'joined_at' => 'date',
        ];
    }

    public function savings()
    {
        return $this->hasMany(Savings::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function installments()
    {
        return $this->hasManyThrough(Installment::class, Loan::class);
    }
}
