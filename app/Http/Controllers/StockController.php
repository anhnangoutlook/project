<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Main;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class StockController extends Controller
{
    private $customOrder = [
        'ត្បូងបារាំងដើម',
        'ធម្មជាតិ',
        'គ្រិស្តាល់',
        'និល',
        'Amathyst',
        'Citrin',
        'Emarald',
        'GARNET',
        'Power Sachi',
        'Quartz',
        'Rose quartz',
        'Ruby',
        'Sapphire',
        'smoky',
        'Topa',
        'Zicon',
        'Black',
        'Golden yellow',
        'Large Green',
        'ត្បូងបន្ទះសាច់ស្រាល',
        'ថ្មកែងត្បូង'
    ];

    public function index(Request $request)
    {
        $query = Stock::query();
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('no_val', 'like', '%' . $search . '%')
                  ->orWhere('code', 'like', '%' . $search . '%');
            });
        }
        if ($request->has('category') && $request->category != '') {
            $query->where('no_val', $request->category);
        }
        if ($request->has('sort') && $request->has('direction')) {
            $query->orderBy($request->sort, $request->direction);
        }
        $stocks = $query->paginate(50, ['*'], 'stock_page');
        $kg_sort = $request->query('kg_sort', 'id');
        $kg_direction = $request->query('kg_direction', 'desc');
        $stockkgQuery = DB::table('stockkg');
        if ($request->has('kg_search')) {
            $stockkgQuery->where('name', 'like', '%' . $request->kg_search . '%');
        }
        if ($request->has('kg_category') && $request->kg_category != '') {
            $stockkgQuery->where('name', $request->kg_category);
        }
        $stockkgQuery->orderBy($kg_sort, $kg_direction);
        $stockkgs = $stockkgQuery->paginate(50, ['*'], 'stockkg_page');
        $productNames = Stock::distinct()->pluck('no_val')->sort();
        $stockKgNames = DB::table('stockkg')->distinct()->pluck('name')->sort();
        return view('stock', compact('stocks', 'stockkgs', 'productNames', 'stockKgNames'));
    }



    public function show($id)
    {
        $stock = Stock::find($id);

        if ($stock) {
            $searchValue = $stock->code ?: $stock->no_val;
            return redirect()->route('products.stock', ['search' => $searchValue]);
        }

        $goodItem = DB::table('good_in_stock')->where('id', $id)->first();

        if ($goodItem) {
            $searchValue = $goodItem->name ?: $goodItem->code;
            return redirect()->route('products.stock', ['search' => $searchValue]);
        }

        abort(404);
    }

    public function update(Request $request, $id)
    {
        $stock = Stock::find($id);
        $stock->update([
            'code' => $request->input('code'),
            'no_val' => $request->input('name'),
            'weight' => $request->input('weight'),
            'qty1' => $request->input('qty1'),
            'imported_at' => $request->input('imported_at'),
        ]);
        return redirect()->route('products.stock')->with('success', 'បានកែប្រែស្តុកដោយជោគជ័យ');
    }

    public function destroy($id)
    {
        $stock = Stock::find($id);
        $stock->delete();
        return redirect()->route('products.stock')->with('success', 'បានលុបស្តុកដោយជោគជ័យ');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = explode(',', $request->ids);
        Stock::whereIn('id', $ids)->delete();
        return redirect()->route('products.stock')->with('success', 'បានលុបស្តុកដោយជោគជ័យ');
    }

    public function summary(Request $request)
    {
        $excludedCategories = [
            "\u{1790}\u{17d2}\u{1798}\u{178f}\u{17c2}\u{1784}\u{178f}\u{17d2}\u{1794}\u{17bc}\u{1784}",
            'Power Sachi',
            'Pushing Gemstone',
        ];
        $normalizedExcludedCategories = array_map(function ($category) {
            return strtolower(str_replace(' ', '', trim($category)));
        }, $excludedCategories);

        $query = Stock::query()
            ->selectRaw('name, MIN(code) as start_code, MAX(code) as end_code, SUM(COALESCE(CAST(NULLIF(REPLACE(REPLACE(REPLACE(LOWER(TRIM(qty1)), " ", ""), ",", "."), "g", ""), "") AS DECIMAL(12,3)), 0)) as total_qty, SUM(COALESCE(CAST(NULLIF(REPLACE(REPLACE(REPLACE(LOWER(TRIM(weight)), " ", ""), ",", "."), "g", ""), "") AS DECIMAL(12,3)), 0)) as total_weight')
            ->whereNotNull('name')
            ->whereRaw("TRIM(name) <> ''");

        foreach ($normalizedExcludedCategories as $category) {
            $query->whereRaw("REPLACE(LOWER(TRIM(name)), ' ', '') NOT LIKE ?", ['%' . $category . '%']);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . trim($request->input('search')) . '%');
        }

        $stockSummaries = $query
            ->groupBy('name')
            ->orderBy('name')
            ->paginate(50)
            ->appends($request->query());

        return view('stock-summary', compact('stockSummaries'));
    }

    public function oldLeafNewImport()
    {
        $excludedCategories = [
            "\u{1790}\u{17d2}\u{1798}\u{178f}\u{17c2}\u{1784}\u{178f}\u{17d2}\u{1794}\u{17bc}\u{1784}",
            'Power Sachi',
            'Pushing Gemstone',
        ];
        $normalizedExcludedCategories = array_map(function ($category) {
            return strtolower(str_replace(' ', '', trim($category)));
        }, $excludedCategories);
        $categoryFilter = "REPLACE(LOWER(TRIM(category)), ' ', '') NOT IN (?, ?, ?)";

        $mainStock = Main::query()
            ->where(function ($query) use ($normalizedExcludedCategories, $categoryFilter) {
                $query->whereNull('category')
                    ->orWhereRaw($categoryFilter, $normalizedExcludedCategories);
            })
            ->orderBy('category')
            ->orderBy('name')
            ->paginate(20, ['*'], 'main_page');

        $removedStock = DB::table('remove_stock')
            ->whereNotExists(function ($query) use ($normalizedExcludedCategories) {
                $query->selectRaw('1')
                    ->from('main as excluded_main')
                    ->whereColumn('excluded_main.name', 'remove_stock.name')
                    ->whereRaw(
                        "REPLACE(LOWER(TRIM(excluded_main.category)), ' ', '') IN (?, ?, ?)",
                        $normalizedExcludedCategories
                    );
            })
            ->orderByDesc('created_at')
            ->paginate(20, ['*'], 'removed_page');

        $remainingGems = Main::query()
            ->where(function ($query) use ($normalizedExcludedCategories) {
                $query->whereNull('category')
                    ->orWhereRaw(
                        "REPLACE(LOWER(TRIM(category)), ' ', '') NOT IN (?, ?, ?)",
                        $normalizedExcludedCategories
                    );
            })
            ->where(function ($query) {
                $query->whereRaw("COALESCE(NULLIF(TRIM(remain_qty), ''), '0') + 0 > 0")
                    ->orWhereRaw(
                        "COALESCE(NULLIF(REPLACE(REPLACE(REPLACE(LOWER(TRIM(remain_weight)), ' ', ''), ',', ''), 'g', ''), ''), '0') + 0 > 0"
                    );
            })
            ->orderBy('category')
            ->orderBy('name')
            ->paginate(20, ['*'], 'gems_page');

        $summaryStock = Main::query()
            ->selectRaw(
                "category, name, COUNT(*) as product_count, SUM(COALESCE(NULLIF(TRIM(remain_qty), ''), '0') + 0) as total_qty, SUM(COALESCE(NULLIF(REPLACE(REPLACE(REPLACE(LOWER(TRIM(remain_weight)), ' ', ''), ',', ''), 'g', ''), ''), '0') + 0) as total_weight"
            )
            ->where(function ($query) use ($normalizedExcludedCategories) {
                $query->whereNull('category')
                    ->orWhereRaw(
                        "REPLACE(LOWER(TRIM(category)), ' ', '') NOT IN (?, ?, ?)",
                        $normalizedExcludedCategories
                    );
            })
            ->groupBy('category', 'name')
            ->orderBy('category')
            ->orderBy('name')
            ->paginate(20, ['*'], 'summary_page');

        return view('old-leaf-new-import', compact('mainStock', 'removedStock', 'remainingGems', 'summaryStock'));
    }

    public function outOfStockTable(Request $request)
    {
        $excludedCategories = [
            "\u{1790}\u{17d2}\u{1798}\u{178f}\u{17c2}\u{1784}\u{178f}\u{17d2}\u{1794}\u{17bc}\u{1784}",
            'Power Sachi',
            'Pushing Gemstone',
        ];
        $normalizedExcludedCategories = array_map(function ($category) {
            return strtolower(str_replace(' ', '', trim($category)));
        }, $excludedCategories);

        $query = Main::query()
            ->select('id', 'category', 'name')
            ->selectRaw('code_from as code, remain_qty as qty1, remain_weight as weight')
            ->whereRaw("COALESCE(NULLIF(TRIM(remain_qty), ''), '0') + 0 = 0")
            ->whereRaw("COALESCE(NULLIF(TRIM(remain_weight), ''), '0') + 0 = 0")
            ->where(function ($query) use ($normalizedExcludedCategories) {
                $query->whereNull('category')
                    ->orWhereRaw("REPLACE(LOWER(TRIM(category)), ' ', '') NOT IN (?, ?, ?)", $normalizedExcludedCategories);
            });

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('code_from', 'like', '%' . $search . '%');
            });
        }

        $leafProducts = $query
            ->orderBy('category')
            ->orderBy('name')
            ->paginate(50)
            ->appends($request->query());

        $productNames = Main::query()
            ->whereNotNull('category')
            ->whereRaw("REPLACE(LOWER(TRIM(category)), ' ', '') NOT IN (?, ?, ?)", $normalizedExcludedCategories)
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('out-of-stock-table', compact('leafProducts', 'productNames'));
    }

    public function stockRemovalTable(Request $request)
    {
        $validated = $request->validate([
            'filter_month' => ['nullable', 'date_format:Y-m'],
        ]);
        $filterMonth = $validated['filter_month'] ?? now()->format('Y-m');
        $monthStart = Carbon::createFromFormat('Y-m', $filterMonth)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        $removedStock = DB::table('remove_stock')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->orderByDesc('created_at')
            ->paginate(50)
            ->appends(['filter_month' => $filterMonth]);

        return view('stock-removal-table', compact('removedStock', 'filterMonth'));
    }

    public function raw($id)
    {
        return redirect()->route('products.out-of-stock-table', ['product_id' => $id]);
    }

    public function processed($id)
    {
        return redirect()->route('products.out-of-stock-table', ['product_id' => $id]);
    }

    public function removeStock(Request $request)
    {
        $type = $request->input('type', 'standard');
        $productNames = DB::table('stock')->distinct()->pluck('no_val')->sort();
        $products = null;

        if ($type == 'kg') {
            $productNames = DB::table('stockkg')->distinct()->pluck('name')->sort();
            if ($request->has('category') && $request->category != '') {
                $products = DB::table('stockkg')->where('name', $request->category)->get();
            }
        } else {
            if ($request->has('category') && $request->category != '') {
                $products = DB::table('stock')->where('no_val', $request->category)->get();
            }
        }

        $removedStock = DB::table('remove_stock')->orderBy('created_at', 'desc')->get();

        return view('remove_stock', compact('productNames', 'products', 'removedStock', 'type'));
    }

    public function removeStockStore(Request $request)
    {
        $request->validate([
            'date' => ['nullable', 'array'],
            'date.*' => ['required', 'date_format:Y-m-d'],
        ]);
        $type = $request->input('type', 'standard');
        $selected = $request->input('selected', []);
        $ids = $request->input('id', []);
        $qtyToRemove = $request->input('qty_to_remove', []);
        $dates = $request->input('date', []);
        $filterMonth = now()->format('Y-m');

        $tableName = $type == 'kg' ? 'stockkg' : 'stock';

        if (count($selected) > 0) {
            foreach ($selected as $selectedId) {
                $index = array_search($selectedId, $ids);
                if ($index !== false && isset($qtyToRemove[$index]) && $qtyToRemove[$index] == 1) {
                    $stockRecord = DB::table($tableName)->where('id', $selectedId)->first();

                    if ($stockRecord && $stockRecord->qty1 > 0) {
                        $existingRemoval = DB::table('remove_stock')->where('code', $stockRecord->code)->first();

                        if (!$existingRemoval) {
                            DB::table($tableName)->where('id', $selectedId)->update(['qty1' => 0]);
                            $removedAt = isset($dates[$selectedId])
                                ? Carbon::createFromFormat('Y-m-d', $dates[$selectedId])->startOfDay()
                                : now();

                            DB::table('remove_stock')->insert([
                                'code' => $stockRecord->code,
                                'name' => $stockRecord->name,
                                'weight' => $stockRecord->weight,
                                'qty_removed' => 1,
                                'created_at' => $removedAt,
                                'updated_at' => now()
                            ]);
                            $filterMonth = $removedAt->format('Y-m');
                        }
                    }
                }
            }
        }

        return redirect()->route('products.stock-removal-table', ['filter_month' => $filterMonth])->with('success', 'បានធ្វើបន្ថែមដោយជោគជ័យ');
    }

    public function recoverStock($id)
    {
        $removedRecord = DB::table('remove_stock')->where('id', $id)->first();

        if ($removedRecord) {
            DB::table('stock')->where('code', $removedRecord->code)->update(['qty1' => 1]);

            DB::table('remove_stock')->where('id', $id)->delete();

            return redirect()->route('products.remove_stock')->with('success', 'បានស្តារទិន្នន័យដោយជោគជ័យ');
        }

        return redirect()->route('products.remove_stock')->with('error', 'រកមិនឃើញទិន្នន័យ');
    }

    public function searchStock(Request $request)
    {
        $query = DB::table('main');

        if ($request->has('category') && $request->category != '') {
            $query->where('name', $request->category);
        }

        if ($request->has('code') && $request->code != '') {
            $query->where('code', $request->code);
        }

        $item = $query->first();

        if ($item) {
            return response()->json([
                'success' => true,
                'item' => $item
            ]);
        } else {
            return response()->json([
                'success' => false
            ]);
        }
    }

    public function goodsInStockTable(Request $request)
    {
        $rows = DB::table('good_in_stock')->get()->toArray();
        $orderMap = array_flip($this->customOrder);
        usort($rows, function ($a, $b) use ($orderMap) {
            $an = $a->name ?? '';
            $bn = $b->name ?? '';
            $apos = isset($orderMap[$an]) ? $orderMap[$an] : PHP_INT_MAX;
            $bpos = isset($orderMap[$bn]) ? $orderMap[$bn] : PHP_INT_MAX;
            if ($apos === $bpos) {
                return strcmp($an, $bn);
            }
            return $apos <=> $bpos;
        });
        $goodsIn = collect($rows);
        $stockNames = Stock::query()->distinct()->pluck('no_val')->filter()->sort()->values();
        return view('goods-in-stock-table', compact('goodsIn', 'stockNames'));
    }

    public function goodsInStockStore(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'weight' => 'nullable|string|max:255',
            'qty1' => 'required|numeric',
            'imported_at' => 'nullable|date',
        ]);
        $exists = DB::table('good_in_stock')->where('code', $validated['code'])->exists();
        if ($exists) {
            return response()->json(['success' => false, 'error' => 'This product code already exists.'], 409);
        }

        try {
            $payload = [
                'code' => $validated['code'],
                'name' => $validated['name'],
                'weight' => $validated['weight'] ?? null,
                'qty1' => $validated['qty1'],
                'imported_at' => $validated['imported_at'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $id = DB::transaction(function () use ($payload, $validated) {
                $id = DB::table('good_in_stock')->insertGetId($payload);
                DB::table('stock')->insert([
                    'code' => $validated['code'],
                    'no_val' => $validated['name'],
                    'weight' => $validated['weight'] ?? null,
                    'qty1' => $validated['qty1'],
                    'imported_at' => $validated['imported_at'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return $id;
            });

            $row = DB::table('good_in_stock')->where('id', $id)->first();
            return response()->json(['success' => true, 'data' => $row]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function goodsInStockBulkStore(Request $request)
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1', 'max:500'],
            'items.*.code' => ['required', 'string', 'max:255'],
            'items.*.name' => ['required', 'string', 'max:255'],
            'items.*.weight' => ['nullable', 'string', 'max:255'],
            'items.*.qty1' => ['required', 'numeric'],
            'items.*.imported_at' => ['nullable', 'date'],
        ]);

        $items = array_map(function ($item) {
            $timestamp = now();

            return [
                'code' => trim($item['code']),
                'name' => trim($item['name']),
                'weight' => $item['weight'] ?? null,
                'qty1' => $item['qty1'],
                'imported_at' => $item['imported_at'] ?? null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }, $validated['items']);
        $codes = array_column($items, 'code');

        if (count($codes) !== count(array_unique($codes))) {
            throw ValidationException::withMessages(['items' => 'Each code must be unique.']);
        }

        if (DB::table('good_in_stock')->whereIn('code', $codes)->exists()) {
            throw ValidationException::withMessages(['items' => 'One or more codes already exist.']);
        }

        DB::transaction(function () use ($items) {
            DB::table('good_in_stock')->insert($items);
            $stockRows = array_map(function ($item) {
                return [
                    'code' => $item['code'],
                    'no_val' => $item['name'],
                    'weight' => $item['weight'],
                    'qty1' => $item['qty1'],
                    'imported_at' => $item['imported_at'],
                    'created_at' => $item['created_at'],
                    'updated_at' => $item['updated_at'],
                ];
            }, $items);
            DB::table('stock')->insert($stockRows);
        });

        return response()->json(['success' => true, 'count' => count($items)]);
    }

    public function goodsInStockImport(Request $request)
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx', 'max:10240'],
        ]);

        $rows = $this->parseGoodsInStockImport($validated['file']);
        if (count($rows) === 0) {
            throw ValidationException::withMessages(['file' => 'The uploaded file contains no data rows.']);
        }

        if (count($rows) > 5000) {
            throw ValidationException::withMessages(['file' => 'The uploaded file may contain at most 5000 data rows.']);
        }

        $codes = array_column($rows, 'code');
        if (count($codes) !== count(array_unique($codes))) {
            throw ValidationException::withMessages(['file' => 'The uploaded file contains duplicate codes.']);
        }

        if (DB::table('good_in_stock')->whereIn('code', $codes)->exists()) {
            throw ValidationException::withMessages(['file' => 'One or more codes already exist.']);
        }

        $timestamp = now();
        $goodRows = array_map(function ($row) use ($timestamp) {
            return $row + ['created_at' => $timestamp, 'updated_at' => $timestamp];
        }, $rows);
        $stockRows = array_map(function ($row) use ($timestamp) {
            return [
                'code' => $row['code'],
                'no_val' => $row['name'],
                'weight' => $row['weight'],
                'qty1' => $row['qty1'],
                'imported_at' => $row['imported_at'],
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }, $rows);

        DB::transaction(function () use ($goodRows, $stockRows) {
            foreach (array_chunk($goodRows, 500) as $chunk) {
                DB::table('good_in_stock')->insert($chunk);
            }
            foreach (array_chunk($stockRows, 500) as $chunk) {
                DB::table('stock')->insert($chunk);
            }
        });

        return response()->json(['success' => true, 'count' => count($rows)]);
    }

    private function parseGoodsInStockImport($file)
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $rows = $extension === 'xlsx'
            ? $this->readGoodsInStockXlsx($file->getRealPath())
            : $this->readGoodsInStockCsv($file->getRealPath());

        if (count($rows) < 2) {
            throw ValidationException::withMessages(['file' => 'The file must include a header row and at least one data row.']);
        }

        $headers = array_map(function ($header) {
            return strtolower(trim(preg_replace('/^\xEF\xBB\xBF/', '', (string) $header)));
        }, array_shift($rows));
        $requiredHeaders = ['code', 'name', 'weight', 'qty1'];
        foreach ($requiredHeaders as $header) {
            if (!in_array($header, $headers, true)) {
                throw ValidationException::withMessages(['file' => 'The file must contain code, name, weight, and qty1 columns.']);
            }
        }

        $parsedRows = [];
        foreach ($rows as $rowNumber => $row) {
            if (count(array_filter($row, function ($value) {
                return trim((string) $value) !== '';
            })) === 0) {
                continue;
            }

            $record = [];
            foreach ($headers as $index => $header) {
                $record[$header] = trim((string) ($row[$index] ?? ''));
            }
            if ($record['code'] === '' || $record['name'] === '' || !is_numeric($record['qty1'])) {
                throw ValidationException::withMessages(['file' => 'Invalid required values on data row ' . ($rowNumber + 2) . '.']);
            }

            $importedAt = $record['imported_at'] ?? '';
            if ($importedAt !== '' && is_numeric($importedAt)) {
                $excelDays = (int) floor((float) $importedAt);
                $excelSeconds = (int) round(((float) $importedAt - $excelDays) * 86400);
                $importedAt = Carbon::create(1899, 12, 30)->addDays($excelDays)->addSeconds($excelSeconds)->toDateTimeString();
            } elseif ($importedAt !== '') {
                try {
                    $importedAt = Carbon::parse($importedAt)->toDateTimeString();
                } catch (\Throwable $exception) {
                    throw ValidationException::withMessages(['file' => 'Invalid date on data row ' . ($rowNumber + 2) . '.']);
                }
            } else {
                $importedAt = now()->toDateTimeString();
            }

            $parsedRows[] = [
                'code' => $record['code'],
                'name' => $record['name'],
                'weight' => $record['weight'] !== '' ? $record['weight'] : null,
                'qty1' => $record['qty1'],
                'imported_at' => $importedAt,
            ];
        }

        return $parsedRows;
    }

    private function readGoodsInStockCsv($path)
    {
        $handle = fopen($path, 'rb');
        if ($handle === false) {
            throw ValidationException::withMessages(['file' => 'The uploaded CSV file could not be opened.']);
        }

        $rows = [];
        while (($row = fgetcsv($handle)) !== false) {
            $rows[] = $row;
        }
        fclose($handle);

        return $rows;
    }

    private function readGoodsInStockXlsx($path)
    {
        try {
            $archive = new \PharData($path);
            $workbook = $this->readXlsxXml($archive, 'xl/workbook.xml');
            $relationships = $this->readXlsxXml($archive, 'xl/_rels/workbook.xml.rels');
            $workbook->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            $workbook->registerXPathNamespace('r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
            $relationships->registerXPathNamespace('r', 'http://schemas.openxmlformats.org/package/2006/relationships');
            $sheets = $workbook->xpath('//x:sheets/x:sheet');
            if (!$sheets) {
                throw ValidationException::withMessages(['file' => 'The XLSX workbook has no worksheets.']);
            }
            $sheetRelationship = $sheets[0]->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships');
            $relationshipId = (string) $sheetRelationship['id'];
            $targets = array_values(array_filter($relationships->xpath('//r:Relationship') ?: [], function ($relationship) use ($relationshipId) {
                return (string) $relationship['Id'] === $relationshipId;
            }));
            if (!$targets) {
                throw ValidationException::withMessages(['file' => 'The first XLSX worksheet could not be located.']);
            }
            $target = ltrim((string) $targets[0]['Target'], '/');
            $sheetPath = strpos($target, 'xl/') === 0 ? $target : 'xl/' . $target;
            if (strpos($sheetPath, 'xl/worksheets/') !== 0 || strpos($sheetPath, '..') !== false) {
                throw ValidationException::withMessages(['file' => 'The XLSX worksheet path is invalid.']);
            }
            $sheet = $this->readXlsxXml($archive, $sheetPath);
            $sharedStrings = [];
            if (isset($archive['xl/sharedStrings.xml'])) {
                $sharedStringXml = $this->readXlsxXml($archive, 'xl/sharedStrings.xml');
                $sharedStringXml->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
                foreach ($sharedStringXml->xpath('//x:si') ?: [] as $sharedString) {
                    $sharedString->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
                    $textNodes = $sharedString->xpath('.//x:t');
                    $sharedStrings[] = implode('', array_map(function ($textNode) {
                        return (string) $textNode;
                    }, $textNodes ?: []));
                }
            }
            $sheet->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            $rows = [];
            foreach ($sheet->xpath('//x:sheetData/x:row') ?: [] as $xmlRow) {
                $row = [];
                foreach ($xmlRow->xpath('./x:c') ?: [] as $cell) {
                    $attributes = $cell->attributes();
                    preg_match('/^[A-Z]+/', (string) $attributes['r'], $columnMatch);
                    $column = $columnMatch[0] ?? 'A';
                    $columnIndex = 0;
                    foreach (str_split($column) as $letter) {
                        $columnIndex = ($columnIndex * 26) + ord($letter) - 64;
                    }
                    $valueNode = $cell->xpath('./x:v');
                    $value = isset($valueNode[0]) ? (string) $valueNode[0] : '';
                    if ((string) $attributes['t'] === 's') {
                        $value = $sharedStrings[(int) $value] ?? '';
                    } elseif ((string) $attributes['t'] === 'inlineStr') {
                        $textNodes = $cell->xpath('.//x:is/x:t');
                        $value = implode('', array_map(function ($textNode) {
                            return (string) $textNode;
                        }, $textNodes ?: []));
                    }
                    $row[$columnIndex - 1] = $value;
                }
                if ($row) {
                    ksort($row);
                    $lastColumn = max(array_keys($row));
                    $rows[] = array_values($row + array_fill(0, $lastColumn + 1, ''));
                }
            }

            return $rows;
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            throw ValidationException::withMessages(['file' => 'The XLSX file could not be read.']);
        }
    }

    private function readXlsxXml($archive, $path)
    {
        if (!isset($archive[$path])) {
            throw ValidationException::withMessages(['file' => 'The XLSX file is missing a required workbook component.']);
        }

        $content = $archive[$path]->getContent();
        $xml = simplexml_load_string($content, \SimpleXMLElement::class, LIBXML_NONET | LIBXML_NOBLANKS);
        if ($xml === false) {
            throw ValidationException::withMessages(['file' => 'The XLSX file contains invalid XML.']);
        }

        return $xml;
    }

    public function goodsInStockUpdate(Request $request, $id)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'weight' => 'nullable|string|max:255',
            'qty1' => 'required|numeric',
            'imported_at' => 'nullable|date',
        ]);

        try {
            // prevent updating to a code that already exists on another record
            $existsOther = DB::table('good_in_stock')->where('code', $validated['code'])->where('id', '<>', $id)->exists();
            if ($existsOther) {
                return response()->json(['success' => false, 'error' => 'ទំនិញនេះមានរួចហើយ'], 409);
            }
            DB::table('good_in_stock')->where('id', $id)->update([
                'code' => $validated['code'],
                'name' => $validated['name'],
                'weight' => $validated['weight'] ?? null,
                'qty1' => $validated['qty1'],
                'imported_at' => $validated['imported_at'] ?? null,
                'updated_at' => now(),
            ]);
            $row = DB::table('good_in_stock')->where('id', $id)->first();
            return response()->json(['success' => true, 'data' => $row]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function goodsInStockDelete($id)
    {
        try {
            DB::table('good_in_stock')->where('id', $id)->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
