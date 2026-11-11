@extends('layout')

@section('content')
<style>
    input[type="checkbox"],
    select {
        border: none !important;
    }
    input[type="checkbox"] {
        width: 16px;
        height: 16px;
        padding: 0;
        margin: 0;
    }
    td:first-child,
    th:first-child {
        width: 40px;
        text-align: center;
        padding: 8px;
    }
    .table-bordered th,
    .table-bordered td {
        border: 1px solid #000000 !important;
        text-align: center;
        font-family: 'AKbalthom Monstera', 'AKbalthom KhmerNew', 'Kantumruuy Pro', sans-serif;
    }
    .date-column {
        display: none;
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
    }
    .nav-link {
        cursor: pointer;
    }
    .nav-link:hover {
        background-color: #f8f9fa;
    }
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    .btn {
        transition: all 0.2s ease-in-out;
    }
    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .card {
        border-radius: 0.5rem;
    }
    .form-control:focus,
    .form-select:focus {
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    .modal-content {
        border-radius: 0.5rem;
    }
    .table thead th {
        background-color: #f8f9fa;
        font-weight: 600;
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
    .badge {
        font-weight: 500;
    }
    .cell-red-alert td {
        background-color: #ffe6e6 !important;
    }
    .cell-yellow-alert td {
        background-color: #fff7e6 !important;
    }
    .highlighted-row {
        background-color: #d1e7dd !important;
        box-shadow: inset 0 0 0 2px #0f5132;
    }
</style>
<div class="container">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center gap-3 mb-4 text-start justify-content-start">
                <button class="toggle-btn btn btn-outline-secondary" onclick="toggleSidebar()"><i data-lucide="menu"></i></button>
                <h1 class="mb-0">ស្តុក</h1>
            </div>
            <div class="d-flex gap-2 mb-4 flex-wrap">
                <button type="button" class="btn btn-primary" id="add-product" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i data-lucide="plus"></i> បន្ថែមស្តុក
                </button>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#bulkDeleteModal" id="bulkDeleteBtn" style="display: none;">
                    <i data-lucide="trash-2"></i> លុបដែលបានជ្រើសរើស
                </button>
                <button type="button" class="btn btn-outline-secondary" onclick="toggleDateColumn()">
                    <i data-lucide="calendar"></i> 
                </button>
            </div>
            <ul class="nav nav-tabs mb-3" id="stockTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="regular-tab" data-bs-toggle="tab" data-bs-target="#regular" type="button" role="tab">
                        <i data-lucide="package"></i> ស្តុកឆៅ
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="kg-tab" data-bs-toggle="tab" data-bs-target="#kg" type="button" role="tab">
                        <i data-lucide="scale"></i> ស្តុកច្នៃ
                    </button>
                </li>
            </ul>
            <div class="tab-content" id="stockTabsContent">
                <div class="tab-pane fade show active" id="regular" role="tabpanel">
                    <form action="{{ route('products.stock') }}" method="GET" class="mb-3">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="ស្វែងរកឈ្មោះទំនិញ" value="{{ request('search') }}">
                            <select name="category" class="form-select">
                                <option value="">ទាំងអស់</option>
                                @foreach($productNames as $name)
                                    <option value="{{ $name }}" {{ request('category') == $name ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary">
                                <i data-lucide="search"></i> ស្វែងរក
                            </button>
                            <a href="{{ route('products.stock') }}" class="btn btn-outline-secondary">
                                <i data-lucide="refresh-cw"></i> សម្អាត
                            </a>
                        </div>
                    </form>
                    <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'code', 'direction' => request('sort') == 'code' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" style="color: #000000; text-decoration: none;">
                                    លេខកូដ
                                    @if(request('sort') == 'code')
                                        @if(request('direction') == 'asc') ↑ @else ↓ @endif
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => request('sort') == 'name' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" style="color: #000000; text-decoration: none;">
                                    ឈ្មោះទំនិញ
                                    @if(request('sort') == 'name')
                                        @if(request('direction') == 'asc') ↑ @else ↓ @endif
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'weight', 'direction' => request('sort') == 'weight' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" style="color: #000000; text-decoration: none;">
                                    ទម្ងន់
                                    @if(request('sort') == 'weight')
                                        @if(request('direction') == 'asc') ↑ @else ↓ @endif
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'qty1', 'direction' => request('sort') == 'qty1' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" style="color: #000000; text-decoration: none;">
                                    ចំនួន
                                    @if(request('sort') == 'qty1')
                                        @if(request('direction') == 'asc') ↑ @else ↓ @endif
                                    @endif
                                </a>
                            </th>
                            <th class="date-column">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'imported_at', 'direction' => request('sort') == 'imported_at' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" style="color: #000000; text-decoration: none;">
                                    កាលបរិច្ឆេទ
                                    @if(request('sort') == 'imported_at')
                                        @if(request('direction') == 'asc') ↑ @else ↓ @endif
                                    @endif
                                </a>
                            </th>
                            <th>សកម្មភាព</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stocks as $item)
                        @php
                            $stockQty = (float) $item->qty1;
                        @endphp
                        <tr class="@if($stockQty == 0) cell-red-alert @elseif($stockQty > 0 && $stockQty <= 3) cell-yellow-alert @endif @if(request('search') && (strtolower(trim((string) $item->name)) === strtolower(trim((string) request('search'))) || strtolower(trim((string) $item->code)) === strtolower(trim((string) request('search'))))) highlighted-row @endif">
                            <td><input type="checkbox" class="stock-checkbox" value="{{ $item->id }}"></td>
                            <td>{{ $item->code }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->weight }}</td>
                            <td>{{ $item->qty1 }}</td>
                            <td class="date-column">{{ \Carbon\Carbon::parse($item->imported_at)->format('d/m/Y H:i') }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-warning edit-btn" data-bs-toggle="modal" data-bs-target="#editModal" data-id="{{ $item->id }}" data-name="{{ $item->name }}" data-code="{{ $item->code }}" data-weight="{{ $item->weight }}" data-qty1="{{ $item->qty1 }}" data-imported-at="{{ $item->imported_at }}">
                                    <i data-lucide="edit-2"></i> កែប្រែ
                                </button>
                                <button type="button" class="btn btn-sm btn-danger delete-btn" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="{{ $item->id }}">
                                    <i data-lucide="trash"></i> លុប
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $stocks->appends(request()->except('stock_page'))->links('pagination::bootstrap-5') }}
            </div>
        </div>
        <div class="tab-pane fade" id="kg" role="tabpanel">
            <form action="{{ route('products.stock') }}" method="GET" class="mb-3">
                <div class="input-group">
                    <input type="text" name="kg_search" class="form-control" placeholder="ស្វែងរកឈ្មោះទំនិញ" value="{{ request('kg_search') }}">
                    <select name="kg_category" class="form-select">
                        <option value="">ទាំងអស់</option>
                        @foreach($stockKgNames as $name)
                            <option value="{{ $name }}" {{ request('kg_category') == $name ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="search"></i> ស្វែងរក
                    </button>
                    <a href="{{ route('products.stock') }}" class="btn btn-outline-secondary">
                        <i data-lucide="refresh-cw"></i> សម្អាត
                    </a>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAllKg"></th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['kg_sort' => 'code', 'kg_direction' => request('kg_sort') == 'code' && request('kg_direction') == 'asc' ? 'desc' : 'asc']) }}" style="color: #000000; text-decoration: none;">
                                    លេខកូដ
                                    @if(request('kg_sort') == 'code')
                                        @if(request('kg_direction') == 'asc') ↑ @else ↓ @endif
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['kg_sort' => 'name', 'kg_direction' => request('kg_sort') == 'name' && request('kg_direction') == 'asc' ? 'desc' : 'asc']) }}" style="color: #000000; text-decoration: none;">
                                    ឈ្មោះទំនិញ
                                    @if(request('kg_sort') == 'name')
                                        @if(request('kg_direction') == 'asc') ↑ @else ↓ @endif
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['kg_sort' => 'ct', 'kg_direction' => request('kg_sort') == 'ct' && request('kg_direction') == 'asc' ? 'desc' : 'asc']) }}" style="color: #000000; text-decoration: none;">
                                    CT
                                    @if(request('kg_sort') == 'ct')
                                        @if(request('kg_direction') == 'asc') ↑ @else ↓ @endif
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['kg_sort' => 'unit', 'kg_direction' => request('kg_sort') == 'unit' && request('kg_direction') == 'asc' ? 'desc' : 'asc']) }}" style="color: #000000; text-decoration: none;">
                                    Unit
                                    @if(request('kg_sort') == 'unit')
                                        @if(request('kg_direction') == 'asc') ↑ @else ↓ @endif
                                    @endif
                                </a>
                            </th>
                            <th>សកម្មភាព</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stockkgs as $item)
                        <tr>
                            <td><input type="checkbox" class="stockkg-checkbox" value="{{ $item->id }}"></td>
                            <td>{{ $item->code }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->ct }}</td>
                            <td>{{ $item->unit }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-warning edit-kg-btn" data-bs-toggle="modal" data-bs-target="#editKgModal" data-id="{{ $item->id }}" data-code="{{ $item->code }}" data-name="{{ $item->name }}" data-ct="{{ $item->ct }}" data-unit="{{ $item->unit }}">
                                    <i data-lucide="edit-2"></i> កែប្រែ
                                </button>
                                <button type="button" class="btn btn-sm btn-danger delete-kg-btn" data-bs-toggle="modal" data-bs-target="#deleteKgModal" data-id="{{ $item->id }}">
                                    <i data-lucide="trash"></i> លុប
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $stockkgs->appends(request()->except('stockkg_page'))->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
    </div>
</div>

<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">បន្ថែមស្តុកថ្មី</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('products.stock.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">លេខកូដ</label>
                        <input type="text" name="code" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ឈ្មោះទំនិញ</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ទម្ងន់</label>
                        <input type="text" name="weight" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ចំនួន</label>
                        <input type="text" name="qty1" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">កាលបរិច្ឆេទ</label>
                        <input type="datetime-local" name="imported_at" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">បិទ</button>
                    <button type="submit" class="btn btn-primary">រក្សាទុក</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">កែប្រែស្តុក</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('products.stock.update', ['id' => ':id']) }}" method="POST" id="editForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="id" id="editId">
                    <div class="mb-3">
                        <label class="form-label">លេខកូដ</label>
                        <input type="text" name="code" id="editCode" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ឈ្មោះទំនិញ</label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ទម្ងន់</label>
                        <input type="text" name="weight" id="editWeight" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ចំនួន</label>
                        <input type="text" name="qty1" id="editQty1" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">កាលបរិច្ឆេទ</label>
                        <input type="datetime-local" name="imported_at" id="editImportedAt" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">បិទ</button>
                    <button type="submit" class="btn btn-primary">កែប្រែ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">លុបស្តុក</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('products.stock.destroy', ['id' => ':id']) }}" method="POST" id="deleteForm">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <input type="hidden" name="id" id="deleteId">
                    <p>តើអ្នកប្រាកដថាចង់លុបស្តុកនេះមែនទេ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">បិទ</button>
                    <button type="submit" class="btn btn-danger">លុប</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="bulkDeleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">លុបស្តុកដែលបានជ្រើសរើស</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('products.stock.bulkDestroy') }}" method="POST" id="bulkDeleteForm">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <input type="hidden" name="ids" id="bulkDeleteIds">
                    <p>តើអ្នកប្រាកដថាចង់លុបស្តុកដែលបានជ្រើសរើសទាំងអស់មែនទេ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">បិទ</button>
                    <button type="submit" class="btn btn-danger">លុប</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editKgModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">កែប្រែស្តុកច្នៃ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('products.stock.update', ['id' => ':id']) }}" method="POST" id="editKgForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="id" id="editKgId">
                    <div class="mb-3">
                        <label class="form-label">លេខកូដ</label>
                        <input type="text" name="code" id="editKgCode" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ឈ្មោះទំនិញ</label>
                        <input type="text" name="name" id="editKgName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">CT</label>
                        <input type="text" name="ct" id="editKgCt" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Unit</label>
                        <input type="text" name="unit" id="editKgUnit" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">បិទ</button>
                    <button type="submit" class="btn btn-primary">កែប្រែ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteKgModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">លុបស្តុកច្នៃ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('products.stock.destroy', ['id' => ':id']) }}" method="POST" id="deleteKgForm">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <input type="hidden" name="id" id="deleteKgId">
                    <p>តើអ្នកប្រាកដថាចង់លុបស្តុកច្នៃនេះមែនទេ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">បិទ</button>
                    <button type="submit" class="btn btn-danger">លុប</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var addProductButton = document.getElementById('add-product');
    var createModal = document.getElementById('createModal');
    var hash = window.location.hash;

    if (hash === '#add-product' && addProductButton && createModal) {
        setTimeout(function() {
            addProductButton.scrollIntoView({ behavior: 'smooth', block: 'center' });
            var modal = new bootstrap.Modal(createModal);
            modal.show();
        }, 200);
    }

    document.querySelectorAll('.edit-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            document.getElementById('editId').value = this.dataset.id;
            document.getElementById('editCode').value = this.dataset.code;
            document.getElementById('editName').value = this.dataset.name;
        });
    });

    document.querySelectorAll('.delete-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            document.getElementById('deleteId').value = this.dataset.id;
            document.getElementById('deleteForm').action = '/stock/' + this.dataset.id;
        });
    });

    document.getElementById('selectAll').addEventListener('change', function() {
        var selectAll = this;
        document.querySelectorAll('.stock-checkbox').forEach(function(checkbox) {
            checkbox.checked = selectAll.checked;
        });
        toggleBulkDeleteButton();
    });

    var isMouseDown = false;
    var startCheckbox = null;

    document.querySelectorAll('.stock-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            toggleBulkDeleteButton();
            updateSelectAllCheckbox();
        });
        
        checkbox.addEventListener('mousedown', function(e) {
            isMouseDown = true;
            startCheckbox = this;
            e.preventDefault();
        });

        checkbox.addEventListener('mouseenter', function() {
            if (isMouseDown && startCheckbox) {
                this.checked = !this.checked;
                toggleBulkDeleteButton();
                updateSelectAllCheckbox();
            }
        });
    });

    document.addEventListener('mouseup', function() {
        isMouseDown = false;
        startCheckbox = null;
    });

    document.getElementById('bulkDeleteBtn').addEventListener('click', function() {
        var selectedIds = [];
        document.querySelectorAll('.stock-checkbox:checked').forEach(function(checkbox) {
            selectedIds.push(checkbox.value);
        });
        document.getElementById('bulkDeleteIds').value = selectedIds.join(',');
    });

    document.querySelectorAll('.edit-kg-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            document.getElementById('editKgId').value = this.dataset.id;
            document.getElementById('editKgCode').value = this.dataset.code;
            document.getElementById('editKgName').value = this.dataset.name;
            document.getElementById('editKgCt').value = this.dataset.ct;
            document.getElementById('editKgUnit').value = this.dataset.unit;
            document.getElementById('editKgForm').action = '/stock/' + this.dataset.id;
        });
    });

    document.querySelectorAll('.delete-kg-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            document.getElementById('deleteKgId').value = this.dataset.id;
            document.getElementById('deleteKgForm').action = '/stock/' + this.dataset.id;
        });
    });

    document.getElementById('selectAllKg').addEventListener('change', function() {
        var selectAll = this;
        document.querySelectorAll('.stockkg-checkbox').forEach(function(checkbox) {
            checkbox.checked = selectAll.checked;
        });
    });

    var isMouseDownKg = false;
    var startCheckboxKg = null;

    document.querySelectorAll('.stockkg-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            updateSelectAllKgCheckbox();
        });
        
        checkbox.addEventListener('mousedown', function(e) {
            isMouseDownKg = true;
            startCheckboxKg = this;
            e.preventDefault();
        });

        checkbox.addEventListener('mouseenter', function() {
            if (isMouseDownKg && startCheckboxKg) {
                this.checked = !this.checked;
                updateSelectAllKgCheckbox();
            }
        });
    });

    document.addEventListener('mouseup', function() {
        isMouseDownKg = false;
        startCheckboxKg = null;
    });
});

function toggleBulkDeleteButton() {
    var checkedCount = document.querySelectorAll('.stock-checkbox:checked').length;
    document.getElementById('bulkDeleteBtn').style.display = checkedCount > 0 ? 'inline-block' : 'none';
}

function updateSelectAllCheckbox() {
    var allCheckboxes = document.querySelectorAll('.stock-checkbox');
    var checkedCount = document.querySelectorAll('.stock-checkbox:checked').length;
    document.getElementById('selectAll').checked = allCheckboxes.length === checkedCount;
}

function updateSelectAllKgCheckbox() {
    var allCheckboxes = document.querySelectorAll('.stockkg-checkbox');
    var checkedCount = document.querySelectorAll('.stockkg-checkbox:checked').length;
    document.getElementById('selectAllKg').checked = allCheckboxes.length === checkedCount;
}

function toggleDateColumn() {
    var dateColumns = document.querySelectorAll('.date-column');
    dateColumns.forEach(function(column) {
        if (column.style.display === 'none' || column.style.display === '') {
            column.style.display = 'table-cell';
        } else {
            column.style.display = 'none';
        }
    });
}
</script>
@endsection
