<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryZone extends Model
{

protected $fillable = [
'name',
'cap_start',
'cap_end',
'active'
];

public function slots()
{
return $this->belongsToMany(
DeliverySlot::class,
'delivery_zone_slots',
'zone_id',
'slot_id'
);
}

/*
|--------------------------------------------------------------------------
| TROVA ZONA DA CAP
|--------------------------------------------------------------------------
*/

public static function findByCap($cap)
{

return self::where('cap_start','<=',$cap)
->where('cap_end','>=',$cap)
->where('active',1)
->first();

}

}
