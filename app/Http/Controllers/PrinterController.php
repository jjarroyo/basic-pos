<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Setting;
use Illuminate\Http\Request;

class PrinterController extends Controller
{
    public function ticket(Sale $sale)
    { 
        $sale->load(['details.product', 'user', 'client']);
 
        $company = [
            'name' => Setting::get('company_name', 'Nexus POS'),
            'nit' => Setting::get('company_nit', '000000000'),
            'address' => Setting::get('company_address', 'Dirección Principal'),
            'phone' => Setting::get('company_phone', '0000000'),
            'footer' => Setting::get('ticket_footer', 'Gracias por su compra'),
        ];

        return view('print.ticket', compact('sale', 'company'));
    }
}