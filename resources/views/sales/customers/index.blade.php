@extends('layouts.app')

@section('title', 'العملاء')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-[1600px] mx-auto space-y-8 px-2">
        
        {{-- Header & Quick Actions --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 animate-fade-in-down">
            <div class="lg:col-span-8">
                <div class="flex items-center gap-3 mb-2">
                    <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                        إدارة العملاء
                    </span>
                </div>
                <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                    العملاء
                </h1>
            </div>

            <div class="lg:col-span-4 lg:translate-y-[30px]">
                <a href="{{ route('sales.customers.create') }}" class="px-8 py-4 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all shadow-lg shadow-primary-200/50 dark:shadow-none flex items-center justify-center gap-2 w-full">
                    <i data-lucide="user-plus" class="w-5 h-5"></i>
                    عميل جديد
                </a>
            </div>
        </div>

        {{-- Customers List --}}
        <div class="bg-white dark:bg-dark-card rounded-[2rem] p-6 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border animate-slide-up">
            @forelse($customers as $customer)
                <div class="bg-gray-50 dark:bg-dark-bg rounded-2xl p-6 mb-4 border border-gray-200 dark:border-dark-border hover:shadow-lg transition-all">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-12 h-12 bg-primary-100 dark:bg-primary-600/20 rounded-xl flex items-center justify-center">
                                    <i data-lucide="user" class="w-6 h-6 text-primary-600 dark:text-primary-400"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $customer->name }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-dark-muted flex items-center gap-2">
                                        <i data-lucide="phone" class="w-4 h-4"></i>
                                        {{ $customer->phone }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-3 gap-4 mt-4">
                                <div class="bg-white dark:bg-dark-card rounded-xl p-3 border border-gray-200 dark:border-dark-border">
                                    <p class="text-xs text-gray-500 dark:text-dark-muted mb-1">عدد الفواتير</p>
                                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $customer->invoices_count ?? 0 }}</p>
                                </div>
                                <div class="bg-white dark:bg-dark-card rounded-xl p-3 border border-gray-200 dark:border-dark-border">
                                    <p class="text-xs text-gray-500 dark:text-dark-muted mb-1">الدين</p>
                                    <p class="text-lg font-bold {{ ($customer->debt_ledger_sum_amount ?? 0) > 0 ? 'text-red-600' : 'text-green-600' }}">
                                        {{ number_format($customer->debt_ledger_sum_amount ?? 0, 0) }} د.ع
                                    </p>
                                </div>
                                <div class="bg-white dark:bg-dark-card rounded-xl p-3 border border-gray-200 dark:border-dark-border">
                                    <p class="text-xs text-gray-500 dark:text-dark-muted mb-1">الحالة</p>
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-bold {{ $customer->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $customer->is_active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                        {{ $customer->is_active ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex gap-2 mr-4">
                            <a href="{{ route('sales.customers.show', $customer) }}" class="px-5 py-2.5 bg-white dark:bg-dark-card border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-50 dark:hover:bg-gray-700 transition-all text-sm flex items-center gap-2">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                                التفاصيل
                            </a>
                            <a href="{{ route('sales.customers.edit', $customer) }}" class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2">
                                <i data-lucide="edit" class="w-4 h-4"></i>
                                تعديل
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-gray-100 dark:bg-dark-bg rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="users" class="w-10 h-10 text-gray-400 dark:text-gray-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">لا يوجد عملاء</h3>
                    <p class="text-gray-500 dark:text-dark-muted mb-6">لم تقم بإضافة أي عملاء بعد</p>
                    <a href="{{ route('sales.customers.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all">
                        <i data-lucide="user-plus" class="w-5 h-5"></i>
                        إضافة عميل جديد
                    </a>
                </div>
            @endforelse

            {{-- Pagination --}}
            @if($customers->hasPages())
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-dark-border">
                    {{ $customers->links() }}
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
