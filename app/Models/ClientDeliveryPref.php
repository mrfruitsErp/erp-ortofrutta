<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientDeliveryPref extends Model
{
    protected $fillable = ['client_id', 'delivery_time_slot_id', 'preferito'];

    protected $casts = ['preferito' => 'boolean'];

    public function client() { return $this->belongsTo(Client::class); }
    public function slot()   { return $this->belongsTo(DeliveryTimeSlot::class, 'delivery_time_slot_id'); }
}
