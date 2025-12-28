<?php

namespace App\Livewire;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\Permission\Models\Role;

class SetupWizard extends Component
{
    use WithFileUploads;

    public $currentStep = 1;

    // Step 1: Company Information
    public $company_name;
    public $company_nit;
    public $company_address;
    public $company_city;
    public $company_phone;
    public $company_email;
    public $company_website;
    public $logo;

    // Step 2: User Creation
    public $seller_count = 0;

    // Step 3: Cash Register Creation
    public $cash_register_name = 'Caja Principal';

    // Step 4: System Configuration
    public $tax_rate = 19;
    public $currency_symbol = '$';
    public $ticket_footer = '¡Gracias por su compra! Vuelva pronto.';

    protected function rules()
    {
        $rules = [];

        if ($this->currentStep === 1) {
            $rules = [
                'company_name' => 'required|string|max:255',
                'company_nit' => 'nullable|string|max:50',
                'company_address' => 'nullable|string|max:255',
                'company_city' => 'nullable|string|max:100',
                'company_phone' => 'nullable|string|max:20',
                'company_email' => 'nullable|email|max:255',
                'company_website' => 'nullable|url|max:255',
                'logo' => 'nullable|image|max:2048',
            ];
        } elseif ($this->currentStep === 2) {
            $rules = [
                'seller_count' => 'required|integer|min:0|max:10',
            ];
        } elseif ($this->currentStep === 3) {
            $rules = [
                'cash_register_name' => 'required|string|max:255',
            ];
        } elseif ($this->currentStep === 4) {
            $rules = [
                'tax_rate' => 'required|numeric|min:0|max:100',
                'currency_symbol' => 'required|string|max:5',
                'ticket_footer' => 'nullable|string|max:500',
            ];
        }

        return $rules;
    }

    public function nextStep()
    {
        $this->validate();

        if ($this->currentStep < 5) {
            $this->currentStep++;
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function completeSetup()
    {
        // Save company information
        Setting::set('company_name', $this->company_name);
        Setting::set('company_nit', $this->company_nit);
        Setting::set('company_address', $this->company_address);
        Setting::set('company_city', $this->company_city);
        Setting::set('company_phone', $this->company_phone);
        Setting::set('company_email', $this->company_email);
        Setting::set('company_website', $this->company_website);

        // Save logo if uploaded
        if ($this->logo) {
            $logoPath = $this->logo->store('logos', 'public');
            Setting::set('company_logo', $logoPath);
        }

        // Save system configuration
        Setting::set('tax_rate', $this->tax_rate);
        Setting::set('currency_symbol', $this->currency_symbol);
        Setting::set('ticket_footer', $this->ticket_footer);

        // Create seller users
        $this->createSellers();

        // Create cash register
        $this->createCashRegister();

        // Mark setup as completed
        Setting::set('setup_completed', true);

        // Redirect to dashboard
        session()->flash('message', 'Configuración inicial completada exitosamente.');
        return redirect()->route('dashboard');
    }

    private function createSellers()
    {
        if ($this->seller_count > 0) {
            // Ensure seller role exists
            $sellerRole = Role::firstOrCreate(['name' => 'seller']);

            for ($i = 1; $i <= $this->seller_count; $i++) {
                $seller = User::create([
                    'name' => "Vendedor $i",
                    'email' => "vendedor$i@nexus.com",
                    'password' => Hash::make('password'),
                    'is_active' => true,
                ]);
                $seller->assignRole($sellerRole);
            }
        }
    }

    private function createCashRegister()
    {
        // Create only one cash register
        \App\Models\CashRegister::create([
            'name' => $this->cash_register_name,
            'is_active' => true,
            'is_open' => false,
        ]);
    }

    public function render()
    {
        return view('livewire.setup-wizard');
    }
}
