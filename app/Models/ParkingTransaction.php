<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkingTransaction extends Model
{
    protected $fillable = [
        'vehicle_type_id', 'license_plate', 'entry_time', 'exit_time', 'total_fee', 'status'
    ];

    protected $casts = [
        'entry_time' => 'datetime',
        'exit_time' => 'datetime',
    ];

    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }
}
