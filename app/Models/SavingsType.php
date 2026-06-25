<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavingsType extends Model
{
    /** @use HasFactory<\Database\Factories\SavingsTypeFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'is_mandatory',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_mandatory' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function savings()
    {
        return $this->hasMany(Savings::class);
    }
}
