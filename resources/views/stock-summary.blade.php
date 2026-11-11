@extends('layout')

@section('content')
<style>
    .table-bordered th,
    .table-bordered td {
        border: 1px solid #000000 !important;
        text-align: center;
        font-family: 'AKbalthom Monstera', 'AKbalthom KhmerNew', 'Kantumruuy Pro', sans-serif;
        white-space: nowrap !important;
        vertical-align: middle;
    }
    nav p.text-sm.text-gray-700 {
        display: none !important;
    }
    .pagination div fieldset p,
    .pagination .flex-1.flex.justify-between + div p {
        display: none !important;
    }
    .container {
        margin-left: 0 !important;
        padding-left: 0 !important;
        max-width: 100% !important;
        width: 100% !important;
    }
    .table thead th {
        font-weight: 600;
    }
    .table thead th:nth-child(1) {
        background-color: #ff6b6b;
        width: 35%;
    }
    .table thead th:nth-child(2) {
        background-color: #4ecdc4;
        width: 15%;
    }
    .table thead th:nth-child(3) {
        background-color: #45b7d1;
        width: 15%;
    }
    .table thead th:nth-child(4) {
        background-color: #96ceb4;
        width: 15%;
    }
    .table thead th:nth-child(5) {
        background-color: #ffeaa7;
        width: 20%;
    }
    .main-content-outer {
        display: flex;
        width: 100%;
        align-items: flex-start;
    }
    .table-container-wrapper {
        flex: 1;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
</style>
<div class="container">
    <div class="d-flex align-items-center gap-3 mb-4 text-start justify-content-start">
        <button class="toggle-btn" onclick="toggleSidebar()"><i data-lucide="menu"></i></button>
        <h1 class="mb-0">សង្ខេបស្តុក</h1>
    </div>
    <form action="{{ route('products.stock.summary') }}" method="GET" class="mb-3">
        <div class="d-flex gap-2">
            <input type="text" name="search" class="form-control" placeholder="ស្វែងរកឈ្មោះទំនិញ" value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary">ស្វែងរក</button>
            <a href="{{ route('products.stock.summary') }}" class="btn btn-secondary">សម្អាត</a>
        </div>
    </form>
    <div class="table-container-wrapper">
        <table class="table table-striped table-bordered" style="width: 100% !important; table-layout: fixed; margin: 0;">
            <thead>
                <tr class="tr">
                    <th>ឈ្មោះទំនិញ</th>
                    <th>លេខកូដចាប់ពី</th>
                    <th>លេខកូដរហូតដល់</th>
                    <th>ចំនួនសរុប</th>
                    <th>ទម្ងន់សរុប</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stockSummaries as $summary)
                <tr>
                    <td style="white-space: nowrap !important; text-align: center; padding-left: 15px;">{{ $summary->name }}</td>
                    <td style="white-space: nowrap !important;">{{ $summary->start_code }}</td>
                    <td style="white-space: nowrap !important;">{{ $summary->end_code }}</td>
                    <td style="white-space: nowrap !important;">{{ number_format((float) $summary->total_qty, 0) }}</td>
                    <td style="white-space: nowrap !important;">{{ number_format($summary->total_weight, 0) }}g</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">មិនមានទិន្នន័យ</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3">
            {{ $stockSummaries->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('collapsed');
}
lucide.createIcons();
</script>
@endsection
