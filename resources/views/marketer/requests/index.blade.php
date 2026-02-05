@extends('layouts.app')

@section('title', 'طلبات البضاعة')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>طلبات البضاعة</h2>
    <a href="{{ route('marketer.requests.create') }}" class="btn btn-primary">طلب جديد</a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>رقم الفاتورة</th>
                    <th>التاريخ</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $request)
                <tr>
                    <td>{{ $request->invoice_number }}</td>
                    <td>{{ $request->created_at->format('Y-m-d') }}</td>
                    <td>
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
                    </td>
                    <td>
                        <a href="{{ route('marketer.requests.show', $request) }}" class="btn btn-sm btn-info">عرض</a>
                        @if(in_array($request->status, ['pending', 'approved']))
                            <form action="{{ route('marketer.requests.cancel', $request) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد؟')">إلغاء</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">لا توجد طلبات</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        {{ $requests->links() }}
    </div>
</div>
@endsection
