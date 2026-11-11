@extends('layout')

@section('title', 'ស្តុកចាស់បញ្ចូលថ្មី')

@section('content')
<style>
    .inventory-dashboard .dashboard-table-card {
        height: 100%;
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .inventory-dashboard .dashboard-table-card .card-header {
        background-color: #f8f9fa;
        font-weight: 700;
    }
    .inventory-dashboard .table-responsive {
        width: 100%;
        overflow-x: auto;
    }
    .inventory-dashboard table {
        margin-bottom: 0;
        vertical-align: middle;
        text-align: center;
        white-space: nowrap;
    }
    .inventory-dashboard .pagination {
        margin-bottom: 0;
    }
</style>

<div class="container-fluid inventory-dashboard">
    <div class="d-flex align-items-center gap-3 mb-4">
        <button class="toggle-btn btn btn-outline-secondary" onclick="toggleSidebar()"><i data-lucide="menu"></i></button>
        <h1 class="mb-0">ស្តុកចាស់បញ្ចូលថ្មី</h1>
    </div>

    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="main-stock-tab" data-bs-toggle="tab" data-bs-target="#main-stock-pane" type="button" role="tab" aria-controls="main-stock-pane" aria-selected="true">ស្តុកទំនិញ</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="removed-stock-tab" data-bs-toggle="tab" data-bs-target="#removed-stock-pane" type="button" role="tab" aria-controls="removed-stock-pane" aria-selected="false">ទិន្នន័យបានដក</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="remaining-gems-tab" data-bs-toggle="tab" data-bs-target="#remaining-gems-pane" type="button" role="tab" aria-controls="remaining-gems-pane" aria-selected="false">តារាងត្បូងនៅសល់</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="summary-stock-tab" data-bs-toggle="tab" data-bs-target="#summary-stock-pane" type="button" role="tab" aria-controls="summary-stock-pane" aria-selected="false">សង្ខេបស្តុក</button>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active pt-3" id="main-stock-pane" role="tabpanel" aria-labelledby="main-stock-tab" tabindex="0">
            <section class="card dashboard-table-card">
                <div class="card-header">ស្តុកទំនិញ</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>ប្រភេទ</th>
                                    <th>ឈ្មោះទំនិញ</th>
                                    <th>លេខកូដចាប់ពី</th>
                                    <th>លេខកូដរហូតដល់</th>
                                    <th>ចំនួនសរុប</th>
                                    <th>ចំនួននៅសល់</th>
                                    <th>ទម្ងន់នៅសល់</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mainStock as $item)
                                    <tr>
                                        <td>{{ $item->category ?: 'Unknown' }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->code_from }}</td>
                                        <td>{{ $item->code_to }}</td>
                                        <td>{{ $item->total_qty }}</td>
                                        <td>{{ $item->remain_qty }}</td>
                                        <td>{{ $item->remain_weight }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7">មិនមានទិន្នន័យ</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $mainStock->links('pagination::bootstrap-5') }}</div>
                </div>
            </section>
        </div>

        <div class="tab-pane fade pt-3" id="removed-stock-pane" role="tabpanel" aria-labelledby="removed-stock-tab" tabindex="0">
            <section class="card dashboard-table-card">
                <div class="card-header">ទិន្នន័យបានដក</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-sm">
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
                                        <td>{{ $item->created_at }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5">មិនមានទិន្នន័យ</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $removedStock->links('pagination::bootstrap-5') }}</div>
                </div>
            </section>
        </div>

        <div class="tab-pane fade pt-3" id="remaining-gems-pane" role="tabpanel" aria-labelledby="remaining-gems-tab" tabindex="0">
            <section class="card dashboard-table-card">
                <div class="card-header">តារាងត្បូងនៅសល់</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>ប្រភេទ</th>
                                    <th>ឈ្មោះទំនិញ</th>
                                    <th>លេខកូដចាប់ពី</th>
                                    <th>លេខកូដរហូតដល់</th>
                                    <th>ចំនួននៅសល់</th>
                                    <th>ទម្ងន់នៅសល់</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($remainingGems as $item)
                                    <tr>
                                        <td>{{ $item->category ?: 'Unknown' }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->code_from }}</td>
                                        <td>{{ $item->code_to }}</td>
                                        <td>{{ $item->remain_qty }}</td>
                                        <td>{{ $item->remain_weight }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6">មិនមានទិន្នន័យ</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $remainingGems->links('pagination::bootstrap-5') }}</div>
                </div>
            </section>
        </div>

        <div class="tab-pane fade pt-3" id="summary-stock-pane" role="tabpanel" aria-labelledby="summary-stock-tab" tabindex="0">
            <section class="card dashboard-table-card">
                <div class="card-header">សង្ខេបស្តុក</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>ប្រភេទ</th>
                                    <th>ឈ្មោះទំនិញ</th>
                                    <th>ចំនួនប្រភេទ</th>
                                    <th>ចំនួននៅសល់សរុប</th>
                                    <th>ទម្ងន់នៅសល់សរុប</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($summaryStock as $item)
                                    <tr>
                                        <td>{{ $item->category ?: 'Unknown' }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->product_count }}</td>
                                        <td>{{ $item->total_qty }}</td>
                                        <td>{{ $item->total_weight }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5">មិនមានទិន្នន័យ</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $summaryStock->links('pagination::bootstrap-5') }}</div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
