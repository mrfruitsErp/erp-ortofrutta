<?php
// FILE: app/Models/PriceListItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceListItem extends Model
{
    protected $fillable = [
        'price_list_id', 'product_id', 'prezzo_override',
        'sconto_pct', 'min_qty', 'max_qty', 'min_qty_kg', 'bloccato',
    ];

    protected $casts = [
        'prezzo_override' => 'decimal:2',
        'sconto_pct'      => 'decimal:2',
        'min_qty'         => 'decimal:3',
        'max_qty'         => 'decimal:3',
        'min_qty_kg'      => 'decimal:3',
        'bloccato'        => 'boolean',
    ];

    public function priceList() { return $this->belongsTo(PriceList::class); }
    public function product()   { return $this->belongsTo(Product::class); }
}
