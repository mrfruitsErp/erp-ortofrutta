<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Supplier;
use App\Models\Product;

class Purchase extends Model
{

    protected $fillable = [
        'supplier_id',
        'product_id',
        'kg',
        'price',
        'total',
        'date'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}