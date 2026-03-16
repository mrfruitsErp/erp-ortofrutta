<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentRow extends Model
{

protected $table = 'document_rows';

protected $fillable = [
'document_id',
'product_id',
'boxes',
'kg_estimated',
'kg_real',
'price_per_kg',
'vat_rate',
'total'
];

public function product()
{
return $this->belongsTo(Product::class);
}

public function document()
{
return $this->belongsTo(Document::class);
}

}