<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('products.leaf');
});

Route::get('/leaf-products', [App\Http\Controllers\LeafProductController::class, 'index'])->name('products.leaf');
Route::get('/leaf-products/print-raw', [App\Http\Controllers\LeafProductController::class, 'printRaw'])->name('products.leaf.print-raw');
Route::get('/all-data', [App\Http\Controllers\LeafProductController::class, 'allData'])->name('products.all-data');

Route::get('/old-leaf-new-import', [App\Http\Controllers\StockController::class, 'oldLeafNewImport'])->name('products.old-leaf-new-import');

Route::get('/stock', [App\Http\Controllers\StockController::class, 'index'])->name('products.stock');
Route::get('/products/{id}/show', [App\Http\Controllers\StockController::class, 'show'])->name('products.show');
Route::post('/stock', [App\Http\Controllers\StockApiController::class, 'store'])->name('products.stock.store');
Route::put('/stock/{id}', [App\Http\Controllers\StockController::class, 'update'])->name('products.stock.update');
Route::delete('/stock/{id}', [App\Http\Controllers\StockController::class, 'destroy'])->name('products.stock.destroy');
Route::delete('/stock/bulk', [App\Http\Controllers\StockController::class, 'bulkDestroy'])->name('products.stock.bulkDestroy');
Route::get('/stock-summary', [App\Http\Controllers\StockController::class, 'summary'])->name('products.stock.summary');

Route::get('/out-of-stock-table', [App\Http\Controllers\StockController::class, 'outOfStockTable'])->name('products.out-of-stock-table');

Route::get('/stock-removal-table', [App\Http\Controllers\StockController::class, 'stockRemovalTable'])->name('products.stock-removal-table');
Route::delete('/old-month-stock-table/{month}', [App\Http\Controllers\LeafProductController::class, 'deleteMonthlySnapshot'])->name('products.old-month-stock-table.delete');

Route::get('/remove-stock', [App\Http\Controllers\StockController::class, 'removeStock'])->name('products.remove_stock');
Route::post('/remove-stock', [App\Http\Controllers\StockController::class, 'removeStockStore'])->name('products.remove_stock.store');
Route::get('/remove-stock/recover/{id}', [App\Http\Controllers\StockController::class, 'recoverStock'])->name('products.remove_stock.recover');
Route::get('/search-stock', [App\Http\Controllers\StockController::class, 'searchStock'])->name('products.search_stock');

use App\Http\Controllers\StockController;

Route::get('/goods-in-stock-table', [StockController::class, 'goodsInStockTable'])->name('products.goods-in-stock-table');
Route::post('/goods-in-stock-table', [StockController::class, 'goodsInStockStore'])->name('products.goods-in-stock-table.store');
Route::post('/goods-in-stock-table/bulk', [StockController::class, 'goodsInStockBulkStore'])->name('products.goods-in-stock-table.bulk');
Route::post('/goods-in-stock-table/import', [StockController::class, 'goodsInStockImport'])->name('products.goods-in-stock-table.import');
Route::put('/goods-in-stock-table/{id}', [StockController::class, 'goodsInStockUpdate'])->name('products.goods-in-stock-table.update');
Route::delete('/goods-in-stock-table/{id}', [StockController::class, 'goodsInStockDelete'])->name('products.goods-in-stock-table.delete');

Route::get('/old-month-stock-table', [App\Http\Controllers\LeafProductController::class, 'monthlySnapshot'])->name('products.old-month-stock-table');

Route::get('/products/{id}/raw', [App\Http\Controllers\StockController::class, 'raw'])->name('products.raw');
Route::get('/products/{id}/processed', [App\Http\Controllers\StockController::class, 'processed'])->name('products.processed');
