@extends('layouts.app')

@section('title', 'المدفوعات')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-[1600px] mx-auto space-y-8 px-2">
        
        {{-- Header & Quick Actions --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 animate-fade-in-down">
            <div class="lg:col-span-8">
                <div class="flex items-center gap-3 mb-2">
                    <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                        إدارة المدفوعات
                    </span>
                </div>
                <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                    المدفوعات
                </h1>
            </div>

            <div class="lg:col-span-4 lg:translate-y-[30px] flex items-center gap-3">
                <div class="flex items-center gap-2 bg-white dark:bg-dark-card rounded-xl p-1 border border-gray-200 dark:border-dark-border">
                    <button onclick="setPaymentView('card')" id="cardBtn" class="px-4 py-2 rounded-lg font-bold transition-all flex items-center gap-2">
                        <i data-lucide="layout-grid" class="w-4 h-4"></i>
                    </button>
                    <button onclick="setPaymentView('table')" id="tableBtn" class="px-4 py-2 rounded-lg font-bold transition-all flex items-center gap-2">
                        <i data-lucide="table" class="w-4 h-4"></i>
                    </button>
                </div>
                <a href="{{ route('sales.payments.create') }}" class="flex-1 px-8 py-4 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all shadow-lg shadow-primary-200/50 dark:shadow-none flex items-center justify-center gap-2">
                    <i data-lucide="plus-circle" class="w-5 h-5"></i>
                    تسجيل دفعة جديدة
                </a>
            </div>
        </div>

        {{-- Payments List --}}
        <div id="cardView" class="bg-white dark:bg-dark-card rounded-[2rem] p-6 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border animate-slide-up">
            @forelse($payments as $payment)
                <div class="bg-gray-50 dark:bg-dark-bg rounded-2xl p-6 mb-4 border border-gray-200 dark:border-dark-border hover:shadow-lg transition-all">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-12 h-12 bg-green-100 dark:bg-green-600/20 rounded-xl flex items-center justify-center">
                                    <i data-lucide="banknote" class="w-6 h-6 text-green-600 dark:text-green-400"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">#{{ $payment->payment_number }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-dark-muted flex items-center gap-2">
                                        <i data-lucide="user" class="w-4 h-4"></i>
                                        {{ $payment->customer->name }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-4 gap-4 mt-4">
                                <div class="bg-white dark:bg-dark-card rounded-xl p-3 border border-gray-200 dark:border-dark-border">
                                    <p class="text-xs text-gray-500 dark:text-dark-muted mb-1">المبلغ</p>
                                    <p class="text-lg font-bold text-green-600">{{ number_format($payment->amount, 0) }} دينار</p>
                                </div>
                                <div class="bg-white dark:bg-dark-card rounded-xl p-3 border border-gray-200 dark:border-dark-border">
                                    <p class="text-xs text-gray-500 dark:text-dark-muted mb-1">طريقة الدفع</p>
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">
                                        {{ $payment->payment_method === 'cash' ? 'نقدي' : ($payment->payment_method === 'transfer' ? 'تحويل' : 'شيك') }}
                                    </span>
                                </div>
                                <div class="bg-white dark:bg-dark-card rounded-xl p-3 border border-gray-200 dark:border-dark-border">
                                    <p class="text-xs text-gray-500 dark:text-dark-muted mb-1">التاريخ</p>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $payment->created_at->format('Y-m-d') }}</p>
                                </div>
                                <div class="bg-white dark:bg-dark-card rounded-xl p-3 border border-gray-200 dark:border-dark-border">
                                    <p class="text-xs text-gray-500 dark:text-dark-muted mb-1">الحالة</p>
                                    @if($payment->status === 'completed')
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 dark:text-emerald-400">
                                        <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                                        مكتمل
                                    </span>
                                    @else
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-red-700 dark:text-red-400">
                                        <i data-lucide="x-circle" class="w-3.5 h-3.5"></i>
                                        ملغي
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex gap-2 mr-4">
                            <a href="{{ route('sales.payments.show', $payment) }}" class="px-5 py-2.5 bg-white dark:bg-dark-card border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-50 dark:hover:bg-gray-700 transition-all text-sm flex items-center gap-2">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                                التفاصيل
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-gray-100 dark:bg-dark-bg rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="banknote" class="w-10 h-10 text-gray-400 dark:text-gray-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">لا توجد مدفوعات</h3>
                    <p class="text-gray-500 dark:text-dark-muted mb-6">لم تقم بتسجيل أي مدفوعات بعد</p>
                    <a href="{{ route('sales.payments.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all">
                        <i data-lucide="plus-circle" class="w-5 h-5"></i>
                        تسجيل دفعة جديدة
                    </a>
                </div>
            @endforelse

            {{-- Pagination --}}
            @if($payments->hasPages())
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-dark-border">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>

        {{-- Table View --}}
        <div id="tableView" class="bg-white dark:bg-dark-card rounded-2xl shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border animate-slide-up hidden overflow-hidden">
            @if($payments->count() > 0)
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-dark-bg border-b border-gray-200 dark:border-dark-border">
                    <tr>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">رقم الدفعة</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">العميل</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">المبلغ</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">طريقة الدفع</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">الحالة</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">التاريخ</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-dark-border">
                    @foreach($payments as $payment)
                    <tr class="hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-green-100 dark:bg-green-600/20 rounded-lg flex items-center justify-center">
                                    <i data-lucide="banknote" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
                                </div>
                                <span class="font-bold text-gray-900 dark:text-white">#{{ $payment->payment_number }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $payment->customer->name }}</td>
                        <td class="px-6 py-4 text-center font-bold text-green-600">{{ number_format($payment->amount, 0) }} دينار</td>
                        <td class="px-6 py-4 text-center text-gray-900 dark:text-white font-bold">
                            {{ $payment->payment_method === 'cash' ? 'نقدي' : ($payment->payment_method === 'transfer' ? 'تحويل' : 'شيك') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($payment->status === 'completed')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 rounded-lg text-xs font-bold border border-emerald-200 dark:border-emerald-500/30">
                                <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                                مكتمل
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-100 dark:bg-red-500/10 text-red-700 dark:text-red-400 rounded-lg text-xs font-bold border border-red-200 dark:border-red-500/30">
                                <i data-lucide="x-circle" class="w-3.5 h-3.5"></i>
                                ملغي
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center text-gray-600 dark:text-gray-400">{{ $payment->created_at->format('Y-m-d') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center">
                                <a href="{{ route('sales.payments.show', $payment) }}" class="w-9 h-9 bg-primary-600 hover:bg-primary-700 text-white rounded-lg flex items-center justify-center transition-all">
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
                    <i data-lucide="banknote" class="w-10 h-10 text-gray-400 dark:text-gray-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">لا توجد مدفوعات</h3>
                <p class="text-gray-500 dark:text-dark-muted">لم تقم بتسجيل أي مدفوعات بعد</p>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        
        const view = localStorage.getItem('paymentsView') || 'card';
        setPaymentView(view);
    });
    
    function setPaymentView(view) {
        const cardView = document.getElementById('cardView');
        const tableView = document.getElementById('tableView');
        const cardBtn = document.getElementById('cardBtn');
        const tableBtn = document.getElementById('tableBtn');
        
        if (view === 'card') {
            cardView.classList.remove('hidden');
            tableView.classList.add('hidden');
            cardBtn.classList.add('bg-primary-600', 'text-white');
            cardBtn.classList.remove('text-gray-600', 'dark:text-gray-400');
            tableBtn.classList.remove('bg-primary-600', 'text-white');
            tableBtn.classList.add('text-gray-600', 'dark:text-gray-400');
        } else {
            cardView.classList.add('hidden');
            tableView.classList.remove('hidden');
            tableBtn.classList.add('bg-primary-600', 'text-white');
            tableBtn.classList.remove('text-gray-600', 'dark:text-gray-400');
            cardBtn.classList.remove('bg-primary-600', 'text-white');
            cardBtn.classList.add('text-gray-600', 'dark:text-gray-400');
        }
        
        localStorage.setItem('paymentsView', view);
        lucide.createIcons();
    }
</script>
@endpush
@endsection
