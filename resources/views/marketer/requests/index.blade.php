@extends('layouts.app')

@section('title', 'طلبات البضاعة')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-[1600px] mx-auto space-y-8 px-2">
        
        {{-- Header & Quick Actions --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 animate-fade-in-down">
            <div class="lg:col-span-8">
                <div class="flex items-center gap-3 mb-2">
                    <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                        إدارة الطلبات
                    </span>
                </div>
                <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                    طلبات البضاعة
                </h1>
            </div>

            <div class="lg:col-span-4 lg:translate-y-[30px]">
                <a href="{{ route('marketer.requests.create') }}" class="px-8 py-4 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all shadow-lg shadow-primary-200/50 dark:shadow-none flex items-center justify-center gap-2 w-full">
                    <i data-lucide="plus-circle" class="w-5 h-5"></i>
                    طلب جديد
                </a>
            </div>
        </div>

        {{-- Requests List --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- Main List --}}
            <div class="lg:col-span-8">
                {{-- Status Tabs --}}
                <div class="bg-white dark:bg-dark-card rounded-2xl p-2 shadow-lg shadow-gray-200/50 dark:shadow-none border border-gray-200 dark:border-dark-border mb-6">
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('marketer.requests.index', ['status' => 'pending']) }}" class="{{ !request('all') && (!request('status') || request('status') === 'pending') ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border-2 border-amber-200 dark:border-amber-800' : 'bg-gray-100 dark:bg-dark-bg text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-dark-border' }} px-3 py-2.5 rounded-xl font-bold transition-all text-sm flex items-center gap-1.5 flex-1 basis-[calc(33.333%-0.5rem)] md:basis-auto justify-center">
                            <i data-lucide="clock" class="w-4 h-4"></i>
                            قيد الانتظار
                        </a>
                        <a href="{{ route('marketer.requests.index', ['status' => 'approved']) }}" class="{{ request('status') === 'approved' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 border-2 border-blue-200 dark:border-blue-800' : 'bg-gray-100 dark:bg-dark-bg text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-dark-border' }} px-3 py-2.5 rounded-xl font-bold transition-all text-sm flex items-center gap-1.5 flex-1 basis-[calc(33.333%-0.5rem)] md:basis-auto md:flex-[1.2] justify-center">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                            تمت الموافقة
                        </a>
                        <a href="{{ route('marketer.requests.index', ['status' => 'documented']) }}" class="{{ request('status') === 'documented' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border-2 border-emerald-200 dark:border-emerald-800' : 'bg-gray-100 dark:bg-dark-bg text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-dark-border' }} px-3 py-2.5 rounded-xl font-bold transition-all text-sm flex items-center gap-1.5 flex-1 basis-[calc(33.333%-0.5rem)] md:basis-auto justify-center">
                            <i data-lucide="file-check" class="w-4 h-4"></i>
                            موثق
                        </a>
                        <a href="{{ route('marketer.requests.index', ['status' => 'rejected']) }}" class="{{ request('status') === 'rejected' ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 border-2 border-red-200 dark:border-red-800' : 'bg-gray-100 dark:bg-dark-bg text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-dark-border' }} px-3 py-2.5 rounded-xl font-bold transition-all text-sm flex items-center gap-1.5 flex-1 basis-[calc(33.333%-0.5rem)] md:basis-auto justify-center">
                            <i data-lucide="x-circle" class="w-4 h-4"></i>
                            مرفوض
                        </a>
                        <a href="{{ route('marketer.requests.index', ['status' => 'cancelled']) }}" class="{{ request('status') === 'cancelled' ? 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-2 border-gray-300 dark:border-gray-600' : 'bg-gray-100 dark:bg-dark-bg text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-dark-border' }} px-3 py-2.5 rounded-xl font-bold transition-all text-sm flex items-center gap-1.5 flex-1 basis-[calc(33.333%-0.5rem)] md:basis-auto justify-center">
                            <i data-lucide="slash" class="w-4 h-4"></i>
                            ملغي
                        </a>
                        <a href="{{ route('marketer.requests.index', ['all' => '1']) }}" class="{{ request('all') ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-dark-bg text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-dark-border' }} px-3 py-2.5 rounded-xl font-bold transition-all text-sm flex items-center gap-1.5 flex-1 basis-[calc(33.333%-0.5rem)] md:basis-auto justify-center">
                            <i data-lucide="list" class="w-4 h-4"></i>
                            الكل
                        </a>
                    </div>
                </div>

                <div class="bg-white dark:bg-dark-card rounded-[2rem] p-4 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border animate-slide-up">
            @forelse($requests as $request)
                <div class="bg-gray-50 dark:bg-dark-bg/60 rounded-2xl border border-gray-200 dark:border-dark-border mb-3 first:mt-4 md:first:mt-0 last:mb-4 md:last:mb-0 hover:shadow-md transition-all overflow-hidden">
                    @php
                        $statusConfig = [
                            'pending' => ['bg' => 'bg-amber-50 dark:bg-amber-900/20', 'text' => 'text-amber-700 dark:text-amber-400', 'iconBg' => 'bg-amber-100 dark:bg-amber-900/40', 'icon' => 'clock', 'label' => 'قيد الانتظار'],
                            'approved' => ['bg' => 'bg-blue-50 dark:bg-blue-900/20', 'text' => 'text-blue-700 dark:text-blue-400', 'iconBg' => 'bg-blue-100 dark:bg-blue-900/40', 'icon' => 'check-circle', 'label' => 'تمت الموافقة'],
                            'documented' => ['bg' => 'bg-emerald-50 dark:bg-emerald-900/20', 'text' => 'text-emerald-700 dark:text-emerald-400', 'iconBg' => 'bg-emerald-100 dark:bg-emerald-900/40', 'icon' => 'file-check', 'label' => 'موثق'],
                            'rejected' => ['bg' => 'bg-red-50 dark:bg-red-900/20', 'text' => 'text-red-700 dark:text-red-400', 'iconBg' => 'bg-red-100 dark:bg-red-900/40', 'icon' => 'x-circle', 'label' => 'مرفوض'],
                            'cancelled' => ['bg' => 'bg-gray-50 dark:bg-gray-800/50', 'text' => 'text-gray-600 dark:text-gray-400', 'iconBg' => 'bg-gray-100 dark:bg-gray-700', 'icon' => 'slash', 'label' => 'ملغي'],
                        ][$request->status] ?? ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'iconBg' => 'bg-gray-100', 'icon' => 'help-circle', 'label' => $request->status];
                    @endphp
                    
                    <div class="flex flex-row-reverse">
                        <div class="{{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} px-3 py-4 text-sm font-bold flex flex-col items-center justify-center gap-3 border-r {{ $statusConfig['iconBg'] }}/30 shadow-[inset_-4px_0_8px_-2px_rgba(0,0,0,0.1)]" style="box-shadow: inset -6px 0 12px -2px {{ $request->status === 'pending' ? 'rgba(217,119,6,0.5)' : ($request->status === 'approved' ? 'rgba(37,99,235,0.5)' : ($request->status === 'documented' ? 'rgba(16,185,129,0.5)' : ($request->status === 'rejected' ? 'rgba(220,38,38,0.5)' : 'rgba(107,114,128,0.5)'))) }};">
                            <span class="{{ $statusConfig['iconBg'] }} {{ $statusConfig['text'] }} w-8 h-8 rounded-full flex items-center justify-center shrink-0">
                                <i data-lucide="{{ $statusConfig['icon'] }}" class="w-4 h-4"></i>
                            </span>
                            <span style="writing-mode: vertical-rl; text-orientation: mixed;">{{ $statusConfig['label'] }}</span>
                        </div>

                        <div class="flex-1 p-4 md:p-6">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h3 class="text-xl md:text-lg font-black text-gray-900 dark:text-white">#{{ $request->invoice_number }}</h3>
                                    </div>
                                    <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400 font-medium">
                                        <div class="flex items-center gap-1.5">
                                            <i data-lucide="calendar" class="w-4 h-4"></i>
                                            <span>{{ $request->created_at->format('Y-m-d') }}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <i data-lucide="package" class="w-4 h-4"></i>
                                            <span>{{ $request->items->count() }} منتج</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex gap-2">
                                    <a href="{{ route('marketer.requests.show', $request) }}" class="px-5 py-2.5 bg-white dark:bg-dark-card border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-400 dark:hover:border-gray-500 transition-all text-sm flex items-center gap-2 shadow-sm">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                        التفاصيل
                                    </a>
                                    @if(in_array($request->status, ['pending', 'approved']))
                                        <button type="button" onclick="confirmCancel({{ $request->id }})" class="px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 rounded-xl font-bold hover:bg-red-100 dark:hover:bg-red-900/30 transition-all text-sm flex items-center gap-2">
                                            <i data-lucide="x-circle" class="w-4 h-4"></i>
                                            إلغاء
                                        </button>
                                        <form id="cancel-form-{{ $request->id }}" action="{{ route('marketer.requests.cancel', $request) }}" method="POST" class="hidden">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="notes" id="cancel-notes-{{ $request->id }}">
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-gray-100 dark:bg-dark-bg rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="inbox" class="w-10 h-10 text-gray-400 dark:text-gray-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">لا توجد طلبات</h3>
                    <p class="text-gray-500 dark:text-dark-muted mb-6">لم تقم بإنشاء أي طلبات بعد</p>
                    <a href="{{ route('marketer.requests.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all">
                        <i data-lucide="plus-circle" class="w-5 h-5"></i>
                        إنشاء طلب جديد
                    </a>
                </div>
            @endforelse

            {{-- Pagination --}}
            @if($requests->hasPages())
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-dark-border">
                    {{ $requests->links() }}
                </div>
            @endif
                </div>
            </div>

            {{-- Timeline Guide --}}
            <div class="lg:col-span-4">
                <div class="bg-white dark:bg-dark-card rounded-[1.5rem] border border-gray-200 dark:border-dark-border p-8 shadow-lg shadow-gray-200/50 dark:shadow-sm lg:sticky lg:top-[150px]">
                    <h3 class="font-bold text-xl text-gray-900 dark:text-white mb-8 flex items-center gap-3">
                        <i data-lucide="info" class="w-6 h-6 text-primary-500"></i>
                        دليل حالات الطلب
                    </h3>
                    
                    <div class="relative space-y-8 before:absolute before:inset-0 before:mr-[21px] before:h-full before:w-0.5 before:bg-gradient-to-b before:from-gray-200 dark:before:from-dark-border before:via-gray-100 dark:before:via-dark-bg before:to-transparent">
                        
                        <div class="relative flex items-start gap-5">
                            <div class="w-11 h-11 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 shadow-sm z-10 border-2 border-white dark:border-dark-card">
                                <i data-lucide="clock" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white text-base mb-1">قيد الانتظار</h4>
                                <p class="text-sm text-gray-500 dark:text-dark-muted leading-relaxed">الطلب تم إنشاؤه وبانتظار مراجعة المخزن</p>
                            </div>
                        </div>

                        <div class="relative flex items-start gap-5">
                            <div class="w-11 h-11 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 shadow-sm z-10 border-2 border-white dark:border-dark-card">
                                <i data-lucide="check-circle" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white text-base mb-1">تمت الموافقة</h4>
                                <p class="text-sm text-gray-500 dark:text-dark-muted leading-relaxed">تمت الموافقة من المخزن وجاري التجهيز</p>
                            </div>
                        </div>

                        <div class="relative flex items-start gap-5">
                            <div class="w-11 h-11 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 shadow-sm z-10 border-2 border-white dark:border-dark-card">
                                <i data-lucide="file-check" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white text-base mb-1">موثق</h4>
                                <p class="text-sm text-gray-500 dark:text-dark-muted leading-relaxed">تم توثيق الاستلام وإغلاق الطلب</p>
                            </div>
                        </div>

                        <div class="relative flex items-start gap-5">
                            <div class="w-11 h-11 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center shrink-0 shadow-sm z-10 border-2 border-white dark:border-dark-card">
                                <i data-lucide="x-circle" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white text-base mb-1">مرفوض</h4>
                                <p class="text-sm text-gray-500 dark:text-dark-muted leading-relaxed">تم رفض الطلب من قبل المخزن</p>
                            </div>
                        </div>

                        <div class="relative flex items-start gap-5">
                            <div class="w-11 h-11 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-500 flex items-center justify-center shrink-0 shadow-sm z-10 border-2 border-white dark:border-dark-card">
                                <i data-lucide="slash" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white text-base mb-1">ملغي</h4>
                                <p class="text-sm text-gray-500 dark:text-dark-muted leading-relaxed">تم إلغاء الطلب من قبل المسوق</p>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });

    function confirmCancel(requestId) {
        const notes = prompt('الرجاء إدخال سبب الإلغاء:');
        if (notes && notes.trim() !== '') {
            document.getElementById('cancel-notes-' + requestId).value = notes;
            document.getElementById('cancel-form-' + requestId).submit();
        }
    }
</script>
@endpush
@endsection
