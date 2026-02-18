@extends('layouts.app')

@section('title', 'المرتجعات')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-[1600px] mx-auto space-y-8 px-2">
        
        {{-- Header --}}
        <div class="animate-fade-in-down">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-3">
                    <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                        إدارة المرتجعات
                    </span>
                </div>
                <a href="{{ route('sales.returns.create') }}" class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                    <i data-lucide="plus" class="w-5 h-5"></i>
                    إضافة مرتجع جديد
                </a>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                المرتجعات
            </h1>
        </div>

        {{-- Filter --}}
        <div class="animate-fade-in">
            <details class="bg-white dark:bg-dark-card rounded-2xl shadow-lg shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border overflow-hidden">
                <summary class="px-6 py-4 cursor-pointer hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i data-lucide="filter" class="w-5 h-5 text-primary-600 dark:text-primary-400"></i>
                        <span class="font-bold text-gray-900 dark:text-white">فلترة متقدمة</span>
                        @if(request()->hasAny(['return_number', 'customer', 'date_from', 'date_to', 'amount_from', 'amount_to', 'status']))
                            <span class="px-2 py-1 bg-primary-100 dark:bg-primary-500/20 text-primary-600 dark:text-primary-400 rounded-lg text-xs font-bold">نشط</span>
                        @endif
                    </div>
                    <i data-lucide="chevron-down" class="w-5 h-5 text-gray-400"></i>
                </summary>
                <form method="GET" class="p-6 border-t border-gray-200 dark:border-dark-border">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-2">رقم المرتجع</label>
                            <input type="text" name="return_number" value="{{ request('return_number') }}" placeholder="ابحث..." class="w-full px-4 py-2.5 bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl text-gray-900 dark:text-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 dark:focus:ring-primary-500/20 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-2">العميل</label>
                            <input type="text" name="customer" value="{{ request('customer') }}" placeholder="ابحث..." class="w-full px-4 py-2.5 bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl text-gray-900 dark:text-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 dark:focus:ring-primary-500/20 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-2">من تاريخ</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl text-gray-900 dark:text-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 dark:focus:ring-primary-500/20 transition-all dark:[color-scheme:dark]">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-2">إلى تاريخ</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl text-gray-900 dark:text-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 dark:focus:ring-primary-500/20 transition-all dark:[color-scheme:dark]">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-2">من مبلغ</label>
                            <input type="number" name="amount_from" value="{{ request('amount_from') }}" placeholder="0" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl text-gray-900 dark:text-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 dark:focus:ring-primary-500/20 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-2">إلى مبلغ</label>
                            <input type="number" name="amount_to" value="{{ request('amount_to') }}" placeholder="0" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl text-gray-900 dark:text-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 dark:focus:ring-primary-500/20 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-2">الحالة</label>
                            <select name="status" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl text-gray-900 dark:text-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 dark:focus:ring-primary-500/20 transition-all">
                                <option value="">الكل</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>مكتمل</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>ملغي</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex gap-2 mt-6">
                        <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all flex items-center gap-2 shadow-md">
                            <i data-lucide="search" class="w-4 h-4"></i>
                            بحث
                        </button>
                        @if(request()->hasAny(['return_number', 'customer', 'date_from', 'date_to', 'amount_from', 'amount_to', 'status']))
                            <a href="{{ route('sales.returns.index') }}" class="px-6 py-2.5 bg-gray-200 dark:bg-dark-bg hover:bg-gray-300 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-bold transition-all flex items-center gap-2">
                                <i data-lucide="x" class="w-4 h-4"></i>
                                إعادة تعيين
                            </a>
                        @endif
                    </div>
                </form>
            </details>
        </div>

        {{-- Returns List --}}
        <div class="bg-white dark:bg-dark-card rounded-2xl shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border animate-slide-up overflow-hidden">
            @if($returns->count() > 0)
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-dark-bg border-b border-gray-200 dark:border-dark-border">
                    <tr>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">رقم المرتجع</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">العميل</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">المبلغ</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">الحالة</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">التاريخ</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-dark-border">
                    @foreach($returns as $return)
                    <tr class="hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-orange-100 dark:bg-orange-600/20 rounded-lg flex items-center justify-center">
                                    <i data-lucide="package-x" class="w-5 h-5 text-orange-600 dark:text-orange-400"></i>
                                </div>
                                <span class="font-bold text-gray-900 dark:text-white">#{{ $return->return_number }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $return->customer->name }}</td>
                        <td class="px-6 py-4 text-center font-bold text-orange-600 dark:text-orange-400">{{ number_format($return->total_amount, 0) }} دينار</td>
                        <td class="px-6 py-4 text-center">
                            @if($return->status === 'completed')
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 rounded-lg text-xs font-bold">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                مكتمل
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 rounded-lg text-xs font-bold">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                ملغي
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center text-gray-600 dark:text-gray-400">{{ $return->created_at->format('Y-m-d') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center">
                                <a href="{{ route('sales.returns.show', $return) }}" class="w-9 h-9 bg-primary-600 hover:bg-primary-700 text-white rounded-lg flex items-center justify-center transition-all">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="text-center py-12">
                <div class="w-20 h-20 bg-gray-100 dark:bg-dark-bg rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="package-x" class="w-10 h-10 text-gray-400 dark:text-gray-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">لا توجد مرتجعات</h3>
                <p class="text-gray-500 dark:text-dark-muted">لم يتم تسجيل أي مرتجعات بعد</p>
            </div>
            @endif
        </div>

    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>
@endpush
@endsection
