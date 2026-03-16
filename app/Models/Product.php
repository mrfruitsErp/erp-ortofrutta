<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

protected $fillable = [
'name',
'origin',
'category',
'unit',
'tara',
'avg_box_weight',
'price',
'cost_price',
'stock',
'vat_rate'
];

public function stock()
{
return $this->hasOne(Stock::class);
}

}