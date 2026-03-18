<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'order_id',
        'client_id',
        'type',
        'number',
        'date',
        'total',
        'vat_total',
        'status',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function rows()
    {
        return $this->hasMany(DocumentRow::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getTotalCalculatedAttribute()
    {
        return $this->rows->sum('total');
    }

    public function getVatCalculatedAttribute()
    {
        return $this->rows->sum(function ($row) {
            return $row->total * ($row->vat_rate / 100);
        });
    }

    public function getPaidAttribute()
    {
        return $this->payments->sum('amount');
    }

    public function getBalanceAttribute()
    {
        return $this->total_calculated - $this->paid;
    }
}