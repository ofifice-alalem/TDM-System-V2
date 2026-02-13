@extends('layouts.app')

@section('title', 'لوحة التحكم - أمين المخزن')

@section('content')

<div class="py-8">
    <div class="max-w-7xl mx-auto px-4">
        
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-black text-gray-900 dark:text-white">لوحة التحكم</h1>
            <p class="text-gray-500 dark:text-dark-muted mt-1">نظرة سريعة على العمليات المعلقة</p>
        </div>

        {{-- Quick Search --}}
        <div class="bg-white dark:bg-dark-card rounded-2xl p-6 border border-gray-200 dark:border-dark-border shadow-lg mb-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                    <i data-lucide="search" class="w-5 h-5 text-primary-600 dark:text-primary-400"></i>
                </div>
                <h2 class="text-lg font-black text-gray-900 dark:text-white">بحث سريع</h2>
            </div>
            
            <form method="GET" action="{{ route('warehouse.dashboard') }}" class="flex flex-col md:flex-row gap-3">
                <div class="flex-1">
                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">نوع الفاتورة</label>
                    <select name="invoice_type" required class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">اختر نوع الفاتورة...</option>
                        <option value="MR" {{ request('invoice_type') == 'MR' ? 'selected' : '' }}>MR - طلب بضاعة من المسوق</option>
                        <option value="MRR" {{ request('invoice_type') == 'MRR' ? 'selected' : '' }}>MRR - إرجاع بضاعة من المسوق</option>
                        <option value="SI" {{ request('invoice_type') == 'SI' ? 'selected' : '' }}>SI - فاتورة بيع للمتجر</option>
                        <option value="RET" {{ request('invoice_type') == 'RET' ? 'selected' : '' }}>RET - إرجاع بضاعة من المتجر</option>
                        <option value="RCP" {{ request('invoice_type') == 'RCP' ? 'selected' : '' }}>RCP - إيصال قبض</option>
                        <option value="FI" {{ request('invoice_type') == 'FI' ? 'selected' : '' }}>FI - فاتورة مصنع</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">رقم الفاتورة</label>
                    <input type="text" name="invoice_number" value="{{ request('invoice_number') }}" placeholder="أدخل رقم الفاتورة..." required class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2">
                        <i data-lucide="search" class="w-4 h-4"></i>
                        بحث
                    </button>
                </div>
            </form>

            @if($searchResult)
                @if($searchResult['found'])
                    <div class="mt-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700 rounded-xl p-4">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 bg-emerald-500 rounded-lg flex items-center justify-center">
                                <i data-lucide="check-circle" class="w-5 h-5 text-white"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-emerald-900 dark:text-emerald-400">تم العثور على {{ $searchResult['count'] }} نتيجة</h3>
                            </div>
                        </div>
                        <div class="space-y-2">
                            @foreach($searchResult['results'] as $item)
                                @php
                                    $statusConfig = [
                                        'pending' => ['bg' => 'bg-amber-100 dark:bg-amber-900/30', 'text' => 'text-amber-700 dark:text-amber-400', 'label' => 'معلق'],
                                        'approved' => ['bg' => 'bg-blue-100 dark:bg-blue-900/30', 'text' => 'text-blue-700 dark:text-blue-400', 'label' => 'معتمد'],
                                        'documented' => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-700 dark:text-emerald-400', 'label' => 'موثق'],
                                        'rejected' => ['bg' => 'bg-red-100 dark:bg-red-900/30', 'text' => 'text-red-700 dark:text-red-400', 'label' => 'مرفوض'],
                                        'cancelled' => ['bg' => 'bg-gray-100 dark:bg-gray-800/50', 'text' => 'text-gray-700 dark:text-gray-400', 'label' => 'ملغي'],
                                    ][$item['data']->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'label' => $item['data']->status];
                                @endphp
                                <div class="bg-white dark:bg-dark-card rounded-lg p-3 flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <p class="font-bold text-gray-900 dark:text-white text-sm">
                                                @if($searchResult['type'] == 'MR')
                                                    طلب بضاعة: {{ $item['data']->invoice_number }}
                                                @elseif($searchResult['type'] == 'MRR')
                                                    إرجاع بضاعة: {{ $item['data']->invoice_number }}
                                                @elseif($searchResult['type'] == 'SI')
                                                    فاتورة بيع: {{ $item['data']->invoice_number }}
                                                @elseif($searchResult['type'] == 'RET')
                                                    إرجاع من متجر: {{ $item['data']->return_number }}
                                                @elseif($searchResult['type'] == 'RCP')
                                                    إيصال قبض: {{ $item['data']->payment_number }}
                                                @elseif($searchResult['type'] == 'FI')
                                                    فاتورة مصنع: {{ $item['data']->invoice_number }}
                                                @endif
                                            </p>
                                            <span class="{{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} px-2 py-0.5 rounded text-xs font-bold">
                                                {{ $statusConfig['label'] }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">
                                            @if(isset($item['data']->marketer))
                                                المسوق: {{ $item['data']->marketer->full_name }}
                                            @endif
                                            @if(isset($item['data']->store))
                                                | المتجر: {{ $item['data']->store->name }}
                                            @endif
                                        </p>
                                    </div>
                                    <a href="{{ $item['route'] }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold text-sm transition-all flex items-center gap-2">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                        عرض
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="mt-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-xl p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center">
                                <i data-lucide="x-circle" class="w-5 h-5 text-white"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-red-900 dark:text-red-400">لم يتم العثور على الفاتورة</h3>
                                <p class="text-sm text-red-700 dark:text-red-500 mt-1">تأكد من رقم الفاتورة ونوعها</p>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- قسم: طلبات المسوقين --}}
            <div class="bg-white dark:bg-dark-card rounded-2xl p-6 border border-gray-200 dark:border-dark-border shadow-sm">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                        <i data-lucide="package" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
                    </div>
                    <h2 class="text-lg font-black text-gray-900 dark:text-white">طلبات المسوقين</h2>
                </div>
                
                <div class="space-y-3">
                <a href="{{ route('warehouse.requests.index', ['status' => 'pending']) }}" class="block bg-white dark:bg-dark-card rounded-xl p-5 border {{ $pendingRequests > 0 ? 'border-red-200 dark:border-red-800/50 alert-card' : 'border-gray-200 dark:border-dark-border' }} hover:shadow-xl transition-all hover:-translate-y-1">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-amber-50 dark:bg-amber-900/20 rounded-lg flex items-center justify-center">
                                <i data-lucide="clock" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
                            </div>
                            <div>
                                <div class="font-bold text-gray-900 dark:text-white">طلبات معلقة</div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">تحتاج موافقة</p>
                            </div>
                        </div>
                        <span class="text-3xl font-black text-gray-900 dark:text-white bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 px-5 py-3 rounded-xl shadow-sm dark:shadow-md border border-gray-200 dark:border-gray-700">{{ $pendingRequests }}</span>
                    </div>
                </a>

                <a href="{{ route('warehouse.requests.index', ['status' => 'approved']) }}" class="block bg-white dark:bg-dark-card rounded-xl p-5 border {{ $approvedRequests > 0 ? 'border-blue-200 dark:border-blue-800/50 alert-card' : 'border-gray-200 dark:border-dark-border' }} hover:shadow-xl transition-all hover:-translate-y-1">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                                <i data-lucide="check-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div>
                                <div class="font-bold text-gray-900 dark:text-white">بانتظار التوثيق</div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">معتمدة ومحجوزة</p>
                            </div>
                        </div>
                        <span class="text-3xl font-black text-gray-900 dark:text-white bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 px-5 py-3 rounded-xl shadow-sm dark:shadow-md border border-gray-200 dark:border-gray-700">{{ $approvedRequests }}</span>
                    </div>
                </a>

                <a href="{{ route('warehouse.returns.index', ['status' => 'pending']) }}" class="block bg-white dark:bg-dark-card rounded-xl p-5 border {{ $pendingReturns > 0 ? 'border-purple-200 dark:border-purple-800/50 alert-card' : 'border-gray-200 dark:border-dark-border' }} hover:shadow-xl transition-all hover:-translate-y-1">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-purple-50 dark:bg-purple-900/20 rounded-lg flex items-center justify-center">
                                <i data-lucide="package-x" class="w-5 h-5 text-purple-600 dark:text-purple-400"></i>
                            </div>
                            <div>
                                <div class="font-bold text-gray-900 dark:text-white">إرجاعات المسوقين</div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">تحتاج معالجة</p>
                            </div>
                        </div>
                        <span class="text-3xl font-black text-gray-900 dark:text-white bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 px-5 py-3 rounded-xl shadow-sm dark:shadow-md border border-gray-200 dark:border-gray-700">{{ $pendingReturns }}</span>
                    </div>
                </a>
                </div>
            </div>

            {{-- قسم: فواتير البيع --}}
            <div class="bg-white dark:bg-dark-card rounded-2xl p-6 border border-gray-200 dark:border-dark-border shadow-sm">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center">
                        <i data-lucide="shopping-cart" class="w-5 h-5 text-emerald-600 dark:text-emerald-400"></i>
                    </div>
                    <h2 class="text-lg font-black text-gray-900 dark:text-white">فواتير البيع</h2>
                </div>
                
                <div class="space-y-3">
                <a href="{{ route('warehouse.sales.index', ['status' => 'pending']) }}" class="block bg-white dark:bg-dark-card rounded-xl p-5 border {{ $pendingSales > 0 ? 'border-emerald-200 dark:border-emerald-800/50 alert-card' : 'border-gray-200 dark:border-dark-border' }} hover:shadow-xl transition-all hover:-translate-y-1">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg flex items-center justify-center">
                                <i data-lucide="file-text" class="w-5 h-5 text-emerald-600 dark:text-emerald-400"></i>
                            </div>
                            <div>
                                <div class="font-bold text-gray-900 dark:text-white">فواتير معلقة</div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">تحتاج توثيق</p>
                            </div>
                        </div>
                        <span class="text-3xl font-black text-gray-900 dark:text-white bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 px-5 py-3 rounded-xl shadow-sm dark:shadow-md border border-gray-200 dark:border-gray-700">{{ $pendingSales }}</span>
                    </div>
                </a>

                <a href="{{ route('warehouse.sales-returns.index', ['status' => 'pending']) }}" class="block bg-white dark:bg-dark-card rounded-xl p-5 border {{ $pendingSalesReturns > 0 ? 'border-orange-200 dark:border-orange-800/50 alert-card' : 'border-gray-200 dark:border-dark-border' }} hover:shadow-xl transition-all hover:-translate-y-1">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-orange-50 dark:bg-orange-900/20 rounded-lg flex items-center justify-center">
                                <i data-lucide="undo-2" class="w-5 h-5 text-orange-600 dark:text-orange-400"></i>
                            </div>
                            <div>
                                <div class="font-bold text-gray-900 dark:text-white">إرجاعات من المتاجر</div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">من المتاجر للمسوقين</p>
                            </div>
                        </div>
                        <span class="text-3xl font-black text-gray-900 dark:text-white bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 px-5 py-3 rounded-xl shadow-sm dark:shadow-md border border-gray-200 dark:border-gray-700">{{ $pendingSalesReturns }}</span>
                    </div>
                </a>
                </div>
            </div>

            {{-- قسم: إيصالات القبض (الجانب الأيسر) --}}
            <div class="bg-white dark:bg-dark-card rounded-2xl p-6 border border-gray-200 dark:border-dark-border shadow-sm">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 bg-cyan-100 dark:bg-cyan-900/30 rounded-lg flex items-center justify-center">
                        <i data-lucide="banknote" class="w-5 h-5 text-cyan-600 dark:text-cyan-400"></i>
                    </div>
                    <h2 class="text-lg font-black text-gray-900 dark:text-white">إيصالات القبض</h2>
                </div>
                
                <div class="space-y-3">
                <a href="{{ route('warehouse.payments.index', ['status' => 'pending']) }}" class="block bg-white dark:bg-dark-card rounded-xl p-5 border {{ $pendingPayments > 0 ? 'border-cyan-200 dark:border-cyan-800/50 alert-card' : 'border-gray-200 dark:border-dark-border' }} hover:shadow-xl transition-all hover:-translate-y-1">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-cyan-50 dark:bg-cyan-900/20 rounded-lg flex items-center justify-center">
                                <i data-lucide="wallet" class="w-5 h-5 text-cyan-600 dark:text-cyan-400"></i>
                            </div>
                            <div>
                                <div class="font-bold text-gray-900 dark:text-white">إيصالات معلقة</div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">تحتاج موافقة</p>
                            </div>
                        </div>
                        <span class="text-3xl font-black text-gray-900 dark:text-white bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 px-5 py-3 rounded-xl shadow-sm dark:shadow-md border border-gray-200 dark:border-gray-700">{{ $pendingPayments }}</span>
                    </div>
                </a>
                </div>

                {{-- قسم: الطلبات العاجلة --}}
                @if($oldestRequests->count() > 0)
                <div class="bg-gradient-to-br from-red-50 to-orange-50 dark:from-red-900/20 dark:to-orange-900/20 rounded-xl p-5 border-2 border-red-200 dark:border-red-700/30 mt-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center animate-pulse">
                            <i data-lucide="alert-triangle" class="w-5 h-5 text-white"></i>
                        </div>
                        <h2 class="text-lg font-black text-red-900 dark:text-red-400">طلبات عاجلة</h2>
                    </div>
                    
                    <div class="space-y-2">
                        @foreach($oldestRequests as $request)
                            @php
                                $daysOld = now()->diffInDays($request->created_at);
                            @endphp
                            
                            <div class="bg-white dark:bg-dark-card rounded-lg p-3">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-bold text-gray-900 dark:text-white text-sm">{{ $request->invoice_number }}</span>
                                    <span class="bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 px-2 py-0.5 rounded text-xs font-bold">
                                        {{ $daysOld }} يوم
                                    </span>
                                </div>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">{{ $request->marketer->full_name }}</p>
                                <a href="{{ route('warehouse.requests.show', $request) }}" class="block w-full text-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg font-bold text-xs transition-all">
                                    معالجة فوراً
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

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

