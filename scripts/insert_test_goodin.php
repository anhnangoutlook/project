<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\GoodInStock;
use App\Models\Stock;

try {
    $data = [
        'code' => 'TST001',
        'name' => 'Test Product',
        'weight' => '100g',
        'qty1' => 5,
        'imported_at' => now()->toDateTimeString(),
    ];

    $good = GoodInStock::create($data);
    $stock = Stock::create($data);

    echo "Inserted GoodInStock id: " . ($good->id ?? 'null') . "\n";
    echo "Inserted Stock id: " . ($stock->id ?? 'null') . "\n";

    echo "Counts after insert:\n";
    echo "good_in_stock: " . GoodInStock::count() . "\n";
    echo "stocks: " . Stock::count() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
