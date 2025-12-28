<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\ReturnModel;
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

        // check if we should open the cash drawer
        $openDrawer = false;
        $drawerCommand = '';
        
        if ($sale->payment_method === 'cash') {
            $enableDrawer = Setting::get('enable_cash_drawer', false);
            if ($enableDrawer === '1' || $enableDrawer === true) {
                $openDrawer = true;
                $drawerCommand = Setting::get('cash_drawer_command', '\x1B\x70\x00\x19\xFA');
            }
        }

        return view('print.ticket', compact('sale', 'company', 'openDrawer', 'drawerCommand'));
    }

    public function returnReceipt(ReturnModel $return)
    {
        $return->load(['sale.client', 'user', 'details.product', 'details.exchangeProduct']);

        $company = [
            'name' => Setting::get('company_name', 'Nexus POS'),
            'nit' => Setting::get('company_nit', '000000000'),
            'address' => Setting::get('company_address', 'Dirección Principal'),
            'phone' => Setting::get('company_phone', '0000000'),
        ];

        return view('print.return-receipt', compact('return', 'company'));
    }
}