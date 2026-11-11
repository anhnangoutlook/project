<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\GoodInStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StockApiController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'weight' => 'nullable|string|max:255',
            'qty1' => 'required|numeric',
            'imported_at' => 'nullable|date',
        ]);

        try {
            Log::info('Stock store called', ['validated' => $validated]);

            $stock = Stock::create([
                'code' => $validated['code'],
                'no_val' => $validated['name'],
                'weight' => $validated['weight'] ?? null,
                'qty1' => $validated['qty1'],
                'imported_at' => $validated['imported_at'] ?? null,
            ]);
            // also persist a copy into the good_in_stock table
            $good = GoodInStock::create($validated);

            Log::info('Stock and GoodInStock created', ['stock_id' => $stock->id ?? null, 'good_id' => $good->id ?? null]);

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Data saved successfully', 'data' => $stock], 201);
            }

            return redirect()->back()->with('success', 'Data saved successfully');
        } catch (\Exception $e) {
            Log::error('Error saving stock', ['error' => $e->getMessage()]);

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }

            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
