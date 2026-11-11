<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

class LeafProductController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'raw');
        $ranges = [
            'raw' => [1, 234],
            'processed' => [235, 269],
        ];
        $range = $ranges[$tab] ?? $ranges['raw'];

        $excludedCategories = [
            "\u{1790}\u{17d2}\u{1791}\u{17b6}\u{1792}\u{17c1}\u{1791}\u{17b6}\u{1792}\u{17b6}\u{1794}\u{17c9}",
            'Power Sachi',
            'Pushing Gemstone',
        ];
        $normalizedExcludedCategories = array_map(function ($value) {
            return strtolower(str_replace(' ', '', trim((string) $value)));
        }, $excludedCategories);

        $removedSub = DB::table('remove_stock')
            ->selectRaw('name, SUM(qty_removed) as qty_out, SUM(CAST(REPLACE(REPLACE(weight, ",", "."), "g", "") AS DECIMAL(10,2))) as weight_out')
            ->groupBy('name');

        $query = DB::table('main')
            ->leftJoinSub($removedSub, 'removed', function ($join) {
                $join->on('main.name', '=', 'removed.name');
            })
            ->whereBetween('main.id', $range)
            ->selectRaw('
                CASE
                    WHEN main.category IS NULL OR TRIM(main.category) = "" THEN "Unknown"
                    ELSE TRIM(main.category)
                END as category,
                TRIM(main.name) as name,
                main.code_from as code_from,
                main.code_to as code_to,
                main.total_qty as total_qty,
                main.status_1 as status_1,
                main.remain_qty as remain_qty,
                main.total_weight as total_weight,
                main.status_2 as status_2,
                main.remain_weight as remain_weight,
                COALESCE(removed.qty_out,0) as qty_out,
                COALESCE(removed.weight_out,0) as weight_out,
                main.id as main_id
            ');

        $query->where(function ($q) use ($normalizedExcludedCategories) {
            $q->whereNull('main.category')
              ->orWhere(DB::raw('TRIM(main.category)'), '')
              ->orWhereRaw("REPLACE(LOWER(TRIM(main.category)), ' ', '') NOT IN (?, ?, ?)", $normalizedExcludedCategories);
        });

        if ($request->filled('category') && $request->category != '') {
            if ($request->category == 'Unknown') {
                $query->where(function ($q) {
                    $q->whereNull('main.category')
                      ->orWhere(DB::raw('TRIM(main.category)'), '');
                });
            } else {
                $query->where('main.category', $request->category);
            }
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('main.name', 'like', "%{$search}%")
                  ->orWhere('main.category', 'like', "%{$search}%");
            });
        }

        $stockData = $query->get();

        $leafProductsArray = [];
        foreach ($stockData as $item) {
            $leafProductsArray[] = (object)[
                'category' => $item->category,
                'name' => $item->name,
                'code_from' => $item->code_from,
                'code_to' => $item->code_to,
                'total_qty' => (int) ($item->total_qty ?? 0),
                'status_1' => (int) ($item->qty_out ?? 0),
                'remain_qty' => (int) ($item->remain_qty ?? 0),
                'total_weight' => (float) ($item->total_weight ?? 0),
                'status_2' => (float) ($item->weight_out ?? 0),
                'remain_weight' => (float) ($item->remain_weight ?? 0),
                'main_id' => (int) ($item->main_id ?? 0),
                'id' => (int) ($item->main_id ?? 0)
            ];
        }

        $outOfStockSummary = [
            'count' => 0,
            'total_qty' => 0,
            'total_weight' => 0.0,
            'total_qty_out' => 0,
            'total_weight_out' => 0.0,
            'total_remain_qty' => 0,
            'total_remain_weight' => 0.0,
        ];

        foreach ($leafProductsArray as $product) {
            $remainQty = (float) $product->remain_qty;
            $remainWeight = (float) $product->remain_weight;

            if ($remainQty === 0 || $remainWeight === 0) {
                $outOfStockSummary['count']++;
                $outOfStockSummary['total_qty'] += $product->total_qty;
                $outOfStockSummary['total_weight'] += $product->total_weight;
                $outOfStockSummary['total_qty_out'] += $product->status_1;
                $outOfStockSummary['total_weight_out'] += $product->status_2;
                $outOfStockSummary['total_remain_qty'] += $product->remain_qty;
                $outOfStockSummary['total_remain_weight'] += $product->remain_weight;
            }
        }

        usort($leafProductsArray, function ($a, $b) use ($request) {
            if ($request->query('sort') === 'out_of_stock') {
                $aOutOfStock = (float) $a->remain_qty == 0 && (float) $a->remain_weight == 0;
                $bOutOfStock = (float) $b->remain_qty == 0 && (float) $b->remain_weight == 0;

                if ($aOutOfStock !== $bOutOfStock) {
                    return $aOutOfStock ? -1 : 1;
                }
            }

            return ($a->main_id ?? 0) - ($b->main_id ?? 0);
        });

        $currentPage = $request->input('page', 1);
        $perPage = 50;
        $offset = ($currentPage - 1) * $perPage;
        $items = array_slice($leafProductsArray, $offset, $perPage);
        $leafProducts = new \Illuminate\Pagination\LengthAwarePaginator($items, count($leafProductsArray), $perPage, $currentPage, [
            'path' => $request->url(),
            'query' => $request->query()
        ]);

        $categories = DB::table('main')
            ->whereBetween('id', $range)
            ->selectRaw('DISTINCT CASE WHEN category IS NULL OR TRIM(category) = "" THEN "Unknown" ELSE TRIM(category) END as category')
            ->pluck('category')
            ->sort();

        $productNames = $categories->filter(function ($category) use ($normalizedExcludedCategories) {
            $normalizedCategory = strtolower(str_replace(' ', '', trim((string) $category)));

            return !in_array($normalizedCategory, $normalizedExcludedCategories, true);
        })->values();

        return view('leaf-products', compact('leafProducts', 'productNames', 'outOfStockSummary'));
    }

    public function printRaw(Request $request)
    {
        $leafProducts = $this->buildLeafProductsPayload($request, 'raw');

        return view('leaf-products-print', compact('leafProducts'));
    }

    public function allData(Request $request)
    {
        $query = DB::table('main')->orderBy('id');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $allData = $query->paginate(50);

        return view('all-data', compact('allData'));
    }

    public function monthlySnapshot(Request $request)
    {
        $monthKey = $request->query('month', now()->format('Y-m'));
        $snapshotDate = Carbon::createFromFormat('Y-m', $monthKey)->startOfMonth();

        if (!Schema::hasTable('leaf_product_monthly_snapshots')) {
            return redirect()->back()->with('error', 'Snapshot table is not ready yet.');
        }

        $payload = $this->buildLeafProductsPayload($request, 'raw');
        $existing = DB::table('leaf_product_monthly_snapshots')->where('month_key', $monthKey)->first();
        if (!$existing) {
            DB::table('leaf_product_monthly_snapshots')->insert([
                'month_key' => $monthKey,
                'snapshot_date' => $snapshotDate->toDateString(),
                'payload' => json_encode($payload),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            DB::table('leaf_product_monthly_snapshots')->where('month_key', $monthKey)->update([
                'payload' => json_encode($payload),
                'snapshot_date' => $snapshotDate->toDateString(),
                'updated_at' => now(),
            ]);
        }

        $snapshots = DB::table('leaf_product_monthly_snapshots')
            ->orderByDesc('snapshot_date')
            ->get();

        $rows = $payload;
        $currentPage = $request->input('page', 1);
        $perPage = 50;
        $offset = ($currentPage - 1) * $perPage;
        $items = array_slice($rows, $offset, $perPage);
        $leafProducts = new \Illuminate\Pagination\LengthAwarePaginator($items, count($rows), $perPage, $currentPage, [
            'path' => $request->url(),
            'query' => $request->query()
        ]);

        $productNames = collect($rows)->pluck('name')->filter()->unique()->sort()->values();

        return view('old-month-stock-table', compact('leafProducts', 'productNames', 'monthKey', 'snapshots'));
    }

    public function deleteMonthlySnapshot($month)
    {
        DB::table('leaf_product_monthly_snapshots')->where('month_key', $month)->delete();

        return redirect()->route('products.old-month-stock-table', ['month' => now()->format('Y-m')])->with('success', 'បានលុបប្រវត្តិខែនេះដោយជោគជ័យ');
    }

    protected function buildLeafProductsPayload(Request $request, string $tab = 'raw')
    {
        $ranges = [
            'raw' => [1, 234],
            'processed' => [235, 269],
        ];
        $range = $ranges[$tab] ?? $ranges['raw'];

        $excludedCategories = [
            "\u{1790}\u{17d2}\u{1791}\u{17b6}\u{1792}\u{17c1}\u{1791}\u{17b6}\u{1792}\u{17b6}\u{1794}\u{17c9}",
            'Power Sachi',
            'Pushing Gemstone',
        ];
        $normalizedExcludedCategories = array_map(function ($value) {
            return strtolower(str_replace(' ', '', trim((string) $value)));
        }, $excludedCategories);

        $removedSub = DB::table('remove_stock')
            ->selectRaw('name, SUM(qty_removed) as qty_out, SUM(CAST(REPLACE(REPLACE(weight, ",", "."), "g", "") AS DECIMAL(10,2))) as weight_out')
            ->groupBy('name');

        $query = DB::table('main')
            ->leftJoinSub($removedSub, 'removed', function ($join) {
                $join->on('main.name', '=', 'removed.name');
            })
            ->whereBetween('main.id', $range)
            ->selectRaw('CASE WHEN main.category IS NULL OR TRIM(main.category) = "" THEN "Unknown" ELSE TRIM(main.category) END as category, TRIM(main.name) as name, main.code_from as code_from, main.code_to as code_to, main.total_qty as total_qty, main.status_1 as status_1, main.remain_qty as remain_qty, main.total_weight as total_weight, main.status_2 as status_2, main.remain_weight as remain_weight, COALESCE(removed.qty_out,0) as qty_out, COALESCE(removed.weight_out,0) as weight_out, main.id as main_id');

        $query->where(function ($q) use ($normalizedExcludedCategories) {
            $q->whereNull('main.category')
              ->orWhere(DB::raw('TRIM(main.category)'), '')
              ->orWhereRaw("REPLACE(LOWER(TRIM(main.category)), ' ', '') NOT IN (?, ?, ?)", $normalizedExcludedCategories);
        });

        if ($request->filled('category') && $request->category != '') {
            if ($request->category == 'Unknown') {
                $query->where(function ($q) {
                    $q->whereNull('main.category')->orWhere(DB::raw('TRIM(main.category)'), '');
                });
            } else {
                $query->where('main.category', $request->category);
            }
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('main.name', 'like', "%{$search}%")
                  ->orWhere('main.category', 'like', "%{$search}%");
            });
        }

        $stockData = $query->get();
        $leafProductsArray = [];
        foreach ($stockData as $item) {
            $leafProductsArray[] = [
                'category' => $item->category,
                'name' => $item->name,
                'code_from' => $item->code_from,
                'code_to' => $item->code_to,
                'total_qty' => (int) ($item->total_qty ?? 0),
                'status_1' => (int) ($item->qty_out ?? 0),
                'remain_qty' => (int) ($item->remain_qty ?? 0),
                'total_weight' => (float) ($item->total_weight ?? 0),
                'status_2' => (float) ($item->weight_out ?? 0),
                'remain_weight' => (float) ($item->remain_weight ?? 0),
                'main_id' => (int) ($item->main_id ?? 0),
                'id' => (int) ($item->main_id ?? 0),
            ];
        }

        usort($leafProductsArray, function ($a, $b) {
            return ($a['main_id'] ?? 0) - ($b['main_id'] ?? 0);
        });

        return $leafProductsArray;
    }
}
