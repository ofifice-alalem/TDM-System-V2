@extends('layouts.app')

@section('title', 'طلبات السحب')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-black text-gray-900 dark:text-white mb-2">طلبات السحب</h1>
            <p class="text-gray-600 dark:text-gray-400">إدارة طلبات سحب أرباح المسوقين</p>
        </div>
    </div>

    @include('shared.withdrawals._status-tabs', ['route' => fn($params) => route('admin.withdrawals.index', $params)])

    @if($withdrawals->isEmpty())
        <div class="bg-white dark:bg-dark-card rounded-2xl p-12 text-center shadow-lg border border-gray-200 dark:border-dark-border">
            <div class="w-20 h-20 bg-gray-100 dark:bg-dark-bg rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="wallet" class="w-10 h-10 text-gray-400"></i>
            </div>
            <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">لا توجد طلبات سحب</h3>
            <p class="text-gray-600 dark:text-gray-400">لا توجد طلبات سحب في هذه الحالة</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($withdrawals as $withdrawal)
                @include('shared.withdrawals._withdrawal-card', [
                    'withdrawal' => $withdrawal,
                    'viewRoute' => route('admin.withdrawals.show', $withdrawal)
                ])
            @endforeach
        </div>

        <div class="mt-8">
            {{ $withdrawals->links() }}
        </div>
    @endif
</div>
@endsection
