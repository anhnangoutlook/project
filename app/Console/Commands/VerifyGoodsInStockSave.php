<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VerifyGoodsInStockSave extends Command
{
    protected $signature = 'verify:goods-in-stock-save';
    protected $description = 'Verify that goods-in-stock insert also writes to stock table';

    public function handle(): int
    {
        $payload = [
            'code' => 'AUTO-VERIFY',
            'name' => 'AUTO VERIFY',
            'weight' => '1kg',
            'qty1' => 5,
            'imported_at' => now()->toDateTimeString(),
        ];

        $request = new \Illuminate\Http\Request();
        $request->merge($payload);

        $controller = new \App\Http\Controllers\StockController();
        $response = $controller->goodsInStockStore($request);

        $this->info('status=' . $response->getStatusCode());
        $this->info($response->getContent());

        $exists = DB::table('stock')->where('code', 'AUTO-VERIFY')->exists();
        $this->info('stock_exists=' . ($exists ? 'yes' : 'no'));

        return 0;
    }
}
