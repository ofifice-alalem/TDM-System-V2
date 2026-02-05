@extends('layouts.app')

@section('title', 'تفاصيل الطلب')

@section('content')
<h2 class="mb-4">تفاصيل الطلب: {{ $request->invoice_number }}</h2>

<div class="card mb-3">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>المسوق:</strong> {{ $request->marketer->full_name }}</p>
                <p><strong>الحالة:</strong> 
                    @if($request->status === 'pending')
                        <span class="badge bg-warning">قيد الانتظار</span>
                    @elseif($request->status === 'approved')
                        <span class="badge bg-info">تمت الموافقة</span>
                    @elseif($request->status === 'documented')
                        <span class="badge bg-success">موثق</span>
                    @endif
                </p>
                <p><strong>التاريخ:</strong> {{ $request->created_at->format('Y-m-d H:i') }}</p>
            </div>
            <div class="col-md-6">
                @if($request->approved_by)
                    <p><strong>تمت الموافقة بواسطة:</strong> {{ $request->approver->full_name }}</p>
                    <p><strong>تاريخ الموافقة:</strong> {{ $request->approved_at->format('Y-m-d H:i') }}</p>
                @endif
                @if($request->documented_by)
                    <p><strong>تم التوثيق بواسطة:</strong> {{ $request->documenter->full_name }}</p>
                    <p><strong>تاريخ التوثيق:</strong> {{ $request->documented_at->format('Y-m-d H:i') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <h5>المنتجات</h5>
        <table class="table">
            <thead>
                <tr>
                    <th>المنتج</th>
                    <th>الكمية المطلوبة</th>
                </tr>
            </thead>
            <tbody>
                @foreach($request->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->quantity }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@if($request->status === 'pending')
<div class="card">
    <div class="card-body">
        <h5>الإجراءات</h5>
        <form action="{{ route('warehouse.requests.approve', $request) }}" method="POST" class="d-inline">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-success">موافقة</button>
        </form>

        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">رفض</button>
    </div>
</div>
@endif

@if($request->status === 'approved')
<div class="card">
    <div class="card-body">
        <h5>توثيق الطلب</h5>
        <form action="{{ route('warehouse.requests.document', $request) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label">صورة الفاتورة الموقعة</label>
                <input type="file" name="stamped_image" class="form-control" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-primary">توثيق</button>
        </form>
    </div>
</div>
@endif

@if($request->status === 'documented' && $request->stamped_image)
<div class="card">
    <div class="card-body">
        <h5>صورة التوثيق</h5>
        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#documentModal">عرض الصورة</button>
    </div>
</div>

<!-- Document Modal -->
<div class="modal fade" id="documentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">صورة التوثيق</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img src="{{ route('warehouse.requests.documentation', $request->id) }}" class="img-fluid" alt="صورة التوثيق">
            </div>
        </div>
    </div>
</div>
@endif

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('warehouse.requests.reject', $request) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">رفض الطلب</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">سبب الرفض</label>
                        <textarea name="notes" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">رفض الطلب</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('warehouse.requests.index') }}" class="btn btn-secondary">رجوع</a>
</div>
@endsection
