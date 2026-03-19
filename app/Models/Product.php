<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $fillable = [
        'name',
        'origin',
        'unit',
        'tara',
        'avg_box_weight',
        'avg_unit_weight',
        'pieces_per_box',
        'price',
        'cost_price',
        'vat_rate',
        'sale_type',
        'min_margin',
        'category',
        'disponibilita',
        'ordine_step',
        'ordine_min',
    ];

    public function stock()
    {
        return $this->hasOne(Stock::class);
    }

}