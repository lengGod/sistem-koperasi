<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleType extends Model
{
    protected $fillable = ['name', 'hourly_rate'];

    public function transactions()
    {
        return $this->hasMany(ParkingTransaction::class);
    }
}
