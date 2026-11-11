@extends('layout')

@section('title', 'តារាងដកស្តុក')

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
    .table thead th:nth-child(1) { background-color: #ff6b6b; width: 20%; }
    .table thead th:nth-child(2) { background-color: #4ecdc4; width: 25%; }
    .table thead th:nth-child(3) { background-color: #45b7d1; width: 15%; }
    .table thead th:nth-child(4) { background-color: #96ceb4; width: 15%; }
    .table thead th:nth-child(5) { background-color: #ffeaa7; width: 25%; }
    nav p.text-sm.text-gray-700,
    .pagination div fieldset p,
    .pagination .flex-1.flex.justify-between + div p {
        display: none !important;
    }
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
</style>

<div class="container">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center gap-3 mb-4 text-start justify-content-start">
                <button class="toggle-btn btn btn-outline-secondary" onclick="toggleSidebar()"><i data-lucide="menu"></i></button>
                <h1 class="mb-0">តារាងដកស្តុក</h1>
            </div>
            <form action="{{ route('products.stock-removal-table') }}" method="GET" class="mb-3">
                <div class="input-group">
                    <label class="input-group-text" for="filterMonth">ខែ និងឆ្នាំ</label>
                    <input type="month" id="filterMonth" name="filter_month" class="form-control" value="{{ $filterMonth }}">
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="filter"></i> ស្វែងរក
                    </button>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>លេខកូដ</th>
                            <th>ឈ្មោះទំនិញ</th>
                            <th>ចំនួនដក</th>
                            <th>ទម្ងន់ដក</th>
                            <th>កាលបរិច្ឆេទ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($removedStock as $item)
                            <tr>
                                <td>{{ $item->code }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->qty_removed }}</td>
                                <td>{{ $item->weight }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">មិនមានទិន្នន័យ</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-3">
                    {{ $removedStock->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
