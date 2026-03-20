<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientProductOverride extends Model
{
    protected $fillable = [
        'client_id', 'product_id', 'prezzo_override',
        'min_override', 'max_override', 'modalita_override',
        'bloccato', 'note',
    ];

    protected $casts = [
        'prezzo_override' => 'decimal:2',
        'min_override'    => 'decimal:3',
        'max_override'    => 'decimal:3',
        'bloccato'        => 'boolean',
    ];

    public function client()  { return $this->belongsTo(Client::class); }
    public function product() { return $this->belongsTo(Product::class); }
}
