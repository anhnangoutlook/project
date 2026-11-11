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
    .table thead th:nth-child(1) { background-color: #ff6b6b; width: 20%; }
    .table thead th:nth-child(2) { background-color: #4ecdc4; width: 20%; }
    .table thead th:nth-child(3) { background-color: #45b7d1; width: 30%; }
    .table thead th:nth-child(4) { background-color: #96ceb4; width: 15%; }
    .table thead th:nth-child(5) { background-color: #ffeaa7; width: 15%; }
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
    .row-dark-red-alert td {
        background-color: #b02a37 !important;
        color: #ffffff !important;
        font-weight: bold;
    }
</style>

<div class="container">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center gap-3 mb-4 text-start justify-content-start">
                <button class="toggle-btn btn btn-outline-secondary" onclick="toggleSidebar()"><i data-lucide="menu"></i></button>
                <h1 class="mb-0">បញ្ជីទំនិញអស់ពីស្តុក</h1>
            </div>
            <form action="{{ route('products.out-of-stock-table') }}" method="GET" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="ស្វែងរកឈ្មោះទំនិញ ឬលេខកូដ" value="{{ request('search') }}">
                    <select name="category" class="form-select" onchange="this.form.submit()">
                        <option value="">ទាំងអស់</option>
                        @foreach($productNames as $name)
                            @if(trim($name) !== '')
                                <option value="{{ $name }}" {{ request('category') == $name ? 'selected' : '' }}>{{ $name }}</option>
                            @endif
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="filter"></i> ស្វែងរក
                    </button>
                    <a href="{{ route('products.out-of-stock-table') }}" class="btn btn-outline-secondary">
                        <i data-lucide="refresh-cw"></i> សម្អាត
                    </a>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>ប្រភេទ</th>
                            <th>លេខកូដ</th>
                            <th>ឈ្មោះទំនិញ</th>
                            <th>ទម្ងន់</th>
                            <th>ចំនួន</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leafProducts as $item)
                            <tr class="row-dark-red-alert">
                                <td>{{ $item->category ?? '' }}</td>
                                <td>{{ $item->code ?? '' }}</td>
                                <td>{{ $item->name ?? '' }}</td>
                                <td>{{ $item->weight ?? 0 }}</td>
                                <td>
                                    {{ $item->qty1 ?? 0 }}
                                    <span class="badge bg-danger">អស់ពីស្តុក</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">មិនមានទិន្នន័យ</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-3">
                    {{ $leafProducts->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
