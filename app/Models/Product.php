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
        'avg_box_weight',     // peso medio cassa
        'avg_unit_weight',    // peso medio pezzo
        'pieces_per_box',     // pezzi per cassa
        'price',
        'cost',
        'vat_rate',
        'sale_type'
    ];

    public function stock()
    {
        return $this->hasOne(Stock::class);
    }

}