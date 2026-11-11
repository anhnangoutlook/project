@extends('layout')

@section('content')
<style>
    .container {
        margin-left: 0 !important;
        padding-left: 0 !important;
        max-width: 100% !important;
        width: 100% !important;
    }
    .card {
        border-radius: 0.5rem;
        width: 100% !important;
    }
    .table-responsive {
        width: 100% !important;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .table {
        table-layout: fixed;
        width: 100% !important;
        margin-bottom: 0;
        border-collapse: collapse;
    }
    .table-bordered th,
    .table-bordered td {
        border: 1px solid #000000 !important;
        text-align: center;
        font-size: 10px;
        font-family: 'AKbalthom Monstera', 'AKbalthom KhmerNew', 'Kantumruuy Pro', sans-serif;
        padding: 8px 4px;
        vertical-align: middle;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .table thead th:nth-child(1) { background-color: #ff6b6b; width: 10%; }
    .table thead th:nth-child(2) { background-color: #4ecdc4; width: 18%; text-align: center; }
    .table thead th:nth-child(3) { background-color: #45b7d1; width: 8%; }
    .table thead th:nth-child(4) { background-color: #96ceb4; width: 8%; }
    .table thead th:nth-child(5) { background-color: #ffeaa7; width: 8%; }
    .table thead th:nth-child(6) { background-color: #c1c2c3; width: 8%; }
    .table thead th:nth-child(7) { background-color: #fd79a8; width: 12%; }
    .table thead th:nth-child(8) { background-color: #a29bfe; width: 9%; }
    .table thead th:nth-child(9) { background-color: #fdcb6e; width: 8%; }
    .table thead th:nth-child(10) { background-color: #00b894; width: 11%; }
    .table tbody td:nth-child(2) {
        text-align: center;
        padding-left: 0;
    }
    nav p.text-sm.text-gray-700,
    .pagination div fieldset p,
    .pagination .flex-1.flex.justify-between + div p {
        display: none !important;
    }
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    .form-control:focus,
    .form-select:focus {
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    select option {
        color: #000000 !important;
        background-color: #ffffff !important;
    }
    tr.row-dark-red-alert td {
        background-color: #8b0000 !important;
        color: #ffffff !important;
        font-weight: bold;
    }
    tr.row-red-alert td {
        background-color: #fce8e6 !important;
        color: #a51d24 !important;
        font-weight: bold;
    }
    .row-yellow-alert {
        background-color: #fef7e0 !important;
    }
    .out-of-stock-text {
        background-color: #8b0000;
        color: #ffffff;
        padding: 0 4px;
        border-radius: 3px;
        display: inline-block;
    }
    @media print {
        .sidebar,
        .navbar,
        .toggle-btn,
        .nav-tabs,
        .alert,
        form,
        .table-actions,
        .pagination,
        button,
        a {
            display: none !important;
        }
        .container,
        .card,
        .card-body,
        .table-responsive {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            overflow: visible !important;
            box-shadow: none !important;
        }
        .table {
            table-layout: auto;
            width: 100% !important;
        }
        .table-bordered th,
        .table-bordered td {
            font-size: 9px;
            white-space: normal;
        }
    }
</style>

<div class="container">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center gap-3 mb-4 text-start justify-content-start">
                <button class="toggle-btn btn btn-outline-secondary" onclick="toggleSidebar()"><i data-lucide="menu"></i></button>
                <h1 class="mb-0">ស្តុកទំនិញប្រចាំខែ</h1>
            </div>
            <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                    <a class="nav-link {{ request('tab', 'raw') == 'raw' ? 'active' : '' }}" href="{{ route('products.leaf', ['tab' => 'raw', 'search' => request('search'), 'category' => request('category')]) }}">ស្តុកឆៅ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('tab') == 'processed' ? 'active' : '' }}" href="{{ route('products.leaf', ['tab' => 'processed', 'search' => request('search'), 'category' => request('category')]) }}">ស្តុកច្នៃ</a>
                </li>
            </ul>
            @php
                $outOfStockCount = $outOfStockSummary['count'] ?? 0;
                $outOfStockTotalQty = $outOfStockSummary['total_qty'] ?? 0;
                $outOfStockTotalWeight = $outOfStockSummary['total_weight'] ?? 0;
                $outOfStockTotalQtyOut = $outOfStockSummary['total_qty_out'] ?? 0;
                $outOfStockTotalWeightOut = $outOfStockSummary['total_weight_out'] ?? 0;
            @endphp
            @if($outOfStockCount > 0)
                <div class="alert alert-danger alert-dismissible d-flex align-items-center justify-content-between mb-3" role="alert">
                    <div class="d-flex flex-column gap-1">
                        <div class="d-flex align-items-center gap-2">
                            <i data-lucide="alert-circle"></i>
                            <span>មានទំនិញចំនួន {{ $outOfStockCount }} ប្រភេទបានអស់ពីស្តុក!</span>
                        </div>
                        <small class="text-white-50">
                            សរុបគ្រាន់តែ across all pages: Qty {{ $outOfStockTotalQty }}, Weight {{ number_format($outOfStockTotalWeight, 0) }}g, Removed Qty {{ $outOfStockTotalQtyOut }}, Removed Weight {{ number_format($outOfStockTotalWeightOut, 0) }}g
                        </small>
                    </div>
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <a href="{{ route('products.goods-in-stock-table') }}" class="btn btn-sm btn-primary">Go to Add Stock</a>
                        <button type="button" class="btn btn-sm btn-outline-light" onclick="toggleOutOfStockRows(this)">Show Only Out-of-Stock</button>
                        <button type="button" class="btn btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-2 mb-3">
                <form action="{{ route('products.leaf') }}" method="GET" class="flex-grow-1 mb-0">
                    <input type="hidden" name="tab" value="{{ request('tab', 'raw') }}">
                    @if(request('sort') === 'out_of_stock')
                        <input type="hidden" name="sort" value="out_of_stock">
                    @endif
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="ស្វែងរកឈ្មោះទំនិញ" value="{{ request('search') }}">
                        <select name="category" class="form-select" onchange="this.form.submit()">
                            <option value="">ទាំងអស់</option>
                            @foreach($productNames as $name)
                                @php
                                    $normalizedName = strtolower(str_replace(' ', '', trim((string) $name)));
                                    $isExcludedOption = in_array($normalizedName, ['powersachi', 'pushinggemstone', "\u{1790}\u{17d2}\u{1791}\u{17b6}\u{1792}\u{17c1}\u{1791}\u{17b6}\u{1792}\u{17b6}\u{1794}\u{17c9}"], true);
                                @endphp
                                @if(trim($name) !== '' && !$isExcludedOption)
                                    <option value="{{ $name }}" {{ request('category') == $name ? 'selected' : '' }}>{{ $name }}</option>
                                @endif
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="filter"></i> ស្វែងរក
                        </button>
                        <a href="{{ route('products.leaf') }}" class="btn btn-outline-secondary">
                            <i data-lucide="refresh-cw"></i> សម្អាត
                        </a>
                    </div>
                </form>
                <div class="table-actions d-flex flex-wrap justify-content-lg-end gap-2">
                    <a class="btn btn-outline-danger" href="{{ route('products.leaf', array_merge(request()->except(['page', 'sort']), request('sort') === 'out_of_stock' ? [] : ['sort' => 'out_of_stock'])) }}">
                        {{ request('sort') === 'out_of_stock' ? 'បង្ហាញតាមលំដាប់ធម្មតា' : 'បង្ហាញអស់ពីស្តុកមុន' }}
                    </a>
                    @if(request('tab', 'raw') === 'raw')
                        <a class="btn btn-outline-primary" target="_blank" rel="noopener" href="{{ route('products.leaf.print-raw', request()->only(['search', 'category'])) }}">
                            បោះពុម្ពទិន្នន័យឆៅទាំងអស់
                        </a>
                    @endif
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>ប្រភេទ</th>
                            <th>ឈ្មោះទំនិញ</th>
                            <th>ចាប់ពី</th>
                            <th>រហូតដល់</th>
                            <th>ចំ.សរុប</th>
                            <th>ដក (Qty)</th>
                            <th>ចំ.ស.រប្រចាំខែ</th>
                            <th>សរុប (g)</th>
                            <th>ដក (g)</th>
                            <th>ស.វ(g)ប្រចាំខែ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($leafProducts->count() > 0)
                            @foreach($leafProducts as $product)
                                @php
                                    $remainQty = (float) $product->remain_qty;
                                    $remainWeight = (float) $product->remain_weight;
                                    $isCompletelyOut = $remainQty == 0 && $remainWeight == 0;
                                    $isPartiallyOut = $remainQty == 0 || $remainWeight == 0;
                                @endphp
                                <tr data-out-of-stock="{{ $isPartiallyOut ? '1' : '0' }}" @if($isCompletelyOut) class="row-dark-red-alert" @elseif($isPartiallyOut) class="row-red-alert" @endif>
                                    <td>{{ trim($product->category) !== '' ? $product->category : 'ទូទៅ' }}</td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->code_from }}</td>
                                    <td>{{ $product->code_to }}</td>

                                    <td>{{ trim($product->total_qty) !== '' ? $product->total_qty : 0 }}</td>
                                    <td>{!! trim($product->status_1) !== '' ? ($product->status_1 < 1 ? '<span style="color: #2f8f2f;">មិនទាន់ដក</span>' : e($product->status_1)) : '<span style="color: #2f8f2f;">មិនទាន់ដក</span>' !!}</td>
                                    <td>{!! trim($product->remain_qty) !== '' ? ($remainQty == 0 ? '<span class="out-of-stock-text">អស់ពីស្តុក</span>' : ($remainQty < 1 ? '<span style="color: #2f8f2f;">មិនទាន់ដក</span>' : e($product->remain_qty))) : '<span style="color: #2f8f2f;">មិនទាន់ដក</span>' !!}</td>
                                    <td>{{ trim($product->total_weight) !== '' ? round((float) $product->total_weight) . 'g' : '0g' }}</td>
                                    <td>{!! trim($product->status_2) !== '' ? ((float) $product->status_2 < 1 ? '<span style="color: #2f8f2f;">មិនទាន់ដក</span>' : e(round((float) $product->status_2) . 'g')) : '<span style="color: #2f8f2f;">មិនទាន់ដក</span>' !!}</td>
                                    <td>{!! trim($product->remain_weight) !== '' ? ($remainWeight == 0 ? '<span class="out-of-stock-text">អស់ពីស្តុក</span>' : ($remainWeight < 1 ? '<span style="color: #2f8f2f;">មិនទាន់ដក</span>' : e(round((float) $product->remain_weight) . 'g'))) : '<span class="out-of-stock-text">អស់ពីស្តុក</span>' !!}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="10">មិនមានទិន្នន័យ</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                <div class="mt-3">
                    {{ $leafProducts->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function toggleOutOfStockRows(button) {
        const rows = document.querySelectorAll('table tbody tr[data-out-of-stock]');
        const isFiltering = button.getAttribute('data-filtering') === '1';

        rows.forEach(function (row) {
            const shouldShow = !isFiltering ? row.getAttribute('data-out-of-stock') === '1' : true;
            row.style.display = shouldShow ? '' : 'none';
        });

        button.setAttribute('data-filtering', isFiltering ? '0' : '1');
        button.textContent = isFiltering ? 'Show Only Out-of-Stock' : 'Show All Rows';
    }
</script>
@endsection
