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
        'payment_terms'
    ];

    public function documents()
    {
        return $this->hasMany(Document::class);
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
}