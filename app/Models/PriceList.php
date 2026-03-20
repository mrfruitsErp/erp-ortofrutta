<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceList extends Model
{
    protected $fillable = [
        'nome', 'codice', 'descrizione', 'sconto_default_pct',
        'puo_ordinare_kg', 'ordine_min_importo', 'payment_method_id',
        'attivo', 'ordine',
    ];

    protected $casts = [
        'sconto_default_pct' => 'decimal:2',
        'puo_ordinare_kg'    => 'boolean',
        'ordine_min_importo' => 'decimal:2',
        'attivo'             => 'boolean',
    ];

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function items()
    {
        return $this->hasMany(PriceListItem::class);
    }

    public function defaultPaymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function getPrezzoPerProdotto(Product $product): float
    {
        $item = $this->items()->where('product_id', $product->id)->first();

        if ($item && $item->bloccato) return 0;

        if ($item && $item->prezzo_override !== null) {
            return (float) $item->prezzo_override;
        }

        if ($item && $item->sconto_pct !== null) {
            return round($product->price * (1 - $item->sconto_pct / 100), 2);
        }

        if ($this->sconto_default_pct > 0) {
            return round($product->price * (1 - $this->sconto_default_pct / 100), 2);
        }

        return (float) $product->price;
    }

    public function scopeAttivi($query)
    {
        return $query->where('attivo', true)->orderBy('ordine');
    }
}
