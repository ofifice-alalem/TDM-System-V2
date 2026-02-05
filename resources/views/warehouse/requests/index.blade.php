@extends('layouts.app')

@section('title', 'طلبات المسوقين')

@section('content')
<h2 class="mb-4">طلبات المسوقين</h2>

<div class="card">
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>رقم الفاتورة</th>
                    <th>المسوق</th>
                    <th>التاريخ</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $request)
                <tr>
                    <td>{{ $request->invoice_number }}</td>
                    <td>{{ $request->marketer->full_name }}</td>
                    <td>{{ $request->created_at->format('Y-m-d') }}</td>
                    <td>
                        @if($request->status === 'pending')
                            <span class="badge bg-warning">قيد الانتظار</span>
                        @else
                            <span class="badge bg-info">تمت الموافقة</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('warehouse.requests.show', $request) }}" class="btn btn-sm btn-info">عرض</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">لا توجد طلبات</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        {{ $requests->links() }}
    </div>
</div>
@endsection
