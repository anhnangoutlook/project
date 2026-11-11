@extends('layout')

@section('content')
<div class="container">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center gap-3 mb-4 text-start justify-content-start">
                <button class="toggle-btn btn btn-outline-secondary" onclick="toggleSidebar()"><i data-lucide="menu"></i></button>
                <h1 class="mb-0">All Data</h1>
            </div>
            <form action="{{ route('products.all-data') }}" method="GET" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search name or category" value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="filter"></i> Search
                    </button>
                    <a href="{{ route('products.all-data') }}" class="btn btn-outline-secondary">
                        <i data-lucide="refresh-cw"></i> Clear
                    </a>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Category</th>
                            <th>Name</th>
                            <th>Code From</th>
                            <th>Code To</th>
                            <th>Total Qty</th>
                            <th>Status 1</th>
                            <th>Remain Qty</th>
                            <th>Total Weight</th>
                            <th>Status 2</th>
                            <th>Remain Weight</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($allData->count() > 0)
                            @foreach($allData as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->category ?? '' }}</td>
                                <td>{{ $item->name ?? '' }}</td>
                                <td>{{ $item->code_from ?? '' }}</td>
                                <td>{{ $item->code_to ?? '' }}</td>
                                <td>{{ $item->total_qty ?? '' }}</td>
                                <td>{{ $item->status_1 ?? '' }}</td>
                                <td>{{ $item->remain_qty ?? '' }}</td>
                                <td>{{ $item->total_weight ?? '' }}</td>
                                <td>{{ $item->status_2 ?? '' }}</td>
                                <td>{{ $item->remain_weight ?? '' }}</td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="11">No data found</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                <div class="mt-3">
                    {{ $allData->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
