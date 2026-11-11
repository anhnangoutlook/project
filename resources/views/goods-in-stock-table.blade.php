@extends('layout')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    :root {
        --table-border-width: 1px;
        --table-border-style: solid;
        --table-border-color: #000;
        --table-font-size: 14px;
        --table-cell-padding: 10px;
    }
    .cell-red-alert { background-color: #ffdddd; }
    .cell-yellow-alert { background-color: #fff5cc; }
    table.table { width: 100%; border-collapse: collapse; table-layout: auto; }
    table.table th, table.table td { border: var(--table-border-width) var(--table-border-style) var(--table-border-color); padding: var(--table-cell-padding); text-align: center; font-size: var(--table-font-size); }
    table.table { border: var(--table-border-width) var(--table-border-style) var(--table-border-color); }
    table.table thead th { font-weight: 700; font-size: calc(var(--table-font-size) + 1px); }
    #toastContainer {
        position: fixed;
        right: 20px;
        bottom: 20px;
        z-index: 1080;
        display: flex;
        flex-direction: column;
        gap: 8px;
        align-items: flex-end;
        pointer-events: none;
    }
    .toast-item {
        min-width: 220px;
        max-width: 360px;
        background: #fff;
        color: #222;
        border-radius: 6px;
        padding: 10px 12px;
        box-shadow: 0 6px 18px rgba(0,0,0,0.12);
        pointer-events: auto;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
    }
    .table thead th:nth-child(1) {
        background-color: #ff6b6b;
    }
    .table thead th:nth-child(2) {
        background-color: #ffd93d;
    }
    .table thead th:nth-child(3) {
        background-color: #6bc1ff;
    }
    .table thead th:nth-child(4) {
        background-color: #6bff95;
    }
    .table thead th:nth-child(5) {
        background-color: #d36bff;
    }
    .table thead th:nth-child(6) {
        background-color: #ff6b6b;
    }

    .toast-item.success { border-left: 4px solid #28a745; }
    .toast-item.error { border-left: 4px solid #dc3545; }
    .toast-item.info { border-left: 4px solid #0d6efd; }
    .toast-close {
        margin-left: auto;
        background: transparent;
        border: none;
        cursor: pointer;
        color: #666;
        font-size: 16px;
        line-height: 1;
    }
    .product-name-picker {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .product-name-picker .form-control {
        border: 1px solid #ced4da;
        border-radius: 8px;
        padding: 10px 12px;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.04);
    }
    .product-name-picker .form-control:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
    }
    .product-name-picker .hint-text {
        font-size: 12px;
        color: #6c757d;
    }
</style>
<div class="container">
    <div class="card">
        <div class="card-body">
            <h1>តារាងទំនិញចូលស្តុក</h1>
            <div class="mb-3 d-flex align-items-center gap-2 flex-wrap">
                <button class="toggle-btn btn btn-outline-secondary" onclick="toggleSidebar()"><i data-lucide="menu"></i></button>
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importGoodsModal">នាំចូលទិន្នន័យ</button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bulkGoodsModal">បន្ថែមទិន្នន័យច្រើន</button>
                <button type="button" class="btn btn-success" id="openCreateBtn">បន្ថែមទិន្នន័យមួយ</button>
                <a href="{{ route('products.stock') }}#add-product" class="btn btn-success">
                    <i data-lucide="package-plus"></i> មើល/បន្ថែមទំនិញថ្មី
                </a>
                <a href="{{ route('products.stock') }}#add-product" class="btn btn-primary">
                    <i data-lucide="plus-circle"></i> បន្ថែមទំនិញថ្មី
                </a>
            </div>
            <div class="table-responsive mt-4">
                <table class="table">
                    <thead>
                        <tr>
                            <th>លេខកូដ</th>
                            <th>ឈ្មោះទំនិញ</th>
                            <th>ទម្ងន់</th>
                            <th>ចំនួន</th>
                            <th>កាលបរិច្ឆេទ</th>
                            <th>សកម្មភាព</th>
                        </tr>
                    </thead>
                    <tbody id="goodsBody">
                        @if(isset($goodsIn) && $goodsIn->count() > 0)
                            @foreach($goodsIn as $item)
                                @php $qty = intval($item->qty1); @endphp
                                <tr data-id="{{ $item->id }}" class="{{ $qty == 0 ? 'cell-red-alert' : ($qty > 0 && $qty <= 3 ? 'cell-yellow-alert' : '') }}">
                                    <td>{{ $item->code }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->weight }}</td>
                                    <td>{{ intval($item->qty1) }}</td>
                                    <td>{{ $item->imported_at }}</td>
                                    <td>
                                        <a href="{{ route('products.show', $item->id) }}" class="btn btn-sm btn-info">
                                            <i data-lucide="eye"></i> មើលព័ត៌មានទំនិញ
                                        </a>
                                        <button class="btn btn-sm btn-warning edit-good-btn" data-id="{{ $item->id }}">កែប្រែ</button>
                                        <button class="btn btn-sm btn-danger delete-good-btn" data-id="{{ $item->id }}">លុប</button>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr id="noRecordsRow"><td colspan="6">មិនមានកំណត់ត្រា</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
<div id="toastContainer" aria-live="polite" aria-atomic="true"></div>
<div class="modal fade" id="importGoodsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">នាំចូលទិន្នន័យ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="goodsImportForm" action="{{ route('products.goods-in-stock-table.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="goodsImportFile">ឯកសារ CSV ឬ XLSX</label>
                        <input type="file" id="goodsImportFile" name="file" class="form-control" accept=".csv,.xlsx" required>
                    </div>
                    <div class="form-text">The first row must contain code, name, weight, and qty1 headers. The imported_at column is optional.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">បិទ</button>
                    <button type="submit" class="btn btn-primary">នាំចូល</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="bulkGoodsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">បន្ថែមទិន្នន័យច្រើន</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="bulkGoodsForm" action="{{ route('products.goods-in-stock-table.bulk') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div id="bulkGoodsRows">
                        <div class="row g-2 align-items-end mb-2 bulk-good-row">
                            <div class="col-md-3">
                                <label class="form-label">លេខកូដ</label>
                                <input type="text" name="items[0][code]" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">ឈ្មោះទំនិញ</label>
                                <input type="text" name="items[0][name]" class="form-control" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">ទម្ងន់</label>
                                <input type="text" name="items[0][weight]" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">ចំនួន</label>
                                <input type="number" name="items[0][qty1]" value="1" class="form-control" required>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-outline-danger remove-bulk-row">ដកចេញ</button>
                            </div>
                            <input type="hidden" name="items[0][imported_at]" value="">
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary" id="addBulkGoodsRow">បន្ថែមជួរ</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">បិទ</button>
                    <button type="submit" class="btn btn-primary">រក្សាទុកទាំងអស់</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="createGoodModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="goodModalTitle">បន្ថែមទំនិញ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="goodInModalForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">លេខកូដ</label>
                        <input type="text" name="code" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ឈ្មោះទំនិញ</label>
                        <div class="product-name-picker">
                            <input type="text" name="name" class="form-control" id="stockNameInput" list="stockNameList" placeholder="ជ្រើសរើស ឬ បញ្ចូលថ្មី" required>
                            <div class="hint-text">ជ្រើសរើសពីបញ្ជី ឬ បញ្ចូលឈ្មោះថ្មី</div>
                        </div>
                        <datalist id="stockNameList">
                            @foreach($stockNames as $stockName)
                                <option value="{{ $stockName }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ទម្ងន់</label>
                        <input type="text" name="weight" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ចំនួន</label>
                        <input type="number" name="qty1" value ="1" readonly class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">កាលបរិច្ឆេទ</label>
                        <input type="datetime-local" name="imported_at" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">បិទ</button>
                    <button type="submit" class="btn btn-primary" id="goodSubmitBtn">រក្សាទុក</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var modalEl = document.getElementById('createGoodModal');
    var modal = new bootstrap.Modal(modalEl);
    var openCreateBtn = document.getElementById('openCreateBtn');
    var form = document.getElementById('goodInModalForm');
    var submitBtn = document.getElementById('goodSubmitBtn');
    var stockNameInput = document.getElementById('stockNameInput');

    if (stockNameInput) {
        stockNameInput.addEventListener('change', function() {
            var selectedName = stockNameInput.value;
            if (!selectedName) return;

            fetch('/search-stock?name=' + encodeURIComponent(selectedName), {
                headers: { 'Accept': 'application/json' }
            })
            .then(function(res) { return res.json(); })
            .then(function(json) {
                if (json.success && json.item) {
                    if (form.code) form.code.value = json.item.code || '';
                    if (form.name) form.name.value = json.item.name || '';
                    if (form.weight) form.weight.value = json.item.weight || '';
                    if (form.qty1) form.qty1.value = json.item.qty1 || 0;
                }
            })
            .catch(function() {
                showToast('Stock lookup failed.', 'error');
            });
        });
    }

    openCreateBtn.addEventListener('click', function() {
        form.removeAttribute('data-edit-id');
        document.getElementById('goodModalTitle').textContent = 'Add Product';
        submitBtn.textContent = 'Save';
        form.reset();
        var now = new Date();
        var tzOffset = now.getTimezoneOffset();
        var localISO = new Date(now.getTime() - tzOffset * 60000).toISOString().slice(0,16);
        if (form.imported_at) form.imported_at.value = localISO;
        modal.show();
    });
    function autosizeTable() {
        var table = document.querySelector('table.table');
        if (!table || !table.tHead) return;
        var colCount = table.tHead.rows[0].cells.length;
        var colgroup = table.querySelector('colgroup');
        if (!colgroup) {
            colgroup = document.createElement('colgroup');
            for (var i = 0; i < colCount; i++) {
                var c = document.createElement('col');
                colgroup.appendChild(c);
            }
            table.insertBefore(colgroup, table.firstChild);
        }
        var measure = document.createElement('div');
        measure.style.cssText = 'position:absolute;left:-9999px;top:-9999px;visibility:hidden;white-space:nowrap;';
        document.body.appendChild(measure);
        function measureWidth(text, cell) {
            var span = document.createElement('span');
            var cs = getComputedStyle(cell);
            span.style.font = cs.font || (cs.fontSize + ' ' + cs.fontFamily);
            span.textContent = text === null || text === undefined ? '' : text.toString();
            measure.appendChild(span);
            var w = span.offsetWidth;
            measure.removeChild(span);
            return w;
        }
        var widths = new Array(colCount).fill(0);
        var headers = table.tHead.rows[0].cells;
        for (var i = 0; i < headers.length; i++) {
            widths[i] = Math.max(widths[i], measureWidth(headers[i].innerText, headers[i]));
        }
        var rows = table.tBodies[0] ? Array.from(table.tBodies[0].rows) : [];
        var maxRows = Math.min(rows.length, 200);
        for (var r = 0; r < maxRows; r++) {
            var cells = rows[r].cells;
            for (var j = 0; j < Math.min(cells.length, colCount); j++) {
                widths[j] = Math.max(widths[j], measureWidth(cells[j].innerText, cells[j]));
            }
        }
        var paddingPerCol = 24;
        var total = widths.reduce(function(a,b){return a + b + paddingPerCol;}, 0);
        if (total <= 0) total = 1;
        var tableWidth = table.clientWidth || table.offsetWidth || table.getBoundingClientRect().width || total;
        for (var k = 0; k < colCount; k++) {
            var percent = ((widths[k] + paddingPerCol) / total) * 100;
            var col = colgroup.children[k];
            if (col) col.style.width = percent + '%';
        }
        document.body.removeChild(measure);
    }
    setTimeout(autosizeTable, 50);
    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('edit-good-btn')) {
            var id = e.target.getAttribute('data-id');
            var tr = document.querySelector('tr[data-id="' + id + '"]');
            if (!tr) return;
            var tds = tr.querySelectorAll('td');
            form.code.value = tds[0].textContent || '';
            form.name.value = tds[1].textContent || '';
            form.weight.value = tds[2].textContent || '';
            form.qty1.value = tds[3].textContent || '';
            form.imported_at.value = tds[4].textContent || '';
            form.setAttribute('data-edit-id', id);
            document.getElementById('goodModalTitle').textContent = 'Edit Product';
            submitBtn.textContent = 'Update';
            modal.show();
        }
        if (e.target && e.target.classList.contains('delete-good-btn')) {
            var id = e.target.getAttribute('data-id');
            if (typeof window.showConfirm === 'function') {
                showConfirm('Are you sure you want to delete this record?').then(function(confirmed) {
                    if (!confirmed) return;
                    fetch('/goods-in-stock-table/' + id, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    }).then(function(res) { return res.json(); }).then(function(json) {
                        if (json.success) {
                            var tr = document.querySelector('tr[data-id="' + id + '"]');
                            if (tr) tr.remove();
                            showToast('Record deleted successfully.', 'success');
                        } else {
                            showToast('Delete failed: ' + (json.error || 'Unknown error'), 'error');
                        }
                    }).catch(function(err) {
                        showToast('Error: ' + (err.error || err.message || JSON.stringify(err)), 'error');
                    });
                });
            } else {
                if (!confirm('Are you sure you want to delete this record?')) return;
                fetch('/goods-in-stock-table/' + id, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                }).then(function(res) { return res.json(); }).then(function(json) {
                    if (json.success) {
                        var tr = document.querySelector('tr[data-id="' + id + '"]');
                        if (tr) tr.remove();
                        showToast('Record deleted successfully.', 'success');
                    } else {
                        showToast('Delete failed: ' + (json.error || 'Unknown error'), 'error');
                    }
                }).catch(function(err) {
                    showToast('Error: ' + (err.error || err.message || JSON.stringify(err)), 'error');
                });
            }
        }
    });
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        if (submitBtn) submitBtn.disabled = true;
        var data = {
            code: form.code.value,
            name: form.name.value,
            weight: form.weight.value,
            qty1: form.qty1.value,
            imported_at: form.imported_at.value
        };
        var editId = form.getAttribute('data-edit-id');
        var url = editId ? '/goods-in-stock-table/' + editId : '{{ route("products.goods-in-stock-table.store") }}';
        var method = editId ? 'PUT' : 'POST';
        fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        }).then(function(res) { return res.json().then(function(j) { if (!res.ok) throw j; return j; }); })
        .then(function(json) {
            if (json.success) {
                var row = editId ? json.data : json.data;
                var tbody = document.getElementById('goodsBody');
                var noRow = document.getElementById('noRecordsRow');
                if (noRow) noRow.remove();
                var tr = document.querySelector('tr[data-id="' + (row.id || '') + '"]');
                var qty = parseInt(row.qty1, 10);
                var className = '';
                if (qty === 0) className = 'cell-red-alert';
                else if (qty > 0 && qty <= 3) className = 'cell-yellow-alert';
                var inner = '<td>' + (row.code || '') + '</td>' +
                            '<td>' + (row.name || '') + '</td>' +
                            '<td>' + (row.weight || '') + '</td>' +
                            '<td>' + (parseInt(row.qty1, 10) || 0) + '</td>' +
                            '<td>' + (row.imported_at || '') + '</td>' +
                            '<td><button class="btn btn-sm btn-warning edit-good-btn" data-id="' + (row.id || '') + '">Edit</button> ' +
                                        '<button class="btn btn-sm btn-danger delete-good-btn" data-id="' + (row.id || '') + '">Delete</button></td>';
                if (tr) {
                    tr.innerHTML = inner;
                    tr.className = className;
                    tr.setAttribute('data-id', row.id);
                } else {
                    var newTr = document.createElement('tr');
                    newTr.setAttribute('data-id', row.id);
                    newTr.className = className;
                    newTr.innerHTML = inner;
                    tbody.insertBefore(newTr, tbody.firstChild);
                }
                modal.hide();
                form.reset();
                showToast('Saved successfully.', 'success');
                setTimeout(autosizeTable, 30);
            } else {
                showToast('Save failed: ' + (json.error || 'Unknown error'), 'error');
            }
        }).catch(function(err) {
            showToast('Error: ' + (err.error || err.message || JSON.stringify(err)), 'error');
        }).finally(function() { if (submitBtn) submitBtn.disabled = false; });
    });
    var importForm = document.getElementById('goodsImportForm');
    var importModal = new bootstrap.Modal(document.getElementById('importGoodsModal'));
    var bulkForm = document.getElementById('bulkGoodsForm');
    var bulkModal = new bootstrap.Modal(document.getElementById('bulkGoodsModal'));
    var bulkRows = document.getElementById('bulkGoodsRows');
    var bulkRowIndex = 1;
    var bulkRowTemplate = bulkRows.firstElementChild.cloneNode(true);

    document.getElementById('addBulkGoodsRow').addEventListener('click', function() {
        var newRow = bulkRowTemplate.cloneNode(true);
        newRow.querySelectorAll('[name]').forEach(function(input) {
            input.name = input.name.replace(/items\[\d+\]/, 'items[' + bulkRowIndex + ']');
            if (input.type === 'number') {
                input.value = '1';
            } else {
                input.value = '';
            }
        });
        bulkRows.appendChild(newRow);
        bulkRowIndex++;
    });

    bulkRows.addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-bulk-row')) {
            if (bulkRows.children.length > 1) {
                event.target.closest('.bulk-good-row').remove();
            }
        }
    });

    bulkForm.addEventListener('submit', function(event) {
        event.preventDefault();
        var submitButton = bulkForm.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        fetch(bulkForm.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: new FormData(bulkForm)
        }).then(function(response) {
            return response.json().then(function(json) {
                if (!response.ok) throw json;
                return json;
            });
        }).then(function(json) {
            bulkModal.hide();
            showToast('Saved ' + json.count + ' records.', 'success');
            window.setTimeout(function() { window.location.reload(); }, 600);
        }).catch(function(error) {
            showToast(getResponseError(error), 'error');
        }).finally(function() {
            submitButton.disabled = false;
        });
    });

    importForm.addEventListener('submit', function(event) {
        event.preventDefault();
        var submitButton = importForm.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        fetch(importForm.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: new FormData(importForm)
        }).then(function(response) {
            return response.json().then(function(json) {
                if (!response.ok) throw json;
                return json;
            });
        }).then(function(json) {
            importModal.hide();
            showToast('Imported ' + json.count + ' records.', 'success');
            window.setTimeout(function() { window.location.reload(); }, 600);
        }).catch(function(error) {
            showToast(getResponseError(error), 'error');
        }).finally(function() {
            submitButton.disabled = false;
        });
    });

    function getResponseError(error) {
        if (error && error.errors) {
            var keys = Object.keys(error.errors);
            if (keys.length && error.errors[keys[0]].length) return error.errors[keys[0]][0];
        }
        return error && error.message ? error.message : 'The request could not be completed.';
    }

    function showToast(message, type) {
        type = type || 'info';
        var container = document.getElementById('toastContainer');
        if (!container) return;
        var item = document.createElement('div');
        item.className = 'toast-item ' + type;
        item.setAttribute('role', 'status');
        item.setAttribute('aria-live', 'polite');
        item.textContent = message;
        var close = document.createElement('button');
        close.className = 'toast-close';
        close.innerHTML = '&times;';
        close.addEventListener('click', function() {
            if (item && item.parentNode) item.parentNode.removeChild(item);
        });
        item.appendChild(close);
        container.appendChild(item);
        setTimeout(function() { if (item && item.parentNode) item.parentNode.removeChild(item); }, 5000);
    }
});
</script>
@endsection
