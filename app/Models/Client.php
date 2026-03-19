<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'company_name',
        'vat_number',
        'fiscal_code',
        'address',
        'city',
        'zip',
        'province',
        'email',
        'phone',
        'payment_terms',
        'referente',
        'cellulare_referente',
        'zona_consegna',
        'giorni_consegna',
        'giorni_chiusura',
        'fascia_oraria_inizio',
        'fascia_oraria_fine',
        'fido',
        'note_interne',
        'stato',
        'order_token',
        'modalita_ordine',
    ];

    protected $casts = [
        'giorni_consegna' => 'array',
        'giorni_chiusura' => 'array',
        'fido'            => 'decimal:2',
    ];

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getTotalDocumentsAttribute()
    {
        return $this->documents->sum('total_calculated');
    }

    public function getTotalPaidAttribute()
    {
        return $this->payments->sum('amount');
    }

    public function getBalanceAttribute()
    {
        return $this->total_documents - $this->total_paid;
    }

    // Genera token univoco se non esiste
    public static function generateToken(): string
    {
        do {
            $token = bin2hex(random_bytes(16));
        } while (self::where('order_token', $token)->exists());

        return $token;
    }
}