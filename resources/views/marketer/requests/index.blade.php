@extends('layouts.app')

@section('title', 'طلبات البضاعة')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-7xl mx-auto space-y-8 px-1 lg:px-8">
        
        {{-- Header & Quick Actions --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 animate-fade-in-down">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                        إدارة الطلبات
                    </span>
                </div>
                <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                    طلبات البضاعة
                </h1>
            </div>

            <div class="flex gap-3 w-full md:w-auto">
                <a href="{{ route('marketer.requests.create') }}" class="px-8 py-4 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all shadow-lg shadow-primary-200/50 dark:shadow-none flex items-center justify-center gap-2 flex-1 md:flex-auto">
                    <i data-lucide="plus-circle" class="w-5 h-5"></i>
                    طلب جديد
                </a>
            </div>
        </div>

        {{-- Requests List --}}
        <div class="bg-white dark:bg-dark-card rounded-[2rem] p-1.5 md:p-8 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border animate-slide-up">
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
                                        <h3 class="text-2xl md:text-lg font-black text-gray-900 dark:text-white">#{{ $request->invoice_number }}</h3>
                                    </div>
                                    <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-dark-muted">
                                        <div class="flex items-center gap-1">
                                            <i data-lucide="calendar" class="w-4 h-4"></i>
                                            <span>{{ $request->created_at->format('Y-m-d') }}</span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <i data-lucide="package" class="w-4 h-4"></i>
                                            <span>{{ $request->items->count() }} منتج</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex gap-2">
                                    <a href="{{ route('marketer.requests.show', $request) }}" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 dark:bg-gray-600 dark:hover:bg-gray-700 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2">
                                        <i data-lucide="file-text" class="w-4 h-4"></i>
                                        عرض التفاصيل
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
