<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PaymentMethod extends Model
{
    protected $fillable = [
        'nome', 'metodo', 'scadenza', 'giorni_scadenza',
        'fine_mese', 'spese_incasso', 'attivo', 'ordine',
    ];

    protected $casts = [
        'giorni_scadenza' => 'integer',
        'fine_mese'       => 'boolean',
        'spese_incasso'   => 'decimal:2',
        'attivo'          => 'boolean',
    ];

    public function calcolaScadenza(Carbon $dataFattura): Carbon
    {
        if ($this->giorni_scadenza === 0) {
            return $dataFattura->copy();
        }
        if ($this->fine_mese) {
            return $dataFattura->copy()->endOfMonth()->addDays($this->giorni_scadenza);
        }
        return $dataFattura->copy()->addDays($this->giorni_scadenza);
    }

    public function getMetodoLabelAttribute(): string
    {
        return match ($this->metodo) {
            'contanti'      => 'Contanti',
            'bonifico'      => 'Bonifico bancario',
            'riba'          => 'Ri.Ba.',
            'riba_sbf'      => 'Ri.Ba. SBF',
            'assegno'       => 'Assegno',
            'sdd'           => 'SDD / Addebito diretto',
            'carta'         => 'Carta',
            'compensazione' => 'Compensazione',
            default         => $this->metodo,
        };
    }

    public function scopeAttivi($query)
    {
        return $query->where('attivo', true)->orderBy('ordine');
    }
}
