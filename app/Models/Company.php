<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name',
        'trade_name',
        'nit',
        'dv',
        'regime_type',
        'responsibility_codes',
        'email',
        'phone',
        'website',
        'address',
        'city',
        'city_code',
        'department',
        'department_code',
        'country',
        'postal_code',
        'economic_activity_code',
        'economic_activity_description',
        'merchant_registration',
        'merchant_registration_date',
        'logo_path',
        'invoice_footer_note',
        'is_active',
    ];

    protected $casts = [
        'responsibility_codes' => 'array',
        'merchant_registration_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the active company (assuming single company setup)
     */
    public static function getActive()
    {
        return self::where('is_active', true)->first();
    }

    /**
     * Get full NIT with verification digit
     */
    public function getFullNitAttribute()
    {
        return $this->nit . '-' . $this->dv;
    }

    /**
     * Get full address formatted
     */
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->department,
            $this->country,
        ]);
        
        return implode(', ', $parts);
    }
}
