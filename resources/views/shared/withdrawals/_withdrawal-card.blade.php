@php
    $statusConfig = [
        'pending' => ['bg' => 'bg-amber-50 dark:bg-amber-900/20', 'text' => 'text-amber-700 dark:text-amber-400', 'iconBg' => 'bg-amber-100 dark:bg-amber-900/40', 'icon' => 'clock', 'label' => 'قيد الانتظار'],
        'approved' => ['bg' => 'bg-emerald-50 dark:bg-emerald-900/20', 'text' => 'text-emerald-700 dark:text-emerald-400', 'iconBg' => 'bg-emerald-100 dark:bg-emerald-900/40', 'icon' => 'check-circle', 'label' => 'موافق عليه'],
        'rejected' => ['bg' => 'bg-red-50 dark:bg-red-900/20', 'text' => 'text-red-700 dark:text-red-400', 'iconBg' => 'bg-red-100 dark:bg-red-900/40', 'icon' => 'x-circle', 'label' => 'مرفوض'],
        'cancelled' => ['bg' => 'bg-gray-50 dark:bg-gray-800/50', 'text' => 'text-gray-600 dark:text-gray-400', 'iconBg' => 'bg-gray-100 dark:bg-gray-700', 'icon' => 'slash', 'label' => 'ملغي'],
    ][$withdrawal->status] ?? ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'iconBg' => 'bg-gray-100', 'icon' => 'help-circle', 'label' => $withdrawal->status];
@endphp

<div class="bg-gray-50 dark:bg-dark-bg/60 rounded-xl md:rounded-2xl border border-gray-200 dark:border-dark-border mb-3 first:mt-4 md:first:mt-0 last:mb-4 md:last:mb-0 hover:shadow-lg hover:border-gray-300 dark:hover:border-dark-border transition-all overflow-hidden group">
    <div class="flex flex-row-reverse">
        <div class="{{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} px-2 md:px-3 py-3 md:py-4 text-sm font-bold flex flex-col items-center justify-center gap-2 md:gap-3 border-r {{ $statusConfig['iconBg'] }}/30 shadow-[inset_-4px_0_8px_-2px_rgba(0,0,0,0.1)]" style="box-shadow: inset -6px 0 12px -2px {{ $withdrawal->status === 'pending' ? 'rgba(217,119,6,0.5)' : ($withdrawal->status === 'approved' ? 'rgba(16,185,129,0.5)' : ($withdrawal->status === 'rejected' ? 'rgba(220,38,38,0.5)' : 'rgba(107,114,128,0.5)')) }};">
            <span class="{{ $statusConfig['iconBg'] }} {{ $statusConfig['text'] }} w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center shrink-0 shadow-sm">
                <i data-lucide="{{ $statusConfig['icon'] }}" class="w-4 h-4 md:w-5 md:h-5"></i>
            </span>
            <span style="writing-mode: vertical-rl; text-orientation: mixed;" class="text-[10px] md:text-xs">{{ $statusConfig['label'] }}</span>
        </div>

        <div class="flex-1 p-3 md:p-5 lg:p-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 md:gap-4">
                <div class="flex-1 space-y-2 md:space-y-3">
                    <div class="flex items-center gap-2 md:gap-3">
                        <div class="w-8 h-8 md:w-10 md:h-10 bg-amber-100 dark:bg-amber-900/30 rounded-lg md:rounded-xl flex items-center justify-center text-amber-600 dark:text-amber-400 shadow-sm">
                            <i data-lucide="hand-coins" class="w-4 h-4 md:w-5 md:h-5"></i>
                        </div>
                        <div>
                            <h3 class="text-base md:text-lg font-black text-gray-900 dark:text-white">طلب سحب #{{ $withdrawal->id }}</h3>
                            <p class="text-[10px] md:text-xs text-gray-400 dark:text-dark-muted font-medium">سحب أرباح</p>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-2 md:gap-3 text-xs md:text-sm">
                        <div class="flex items-center gap-1 md:gap-1.5 bg-white dark:bg-dark-card px-2 md:px-3 py-1 md:py-1.5 rounded-md md:rounded-lg border border-gray-200 dark:border-dark-border">
                            <i data-lucide="calendar" class="w-3 h-3 md:w-3.5 md:h-3.5 text-gray-400"></i>
                            <span class="text-gray-600 dark:text-gray-400 font-medium">{{ $withdrawal->created_at->format('Y-m-d') }}</span>
                        </div>
                        <div class="flex items-center gap-1 md:gap-1.5 bg-white dark:bg-dark-card px-2 md:px-3 py-1 md:py-1.5 rounded-md md:rounded-lg border border-gray-200 dark:border-dark-border">
                            <i data-lucide="user" class="w-3 h-3 md:w-3.5 md:h-3.5 text-blue-500"></i>
                            <span class="text-gray-600 dark:text-gray-400 font-medium">{{ $withdrawal->marketer->full_name }}</span>
                        </div>
                        <div class="flex items-center justify-center gap-1 md:gap-1.5 bg-amber-50 dark:bg-amber-900/20 px-3 md:px-4 py-1.5 md:py-2 rounded-md md:rounded-lg border border-amber-200 dark:border-amber-800 w-full md:w-auto">
                            <i data-lucide="dollar-sign" class="w-4 h-4 md:w-4 md:h-4 text-amber-600 dark:text-amber-400"></i>
                            <span class="font-black text-amber-600 dark:text-amber-400 text-base md:text-lg">{{ number_format($withdrawal->requested_amount, 2) }}</span>
                            <span class="text-xs md:text-sm text-amber-500 dark:text-amber-500 font-bold">دينار</span>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2 md:flex-col">
                    <a href="{{ $viewRoute }}" class="w-full md:w-auto px-4 md:px-6 py-2.5 md:py-3 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white rounded-lg md:rounded-xl font-bold transition-all flex items-center justify-center gap-2 shadow-md hover:shadow-lg text-xs md:text-sm">
                        <i data-lucide="eye" class="w-3.5 h-3.5 md:w-4 md:h-4"></i>
                        عرض التفاصيل
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
