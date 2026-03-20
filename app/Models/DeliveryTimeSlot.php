<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryTimeSlot extends Model
{
    protected $fillable = ['nome', 'orario_inizio', 'orario_fine', 'attivo', 'ordine'];

    protected $casts = ['attivo' => 'boolean'];

    public function getLabelAttribute(): string
    {
        return "{$this->nome} ({$this->orario_inizio} - {$this->orario_fine})";
    }

    public function scopeAttivi($query)
    {
        return $query->where('attivo', true)->orderBy('ordine');
    }
}
