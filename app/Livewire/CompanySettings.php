<?php

namespace App\Livewire;

use Livewire\Component;

class CompanySettings extends Component
{
    public $company;
    
    // Información básica
    public $name;
    public $trade_name;
    public $nit;
    public $dv;
    
    // Régimen y responsabilidades
    public $regime_type;
    public $responsibility_codes = [];
    
    // Contacto
    public $email;
    public $phone;
    public $website;
    
    // Dirección
    public $address;
    public $city;
    public $city_code;
    public $department;
    public $department_code;
    public $country;
    public $postal_code;
    
    // Información comercial
    public $economic_activity_code;
    public $economic_activity_description;
    public $merchant_registration;
    public $merchant_registration_date;
    
    // Otros
    public $invoice_footer_note;
    public $logo_path;

    protected $rules = [
        'name' => 'required|string|max:255',
        'nit' => 'required|string|max:20',
        'dv' => 'required|string|size:1',
        'regime_type' => 'required|in:comun,simplificado',
        'email' => 'required|email',
        'address' => 'required|string',
        'city' => 'required|string',
        'department' => 'required|string',
        'country' => 'required|string|size:2',
    ];

    public function mount()
    {
        $this->company = \App\Models\Company::getActive() ?? new \App\Models\Company();
        
        if ($this->company->exists) {
            $this->fill([
                'name' => $this->company->name,
                'trade_name' => $this->company->trade_name,
                'nit' => $this->company->nit,
                'dv' => $this->company->dv,
                'regime_type' => $this->company->regime_type,
                'responsibility_codes' => $this->company->responsibility_codes ?? [],
                'email' => $this->company->email,
                'phone' => $this->company->phone,
                'website' => $this->company->website,
                'address' => $this->company->address,
                'city' => $this->company->city,
                'city_code' => $this->company->city_code,
                'department' => $this->company->department,
                'department_code' => $this->company->department_code,
                'country' => $this->company->country,
                'postal_code' => $this->company->postal_code,
                'economic_activity_code' => $this->company->economic_activity_code,
                'economic_activity_description' => $this->company->economic_activity_description,
                'merchant_registration' => $this->company->merchant_registration,
                'merchant_registration_date' => $this->company->merchant_registration_date?->format('Y-m-d'),
                'invoice_footer_note' => $this->company->invoice_footer_note,
                'logo_path' => $this->company->logo_path,
            ]);
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'trade_name' => $this->trade_name,
            'nit' => $this->nit,
            'dv' => $this->dv,
            'regime_type' => $this->regime_type,
            'responsibility_codes' => $this->responsibility_codes,
            'email' => $this->email,
            'phone' => $this->phone,
            'website' => $this->website,
            'address' => $this->address,
            'city' => $this->city,
            'city_code' => $this->city_code,
            'department' => $this->department,
            'department_code' => $this->department_code,
            'country' => $this->country,
            'postal_code' => $this->postal_code,
            'economic_activity_code' => $this->economic_activity_code,
            'economic_activity_description' => $this->economic_activity_description,
            'merchant_registration' => $this->merchant_registration,
            'merchant_registration_date' => $this->merchant_registration_date,
            'invoice_footer_note' => $this->invoice_footer_note,
            'is_active' => true,
        ];

        if ($this->company->exists) {
            $this->company->update($data);
        } else {
            $this->company = \App\Models\Company::create($data);
        }

        session()->flash('message', 'Información de la empresa guardada exitosamente.');
    }

    public function render()
    {
        return view('livewire.company-settings');
    }
}
