<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderRow extends Model
{

protected $table = 'order_rows';

protected $fillable = [
'order_id',
'product_id',
'boxes',
'price_per_kg',
'total'
];

public function product()
{
return $this->belongsTo(Product::class);
}

public function order()
{
return $this->belongsTo(Order::class);
}

}