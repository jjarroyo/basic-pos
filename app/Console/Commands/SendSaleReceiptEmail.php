<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sale;
use App\Mail\SaleReceiptMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;

class SendSaleReceiptEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-sale-receipt {sale_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send sale receipt email for a specific sale';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $saleId = $this->argument('sale_id');
        Log::info("Sending sale receipt email for sale #{$saleId}");
        try {
            $sale = Sale::with(['details.product', 'client', 'user'])->findOrFail($saleId);
            $recipient = Setting::get('email_notifications_recipient');
            
            if (!$recipient) {
                Log::warning("No email recipient configured for sale #{$saleId}");
                return 1;
            }

            Mail::to($recipient)->send(new SaleReceiptMail($sale));
            
            Log::info("Sale receipt email sent successfully for sale #{$saleId} to {$recipient}");
            return 0;
            
        } catch (\Exception $e) {
            Log::error("Failed to send sale receipt email for sale #{$saleId}: " . $e->getMessage());
            return 1;
        }
    }
}
