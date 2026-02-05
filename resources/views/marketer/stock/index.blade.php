@extends('layouts.app')

@section('title', 'مخزوني')

@section('content')
<h2 class="mb-4">مخزوني</h2>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">مجموع المخزون الفعلي</h5>
                <h2 class="text-primary">{{ $totalActual }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">مجموع المخزون المحجوز</h5>
                <h2 class="text-warning">{{ $totalReserved }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <ul class="nav nav-tabs" id="stockTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="actual-tab" data-bs-toggle="tab" data-bs-target="#actual" type="button" role="tab">
                    المخزون الفعلي
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="reserved-tab" data-bs-toggle="tab" data-bs-target="#reserved" type="button" role="tab">
                    المخزون المحجوز
                </button>
            </li>
        </ul>

        <div class="tab-content mt-3" id="stockTabsContent">
            <div class="tab-pane fade show active" id="actual" role="tabpanel">
                @if($actualStock->count() > 0)
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>المنتج</th>
                            <th>الكمية</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($actualStock as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->name }}</td>
                            <td><span class="badge bg-primary">{{ $item->quantity }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-secondary">
                            <td colspan="2" class="text-end"><strong>الإجمالي:</strong></td>
                            <td><strong>{{ $totalActual }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
                @else
                <div class="alert alert-info">لا يوجد مخزون فعلي حالياً</div>
                @endif
            </div>

            <div class="tab-pane fade" id="reserved" role="tabpanel">
                @if($reservedStock->count() > 0)
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>المنتج</th>
                            <th>الكمية</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reservedStock as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->name }}</td>
                            <td><span class="badge bg-warning">{{ $item->quantity }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-secondary">
                            <td colspan="2" class="text-end"><strong>الإجمالي:</strong></td>
                            <td><strong>{{ $totalReserved }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
                @else
                <div class="alert alert-info">لا يوجد مخزون محجوز حالياً</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
