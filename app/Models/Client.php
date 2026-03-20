<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        // Existing fields
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
        // NEW ERP fields
        'price_list_id',
        'payment_method_id',
        'puo_ordinare_kg',
        'orario_limite_ordine',
        'iban',
        'banca',
    ];

    protected $casts = [
        'giorni_consegna'  => 'array',
        'giorni_chiusura'  => 'array',
        'fido'             => 'decimal:2',
        'puo_ordinare_kg'  => 'boolean',
    ];

    // ── RELAZIONI ──

    public function priceList()
    {
        return $this->belongsTo(PriceList::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function deliveryPrefs()
    {
        return $this->hasMany(ClientDeliveryPref::class);
    }

    public function productOverrides()
    {
        return $this->hasMany(ClientProductOverride::class);
    }

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

    // ── HELPERS ──

    /**
     * Il metodo di pagamento effettivo: override cliente > default listino > payment_terms legacy
     */
    public function getEffectivePaymentMethodAttribute(): ?PaymentMethod
    {
        if ($this->payment_method_id) {
            return $this->paymentMethod;
        }
        if ($this->priceList && $this->priceList->payment_method_id) {
            return $this->priceList->defaultPaymentMethod;
        }
        return null;
    }

    /**
     * Può ordinare a kg? Override cliente > regola listino > false
     */
    public function getPuoOrdinarKgEffectivoAttribute(): bool
    {
        if ($this->puo_ordinare_kg !== null) {
            return (bool) $this->puo_ordinare_kg;
        }
        if ($this->priceList) {
            return (bool) $this->priceList->puo_ordinare_kg;
        }
        return false;
    }

    /**
     * Orario limite ordine: override cliente > setting globale
     */
    public function getOrarioLimiteEffectivoAttribute(): string
    {
        if ($this->orario_limite_ordine) {
            return $this->orario_limite_ordine;
        }
        return \DB::table('settings')->where('key', 'orario_limite_ordine_default')->value('value') ?? '21:00';
    }

    // ── ATTRIBUTI CALCOLATI (già esistenti) ──

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

    public static function generateToken(): string
    {
        do {
            $token = bin2hex(random_bytes(16));
        } while (self::where('order_token', $token)->exists());
        return $token;
    }
}
