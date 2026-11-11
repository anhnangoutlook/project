@extends('layout')

@section('title', 'ដកស្តុក')

@section('content')
<style>
    .table-bordered th,
    .table-bordered td {
        border: 1px solid #000000 !important;
        text-align: center;
        font-family: 'AKbalthom Monstera', 'AKbalthom KhmerNew', 'Kantumruuy Pro', sans-serif;
        white-space: nowrap;
        padding: 12px 8px !important;
        vertical-align: middle;
    }
    .container {
        margin-left: 0 !important;
        padding-left: 0 !important;
    }
    .card {
        border-radius: 0.5rem;
    }
    .form-control:focus,
    .form-select:focus {
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    .table thead th {
        font-weight: 600;
        text-align: center;
        font-family: 'AKbalthom Monstera', 'AKbalthom KhmerNew', 'Kantumruuy Pro', sans-serif;
    }
    .table thead th:nth-child(1) {
        background-color: #ff6b6b;
    }
    .table thead th:nth-child(2) {
        background-color: #4ecdc4;
    }
    .table thead th:nth-child(3) {
        background-color: #45b7d1;
    }
    .table thead th:nth-child(4) {
        background-color: #96ceb4;
    }
    .table thead th:nth-child(5) {
        background-color: #ffeaa7;
    }
    .table thead th:nth-child(6) {
        background-color: #fd79a8;
    }
    .table thead th:nth-child(7) {
        background-color: #a29bfe;
    }
    .toast-container {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 9999;
    }
    .toast {
        min-width: 300px;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        animation: slideIn 0.3s ease-out;
    }
    .toast-success {
        background-color: #00b894;
    }
    .toast-error {
        background-color: #d63031;
    }
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    @keyframes fadeOut {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
        }
    }
    .nav-tabs-wrapper {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }
    .tab-link {
        padding: 10px 20px;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        text-decoration: none;
        color: #6c757d;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }
    .tab-link:hover {
        background-color: #e9ecef;
        color: #495057;
    }
    .tab-link.active-tab {
        background-color: #0d6efd;
        color: #ffffff;
        border-color: #0d6efd;
    }
    /* Custom checkbox styling */
    .form-check-input {
        width: 24px !important;
        height: 24px !important;
        cursor: pointer;
        border: 2px solid #000000 !important;
        border-radius: 4px;
    }
    .form-check-input:checked {
        background-color: #ff0000 !important;
        border-color: #ff0000 !important;
    }
    .search-box {
        margin-bottom: 0.75rem;
    }
    .search-box .input-group .form-control {
        border-top-right-radius: 0.25rem;
        border-bottom-right-radius: 0.25rem;
    }
</style>
<div class="container">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center gap-3 mb-4 text-start justify-content-start">
                <button class="toggle-btn btn btn-outline-secondary" onclick="toggleSidebar()"><i data-lucide="menu"></i></button>
                <h1 class="mb-0">ដកស្តុក</h1>
            </div>
            <div class="nav-tabs-wrapper">
                <a href="{{ route('products.remove_stock', ['type' => 'standard']) }}" class="tab-link {{ $type == 'standard' || $type == null ? 'active-tab' : '' }}">
                    <i class="fas fa-box"></i> ស្តុកឆៅ
                </a>
                <a href="{{ route('products.remove_stock', ['type' => 'kg']) }}" class="tab-link {{ $type == 'kg' ? 'active-tab' : '' }}">
                    <i class="fas fa-balance-scale"></i> ស្តុកច្នៃ
                </a>
            </div>
            @if(request('type', 'standard') == 'standard')
            <form action="{{ route('products.remove_stock') }}" method="GET" class="mb-3">
                <input type="hidden" name="type" value="standard">
                <div class="input-group">
                    <select name="category" class="form-select" onchange="this.form.submit()">
                        <option value="">ទាំងអស់</option>
                        @foreach($productNames as $name)
                            <option value="{{ $name }}" {{ request('category') == $name ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    <a href="{{ route('products.remove_stock', ['type' => 'standard']) }}" class="btn btn-outline-secondary">
                        <i data-lucide="refresh-cw"></i> សម្អាត
                    </a>
                </div>
            </form>
            <div class="search-box mb-3">
                <div class="input-group">
                    <input type="text" id="searchInputStandard" class="form-control" placeholder="ស្វែងរកលេខកូដ ឬ ឈ្មោះ">
                    <button class="btn btn-outline-secondary" type="button" onclick="searchTable('standard')"><i data-lucide="search"></i> ស្វែងរក</button>
                    <button class="btn btn-light" type="button" onclick="clearSearch('standard')">លុប</button>
                </div>
            </div>
            <form action="{{ route('products.remove_stock.store') }}" method="POST" class="mb-3">
                @csrf
                <input type="hidden" name="type" value="standard">
                <div class="table-responsive">
                    <table id="standardTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ជ្រើសរើស</th>
                                <th>ថ្ងៃខែឆ្នាំ</th>
                                <th>ចំនួនដក</th>
                                <th>លេខកូដ</th>
                                <th>ឈ្មោះ</th>
                                <th>ទម្ងន់</th>
                                <th>នៅសល់</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($products && $products->count() > 0)
                                @foreach($products as $item)
                                <tr @if($item->qty1 == 0) style="opacity: 0.5;" @endif>
                                    <td><input type="checkbox" class="form-check-input" name="selected[]" value="{{ $item->id }}" onchange="toggleRow(this)" @if($item->qty1 == 0) disabled @endif></td>
                                    <input type="hidden" name="id[]" value="{{ $item->id }}">
                                    <td><input type="date" class="form-control" name="date[{{ $item->id }}]" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" @if($item->qty1 == 0) disabled @endif></td>
                                    <td><input type="number" class="form-control" name="qty_to_remove[]" value="{{ $item->qty1 == 0 ? 1 : 0 }}" min="0" max="1" onchange="toggleRemaining(this)" @if($item->qty1 == 0) disabled readonly @endif></td>
                                    <td><input type="text" class="form-control" name="code[]" value="{{ $item->code }}" readonly></td>
                                    <td><input type="text" class="form-control" name="name[]" value="{{ $item->name }}" readonly></td>
                                    <td><input type="text" class="form-control" name="weight[]" value="{{ $item->weight }}" readonly></td>
                                    <td><input type="number" class="form-control" name="remaining[]" value="{{ $item->qty1 }}" min="0" max="1" readonly></td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7">សូមជ្រើសរើសប្រភេទទំនិញ</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="check"></i> រក្សាទុក
                    </button>
                    <button type="reset" class="btn btn-outline-secondary">
                        <i data-lucide="refresh-cw"></i> សម្អាត
                    </button>
                    <a href="{{ route('products.stock-removal-table') }}" class="btn btn-info">
                        <i data-lucide="eye"></i> មើលទិន្នន័យដកស្តុក
                    </a>
                </div>
            </form>
            @endif

            @if(request('type') == 'kg')
            <form action="{{ route('products.remove_stock') }}" method="GET" class="mb-3">
                <input type="hidden" name="type" value="kg">
                <div class="input-group">
                    <select name="category" class="form-select" onchange="this.form.submit()">
                        <option value="">ទាំងអស់</option>
                        @foreach($productNames as $name)
                            <option value="{{ $name }}" {{ request('category') == $name ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    <a href="{{ route('products.remove_stock', ['type' => 'kg']) }}" class="btn btn-outline-secondary">
                        <i data-lucide="refresh-cw"></i> សម្អាត
                    </a>
                </div>
            </form>
            <div class="search-box mb-3">
                <div class="input-group">
                    <input type="text" id="searchInputKg" class="form-control" placeholder="ស្វែងរកលេខកូដ ឬ ឈ្មោះ">
                    <button class="btn btn-outline-secondary" type="button" onclick="searchTable('kg')"><i data-lucide="search"></i> ស្វែងរក</button>
                    <button class="btn btn-light" type="button" onclick="clearSearch('kg')">លុប</button>
                </div>
            </div>
            <form action="{{ route('products.remove_stock.store') }}" method="POST" class="mb-3">
                @csrf
                <input type="hidden" name="type" value="kg">
                <div class="table-responsive">
                    <table id="kgTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ជ្រើសរើស</th>
                                <th>ថ្ងៃខែឆ្នាំ</th>
                                <th>ចំនួនដក</th>
                                <th>លេខកូដ</th>
                                <th>ឈ្មោះ</th>
                                <th>ទម្ងន់</th>
                                <th>នៅសល់</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($products && $products->count() > 0)
                                @foreach($products as $item)
                                <tr @if($item->qty1 == 0) style="opacity: 0.5;" @endif>
                                    <td><input type="checkbox" class="form-check-input" name="selected[]" value="{{ $item->id }}" onchange="toggleRow(this)" @if($item->qty1 == 0) disabled @endif></td>
                                    <input type="hidden" name="id[]" value="{{ $item->id }}">
                                    <td><input type="date" class="form-control" name="date[{{ $item->id }}]" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" @if($item->qty1 == 0) disabled @endif></td>
                                    <td><input type="number" class="form-control" name="qty_to_remove[]" value="{{ $item->qty1 == 0 ? 1 : 0 }}" min="0" max="1" onchange="toggleRemaining(this)" @if($item->qty1 == 0) disabled readonly @endif></td>
                                    <td><input type="text" class="form-control" name="code[]" value="{{ $item->code }}" readonly></td>
                                    <td><input type="text" class="form-control" name="name[]" value="{{ $item->name }}" readonly></td>
                                    <td><input type="text" class="form-control" name="weight[]" value="{{ $item->weight }}" readonly></td>
                                    <td><input type="number" class="form-control" name="remaining[]" value="{{ $item->qty1 }}" min="0" max="1" readonly></td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7">សូមជ្រើសរើសប្រភេទទំនិញ</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="check"></i> រក្សាទុក
                    </button>
                    <button type="reset" class="btn btn-outline-secondary">
                        <i data-lucide="refresh-cw"></i> សម្អាត
                    </button>
                    <a href="{{ route('products.stock-removal-table') }}" class="btn btn-info">
                        <i data-lucide="eye"></i> មើលទិន្នន័យដកស្តុក
                    </a>
                </div>
            </form>
            @endif

            @if($removedStock && $removedStock->count() > 0)
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h5 class="mb-3">ប្រវត្តិដកស្តុក</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>លេខកូដ</th>
                                    <th>ឈ្មោះ</th>
                                    <th>ទម្ងន់</th>
                                    <th>ចំនួនដក</th>
                                    <th>សកម្មភាព</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($removedStock as $item)
                                <tr>
                                    <td>{{ $item->code }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->weight }}</td>
                                    <td>{{ $item->qty_removed }}</td>
                                    <td>
                                        <a href="{{ route('products.remove_stock.recover', $item->id) }}" class="btn btn-success btn-sm">
                                            <i data-lucide="refresh-cw"></i> ទាញទិន្នន័យមកវិញ
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@php
    $successMessage = session('success');
    $errorMessage = session('error');
@endphp

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('expanded');
    }

    function toggleRemaining(input) {
        const row = input.closest('tr');
        const remainingInput = row.querySelector('input[name="remaining[]"]');
        const checkbox = row.querySelector('input[name="selected[]"]');

        if (input.value == 1) {
            remainingInput.value = 0;
            checkbox.checked = true;
            row.style.backgroundColor = '#fff3cd';
        } else if (input.value == 0) {
            remainingInput.value = 1;
            checkbox.checked = false;
            row.style.backgroundColor = '';
        }
    }

    function toggleRow(checkbox) {
        const row = checkbox.closest('tr');
        const qtyInput = row.querySelector('input[name="qty_to_remove[]"]');
        const remainingInput = row.querySelector('input[name="remaining[]"]');

        if (checkbox.checked) {
            qtyInput.value = 1;
            remainingInput.value = 0;
            row.style.backgroundColor = '#fff3cd';
        } else {
            qtyInput.value = 0;
            remainingInput.value = 1;
            row.style.backgroundColor = '';
        }
    }

    function searchTable(type) {
        const queryInput = type === 'kg' ? document.getElementById('searchInputKg') : document.getElementById('searchInputStandard');
        if (!queryInput) return;
        const q = queryInput.value.trim().toLowerCase();
        const table = document.getElementById(type === 'kg' ? 'kgTable' : 'standardTable');
        if (!table) return;
        const rows = table.tBodies[0].rows;
        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            // code and name are inputs inside td
            const codeInput = row.querySelector('input[name="code[]"]');
            const nameInput = row.querySelector('input[name="name[]"]');
            let code = '';
            let name = '';
            if (codeInput) code = String(codeInput.value || '').toLowerCase();
            else {
                const td = row.cells[3]; if (td) code = String(td.textContent||'').toLowerCase();
            }
            if (nameInput) name = String(nameInput.value || '').toLowerCase();
            else {
                const td = row.cells[4]; if (td) name = String(td.textContent||'').toLowerCase();
            }

            if (q === '' || code.indexOf(q) !== -1 || name.indexOf(q) !== -1) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    }

    function clearSearch(type) {
        const queryInput = type === 'kg' ? document.getElementById('searchInputKg') : document.getElementById('searchInputStandard');
        if (queryInput) queryInput.value = '';
        const table = document.getElementById(type === 'kg' ? 'kgTable' : 'standardTable');
        if (!table) return;
        const rows = table.tBodies[0].rows;
        for (let i = 0; i < rows.length; i++) rows[i].style.display = '';
    }

    // Display session messages as toasts using global showToast function
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            if (window.showToast) {
                window.showToast('{{ session('success') }}', 'success');
            }
        @endif

        @if(session('error'))
            if (window.showToast) {
                window.showToast('{{ session('error') }}', 'error');
            }
        @endif
    });

    lucide.createIcons();
</script>
@endsection
