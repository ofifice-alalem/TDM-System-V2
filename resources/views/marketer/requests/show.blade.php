@extends('layouts.app')

@section('title', 'تفاصيل الطلب')

@section('content')
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
</div>
@endsection
