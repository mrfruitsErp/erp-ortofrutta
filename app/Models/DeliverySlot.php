<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliverySlot extends Model
{

protected $fillable = [
'name',
'start_time',
'end_time',
'active'
];

public function zones()
{
return $this->belongsToMany(
DeliveryZone::class,
'delivery_zone_slots',
'slot_id',
'zone_id'
);
}

}
