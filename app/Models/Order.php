<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'client_id',
        'number',
        'date',
        'delivery_date',
        'delivery_slot',
        'notes',
        'total',
        'status',
    ];

    protected $attributes = [
        'status' => 'draft',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}