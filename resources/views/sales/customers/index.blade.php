@extends('layouts.app')

@section('title', 'العملاء')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-[1600px] mx-auto space-y-8 px-2">
        
        {{-- Header --}}
        <div class="animate-fade-in-down">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-3">
                    <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                        إدارة العملاء
                    </span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 bg-white dark:bg-dark-card rounded-xl p-1 border border-gray-200 dark:border-dark-border">
                        <button onclick="setView('grid')" id="gridBtn" class="px-4 py-2 rounded-lg font-bold transition-all flex items-center gap-2">
                            <i data-lucide="grid-3x3" class="w-4 h-4"></i>
                            شبكة
                        </button>
                        <button onclick="setView('list')" id="listBtn" class="px-4 py-2 rounded-lg font-bold transition-all flex items-center gap-2">
                            <i data-lucide="list" class="w-4 h-4"></i>
                            قائمة
                        </button>
                    </div>
                    <a href="{{ route('sales.customers.create') }}" class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                        <i data-lucide="plus" class="w-5 h-5"></i>
                        إضافة عميل جديد
                    </a>
                </div>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                العملاء
            </h1>
        </div>

        {{-- Filters --}}
        <div class="animate-fade-in">
            <form method="GET" class="bg-white dark:bg-dark-card rounded-2xl p-4 shadow-lg shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="البحث بالاسم أو رقم الهاتف..." class="w-full px-4 py-2 bg-white dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl text-gray-900 dark:text-white focus:border-primary-500 transition-all">
                    </div>
                    <div>
                        <select name="sort" class="w-full px-4 py-2 bg-white dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl text-gray-900 dark:text-white focus:border-primary-500 transition-all">
                            <option value="">الترتيب حسب...</option>
                            <option value="debt_desc" {{ request('sort') == 'debt_desc' ? 'selected' : '' }}>الدين (من الأعلى)</option>
                            <option value="debt_asc" {{ request('sort') == 'debt_asc' ? 'selected' : '' }}>الدين (من الأقل)</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all">
                            <i data-lucide="search" class="w-4 h-4 inline"></i>
                            بحث
                        </button>
                        <a href="{{ route('sales.customers.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-dark-bg hover:bg-gray-300 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-bold transition-all">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Customers Grid --}}
        <div id="gridView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-slide-up">
            @forelse($customers as $customer)
                <div class="bg-white dark:bg-dark-card rounded-3xl p-6 shadow-lg shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                    
                    {{-- Customer Header --}}
                    <div class="flex items-start justify-between mb-6">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900/40 dark:to-blue-800/40 rounded-2xl flex items-center justify-center text-blue-600 dark:text-blue-400 shadow-md group-hover:scale-110 transition-transform">
                                <i data-lucide="user" class="w-8 h-8"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white mb-1">{{ $customer->name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                    <i data-lucide="phone" class="w-3.5 h-3.5"></i>
                                    {{ $customer->phone }}
                                </p>
                            </div>
                        </div>
                        @if($customer->is_active)
                        <span class="px-3 py-1.5 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-full text-xs font-bold border border-emerald-100 dark:border-emerald-500/30 flex items-center gap-1">
                            <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                            نشط
                        </span>
                        @else
                        <span class="px-3 py-1.5 bg-gray-50 dark:bg-gray-500/10 text-gray-600 dark:text-gray-400 rounded-full text-xs font-bold border border-gray-100 dark:border-gray-500/30 flex items-center gap-1">
                            <i data-lucide="x-circle" class="w-3.5 h-3.5"></i>
                            غير نشط
                        </span>
                        @endif
                    </div>

                    {{-- Stats --}}
                    <div class="grid grid-cols-2 gap-3 mb-6">
                        <div class="bg-gray-50 dark:bg-dark-bg rounded-2xl p-4 text-center">
                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1 flex items-center justify-center gap-1">
                                <i data-lucide="receipt" class="w-3.5 h-3.5"></i>
                                الفواتير
                            </div>
                            <div class="text-sm font-black text-gray-900 dark:text-white">{{ $customer->invoices_count ?? 0 }}</div>
                        </div>
                        <div class="bg-gray-50 dark:bg-dark-bg rounded-2xl p-4 text-center">
                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1 flex items-center justify-center gap-1">
                                <i data-lucide="wallet" class="w-3.5 h-3.5"></i>
                                الدين
                            </div>
                            <div class="text-sm font-black {{ ($customer->debt_ledger_sum_amount ?? 0) > 0 ? 'text-red-600 dark:text-red-400' : (($customer->debt_ledger_sum_amount ?? 0) < 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-900 dark:text-white') }}">
                                {{ number_format(abs($customer->debt_ledger_sum_amount ?? 0), 0) }} دينار
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2">
                        <a href="{{ route('sales.customers.show', $customer) }}" class="flex-1 px-5 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                            التفاصيل
                        </a>
                        <a href="{{ route('sales.customers.edit', $customer) }}" class="w-12 h-12 bg-amber-100 dark:bg-amber-500/10 rounded-xl flex items-center justify-center text-amber-600 dark:text-amber-400 hover:bg-amber-200 dark:hover:bg-amber-500/20 transition-all">
                            <i data-lucide="edit" class="w-5 h-5"></i>
                        </a>
                        @if($customer->phone)
                        <a href="tel:{{ $customer->phone }}" class="w-12 h-12 bg-gray-100 dark:bg-dark-bg rounded-xl flex items-center justify-center text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">
                            <i data-lucide="phone" class="w-5 h-5"></i>
                        </a>
                        @endif
                    </div>

                </div>
            @empty
                <div class="col-span-full text-center py-16">
                    <div class="w-24 h-24 bg-gray-100 dark:bg-dark-bg rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="users" class="w-12 h-12 text-gray-400 dark:text-gray-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">لا توجد عملاء</h3>
                    <p class="text-gray-500 dark:text-dark-muted">لم يتم إضافة أي عملاء بعد</p>
                </div>
            @endforelse
        </div>

        {{-- Customers List --}}
        <div id="listView" class="bg-white dark:bg-dark-card rounded-2xl shadow-lg shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border animate-slide-up hidden overflow-hidden">
            @if($customers->count() > 0)
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-dark-bg border-b border-gray-200 dark:border-dark-border">
                    <tr>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">العميل</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">رقم الهاتف</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">عدد الفواتير</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">الدين</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">الحالة</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-dark-border">
                    @foreach($customers as $customer)
                    <tr class="hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900/40 dark:to-blue-800/40 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-400">
                                    <i data-lucide="user" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900 dark:text-white">{{ $customer->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $customer->phone }}</td>
                        <td class="px-6 py-4 text-center font-bold text-gray-900 dark:text-white">{{ $customer->invoices_count ?? 0 }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-bold {{ ($customer->debt_ledger_sum_amount ?? 0) > 0 ? 'text-red-600 dark:text-red-400' : (($customer->debt_ledger_sum_amount ?? 0) < 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-900 dark:text-white') }}">
                                {{ number_format(abs($customer->debt_ledger_sum_amount ?? 0), 0) }} دينار
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($customer->is_active)
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-full text-xs font-bold">
                                <i data-lucide="check-circle" class="w-3 h-3"></i>
                                نشط
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-gray-50 dark:bg-gray-500/10 text-gray-600 dark:text-gray-400 rounded-full text-xs font-bold">
                                <i data-lucide="x-circle" class="w-3 h-3"></i>
                                غير نشط
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('sales.customers.show', $customer) }}" class="w-9 h-9 bg-primary-600 hover:bg-primary-700 text-white rounded-lg flex items-center justify-center transition-all">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                <a href="{{ route('sales.customers.edit', $customer) }}" class="w-9 h-9 bg-amber-100 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 hover:bg-amber-200 rounded-lg flex items-center justify-center transition-all">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </a>
                                @if($customer->phone)
                                <a href="tel:{{ $customer->phone }}" class="w-9 h-9 bg-gray-100 dark:bg-dark-bg text-gray-600 dark:text-gray-400 hover:bg-gray-200 rounded-lg flex items-center justify-center transition-all">
                                    <i data-lucide="phone" class="w-4 h-4"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="text-center py-16">
                <div class="w-24 h-24 bg-gray-100 dark:bg-dark-bg rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="users" class="w-12 h-12 text-gray-400 dark:text-gray-600"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">لا توجد عملاء</h3>
                <p class="text-gray-500 dark:text-dark-muted">لم يتم إضافة أي عملاء بعد</p>
            </div>
            @endif
        </div>

        {{-- Pagination --}}
        @if($customers->hasPages())
        <div class="animate-fade-in">
            {{ $customers->links() }}
        </div>
        @endif

    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        
        const view = localStorage.getItem('customersView') || 'grid';
        setView(view);
    });
    
    function setView(view) {
        const gridView = document.getElementById('gridView');
        const listView = document.getElementById('listView');
        const gridBtn = document.getElementById('gridBtn');
        const listBtn = document.getElementById('listBtn');
        
        if (view === 'grid') {
            gridView.classList.remove('hidden');
            listView.classList.add('hidden');
            gridBtn.classList.add('bg-primary-600', 'text-white');
            gridBtn.classList.remove('text-gray-600', 'dark:text-gray-400');
            listBtn.classList.remove('bg-primary-600', 'text-white');
            listBtn.classList.add('text-gray-600', 'dark:text-gray-400');
        } else {
            gridView.classList.add('hidden');
            listView.classList.remove('hidden');
            listBtn.classList.add('bg-primary-600', 'text-white');
            listBtn.classList.remove('text-gray-600', 'dark:text-gray-400');
            gridBtn.classList.remove('bg-primary-600', 'text-white');
            gridBtn.classList.add('text-gray-600', 'dark:text-gray-400');
        }
        
        localStorage.setItem('customersView', view);
        lucide.createIcons();
    }
</script>
@endpush

@endsection
