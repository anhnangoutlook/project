@extends('layout')

@section('title', 'ទិន្នន័យស្តុកខែចាស់')

@section('content')
<style>
    .container { margin-left: 0 !important; padding-left: 0 !important; max-width: 100% !important; width: 100% !important; }
    .card { border-radius: 0.5rem; width: 100% !important; }
    .table-responsive { width: 100% !important; overflow-x: auto; }
    .table { table-layout: fixed; width: 100% !important; margin-bottom: 0; border-collapse: collapse; }
    .table-bordered th, .table-bordered td { border: 1px solid #000000 !important; text-align: center; font-size: 10px; font-family: 'AKbalthom Monstera', 'AKbalthom KhmerNew', 'Kantumruuy Pro', sans-serif; padding: 8px 4px; vertical-align: middle; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .table thead th:nth-child(1) { background-color: #ff6b6b; width: 10%; }
    .table thead th:nth-child(2) { background-color: #4ecdc4; width: 18%; }
    .table thead th:nth-child(3) { background-color: #45b7d1; width: 8%; }
    .table thead th:nth-child(4) { background-color: #96ceb4; width: 8%; }
    .table thead th:nth-child(5) { background-color: #ffeaa7; width: 8%; }
    .table thead th:nth-child(6) { background-color: #c1c2c3; width: 8%; }
    .table thead th:nth-child(7) { background-color: #fd79a8; width: 12%; }
    .table thead th:nth-child(8) { background-color: #a29bfe; width: 9%; }
    .table thead th:nth-child(9) { background-color: #fdcb6e; width: 8%; }
    .table thead th:nth-child(10) { background-color: #00b894; width: 11%; }
</style>

<div class="container">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center gap-3 mb-4 text-start justify-content-start">
                <button class="toggle-btn btn btn-outline-secondary" onclick="toggleSidebar()"><i data-lucide="menu"></i></button>
                <h1 class="mb-0">ទិន្នន័យស្តុកខែចាស់</h1>
            </div>

            <form action="{{ route('products.old-month-stock-table') }}" method="GET" class="mb-3">
                <div class="input-group">
                    <input type="month" name="month" class="form-control" value="{{ $monthKey ?? now()->format('Y-m') }}">
                    <button type="submit" class="btn btn-primary">រក្សាទុក</button>
                </div>
            </form>

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
                        @if(isset($leafProducts) && $leafProducts->count() > 0)
                            @foreach($leafProducts as $product)
                            <tr>
                                <td>{{ trim($product['category'] ?? '') !== '' ? $product['category'] : 'ទូទៅ' }}</td>
                                <td>{{ $product['name'] ?? '' }}</td>
                                <td>{{ $product['code_from'] ?? '' }}</td>
                                <td>{{ $product['code_to'] ?? '' }}</td>
                                <td>{{ trim($product['total_qty'] ?? '') !== '' ? $product['total_qty'] : 0 }}</td>
                                <td>{{ trim($product['status_1'] ?? '') !== '' ? ($product['status_1'] < 1 ? 'មិនទាន់ដក' : $product['status_1']) : 'មិនទាន់ដក' }}</td>
                                <td>{{ trim($product['remain_qty'] ?? '') !== '' ? ($product['remain_qty'] < 1 ? 'មិនទាន់ដក' : $product['remain_qty']) : 'មិនទាន់ដក' }}</td>
                                <td>{{ trim($product['total_weight'] ?? '') !== '' ? ((float) $product['total_weight'] < 1 ? 'មិនទាន់ដក' : round((float) $product['total_weight']) . 'g') : 'មិនទាន់ដក' }}</td>
                                <td>{{ trim($product['status_2'] ?? '') !== '' ? ((float) $product['status_2'] < 1 ? 'មិនទាន់ដក' : round((float) $product['status_2']) . 'g') : 'មិនទាន់ដក' }}</td>
                                <td>{{ trim($product['remain_weight'] ?? '') !== '' ? ((float) $product['remain_weight'] < 1 ? 'មិនទាន់ដក' : round((float) $product['remain_weight']) . 'g') : 'មិនទាន់ដក' }}</td>
                            </tr>
                            @endforeach
                        @else
                            <tr><td colspan="10">មិនមានទិន្នន័យ</td></tr>
                        @endif
                    </tbody>
                </table>
                <div class="mt-3">
                    {{ $leafProducts->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
            @if(isset($snapshots) && $snapshots->isNotEmpty())
            <div class="mt-4">
                <h5>ប្រវត្តិស្តុកប្រចាំខែ</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>ខែ</th>
                                <th>កាលបរិច្ឆេទ</th>
                                <th>សកម្មភាព</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($snapshots as $snapshot)
                            <tr>
                                <td>
                                    <a href="{{ route('products.old-month-stock-table', ['month' => $snapshot->month_key]) }}" class="text-primary fw-bold">
                                        {{ $snapshot->month_key }}
                                    </a>
                                </td>
                                <td>{{ $snapshot->snapshot_date }}</td>
                                <td>
                                    <form action="{{ route('products.old-month-stock-table.delete', $snapshot->month_key) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('លុបប្រវត្តិខែនេះ?')">លុប</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
