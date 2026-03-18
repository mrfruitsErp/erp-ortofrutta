<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'origin',

        'colli',
        'peso_collo',

        'kg_estimated',
        'kg_real',
        'tara',
        'kg_net',

        'qty',        // pezzi reali
        'price_kg',
        'price',
        'total',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // 🔹 CALCOLO PESO NETTO SICURO
    public function getCalculatedKgNetAttribute()
    {
        return max(0, ($this->kg_real ?? 0) - ($this->tara ?? 0));
    }
}