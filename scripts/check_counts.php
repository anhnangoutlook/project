<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\GoodInStock;
use App\Models\Stock;

try {
    $good = GoodInStock::count();
    $stocks = Stock::count();
    echo "good_in_stock count: $good\n";
    echo "stocks count: $stocks\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