<style>
.alert-card {
    position: relative;
    border-width: 1px;
    box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.1), 0 4px 6px -2px rgba(239, 68, 68, 0.05);
}

.alert-card::before {
    content: '';
    position: absolute;
    inset: -2px;
    border-radius: 0.75rem;
    padding: 2px;
    background: linear-gradient(45deg, currentColor, transparent, currentColor);
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    opacity: 0;
    animation: borderGlow 3s ease-in-out infinite;
}

@keyframes borderGlow {
    0%, 100% { opacity: 0; }
    50% { opacity: 0.6; }
}

.dark .alert-card {
    box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.3), 0 4px 6px -2px rgba(239, 68, 68, 0.2);
}

.dark .alert-card::before {
    animation: borderGlowDark 3s ease-in-out infinite;
}

@keyframes borderGlowDark {
    0%, 100% { opacity: 0; }
    50% { opacity: 0.9; }
}

.alert-badge {
    position: relative;
}

.alert-badge::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 9999px;
    filter: blur(12px);
    opacity: 0.4;
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.alert-badge-red::before { background: rgb(239, 68, 68); }
.alert-badge-blue::before { background: rgb(59, 130, 246); }
.alert-badge-purple::before { background: rgb(168, 85, 247); }
.alert-badge-emerald::before { background: rgb(16, 185, 129); }
.alert-badge-orange::before { background: rgb(249, 115, 22); }
.alert-badge-cyan::before { background: rgb(6, 182, 212); }

.alert-card.border-red-200::before { color: rgb(239, 68, 68); }
.alert-card.border-blue-200::before { color: rgb(59, 130, 246); }
.alert-card.border-purple-200::before { color: rgb(168, 85, 247); }
.alert-card.border-emerald-200::before { color: rgb(16, 185, 129); }
.alert-card.border-orange-200::before { color: rgb(249, 115, 22); }
.alert-card.border-cyan-200::before { color: rgb(6, 182, 212); }
</style>
