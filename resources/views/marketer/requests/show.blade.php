@extends('layouts.app')

@section('title', 'تفاصيل الطلب')

@section('content')
@if(in_array($request->status, ['rejected', 'cancelled']))
<style>
    .watermark-container {
        position: relative;
    }
    .watermark-container::before {
        content: "الفاتورة لا يعتد بها";
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-45deg);
        font-size: 5rem;
        font-weight: bold;
        color: rgba(220, 53, 69, 0.15);
        z-index: 1000;
        pointer-events: none;
        white-space: nowrap;
    }
</style>
@endif
<h2 class="mb-4">تفاصيل الطلب: {{ $request->invoice_number }}</h2>

<div class="card mb-3">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>الحالة:</strong> 
                    @if($request->status === 'pending')
                        <span class="badge bg-warning">قيد الانتظار</span>
                    @elseif($request->status === 'approved')
                        <span class="badge bg-info">تمت الموافقة</span>
                    @elseif($request->status === 'documented')
                        <span class="badge bg-success">موثق</span>
                    @elseif($request->status === 'rejected')
                        <span class="badge bg-danger">مرفوض</span>
                    @else
                        <span class="badge bg-secondary">ملغي</span>
                    @endif
                </p>
                <p><strong>التاريخ:</strong> {{ $request->created_at->format('Y-m-d H:i') }}</p>
            </div>
            <div class="col-md-6">
                @if($request->approved_by)
                    <p><strong>تمت الموافقة بواسطة:</strong> {{ $request->approver->full_name }}</p>
                    <p><strong>تاريخ الموافقة:</strong> {{ $request->approved_at->format('Y-m-d H:i') }}</p>
                @endif
                @if($request->rejected_by)
                    <p><strong>تم الرفض بواسطة:</strong> {{ $request->rejecter->full_name }}</p>
                    <p><strong>تاريخ الرفض:</strong> {{ $request->rejected_at->format('Y-m-d H:i') }}</p>
                @endif
                @if($request->documented_by)
                    <p><strong>تم التوثيق بواسطة:</strong> {{ $request->documenter->full_name }}</p>
                    <p><strong>تاريخ التوثيق:</strong> {{ $request->documented_at->format('Y-m-d H:i') }}</p>
                @endif
                @if($request->notes)
                    <p><strong>ملاحظات:</strong> {{ $request->notes }}</p>
                @endif
                @if($request->stamped_image)
                    <p><strong>الفاتورة الموقعة:</strong> <a href="{{ asset('storage/' . $request->stamped_image) }}" target="_blank">عرض</a></p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h5>المنتجات</h5>
        <table class="table">
            <thead>
                <tr>
                    <th>المنتج</th>
                    <th>الكمية</th>
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

<div class="mt-3">
    <a href="{{ route('marketer.requests.index') }}" class="btn btn-secondary">رجوع</a>
    @if($request->status !== 'pending')
        <a href="{{ route('marketer.requests.pdf', $request) }}" class="btn btn-primary" target="_blank">تحميل PDF</a>
    @endif
    @if(in_array($request->status, ['pending', 'approved']))
        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#cancelModal">إلغاء الطلب</button>
    @endif
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('marketer.requests.cancel', $request) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">إلغاء الطلب</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">سبب الإلغاء</label>
                        <textarea name="notes" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    <button type="submit" class="btn btn-warning">تأكيد الإلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
