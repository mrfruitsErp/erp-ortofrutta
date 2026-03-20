<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'category', 'sku', 'origin', 'vat_rate',
        'modalita_vendita', 'step_grammi',
        'price', 'cost_price',
        'pieces_per_box', 'avg_box_weight', 'tara',
        'disponibilita', 'ordine_min',
        'ordine_min_kg', 'ordine_max',  // NEW
    ];

    protected $casts = [
        'price'          => 'decimal:2',
        'cost_price'     => 'decimal:2',
        'avg_box_weight' => 'decimal:3',
        'tara'           => 'decimal:3',
        'ordine_min'     => 'decimal:3',
        'ordine_min_kg'  => 'decimal:3',
        'ordine_max'     => 'decimal:3',
        'step_grammi'    => 'integer',
        'pieces_per_box' => 'integer',
        'vat_rate'       => 'integer',
    ];

    public function getUnitaPrezzoAttribute(): string
    {
        return match ($this->modalita_vendita) {
            'cassa_collo' => '€/collo',
            'pezzo'       => '€/pz',
            default       => '€/kg',
        };
    }

    public function getUnitaOrdineAttribute(): string
    {
        return match ($this->modalita_vendita) {
            'cassa_kg', 'cassa_collo' => 'casse',
            'kg_liberi'               => 'kg',
            'pezzo'                   => 'pz',
            'peso_step'               => 'g',
            default                   => 'pz',
        };
    }

    public function getUnitaStockAttribute(): string
    {
        return match ($this->modalita_vendita) {
            'cassa_kg', 'cassa_collo' => 'casse',
            'kg_liberi', 'peso_step'  => 'kg',
            'pezzo'                   => 'pz',
            default                   => 'pz',
        };
    }

    public function getModalitaLabelAttribute(): string
    {
        return match ($this->modalita_vendita) {
            'cassa_kg'    => 'A cassa (prezzo al kg)',
            'cassa_collo' => 'A cassa (prezzo fisso per collo)',
            'kg_liberi'   => 'A kg liberi',
            'pezzo'       => 'A pezzo / mazzo',
            'peso_step'   => 'A peso con step (' . ($this->step_grammi ?? 100) . 'g)',
            default       => 'Non definita',
        };
    }

    public function getSupportaKgFlessibileAttribute(): bool
    {
        return $this->modalita_vendita === 'cassa_kg';
    }

    public function calcolaStima(float $qty, string $modalitaOverride = null): float
    {
        $mode = $modalitaOverride ?? $this->modalita_vendita;
        return match ($mode) {
            'cassa_kg'      => $qty * ($this->avg_box_weight ?? 0) * $this->price,
            'cassa_collo'   => $qty * $this->price,
            'kg_liberi'     => $qty * $this->price,
            'pezzo'         => $qty * $this->price,
            'peso_step'     => ($qty / 1000) * $this->price,
            'kg_flessibile' => $qty * $this->price,
            default         => 0,
        };
    }

    public function getStimaIndicativaAttribute(): bool
    {
        return $this->modalita_vendita === 'cassa_kg';
    }

    public function getPesoPerPezzoAttribute(): ?float
    {
        if ($this->avg_box_weight > 0 && $this->pieces_per_box > 0) {
            return round($this->avg_box_weight / $this->pieces_per_box, 3);
        }
        return null;
    }

    public function priceListItems()
    {
        return $this->hasMany(\App\Models\PriceListItem::class);
    }

    public function scopeDisponibili($query)
    {
        return $query->where('disponibilita', '!=', 'non_disponibile');
    }

    public function scopeOrdinabili($query)
    {
        return $query->whereIn('disponibilita', ['disponibile', 'su_richiesta']);
    }
}
