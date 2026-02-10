@php
    $statusConfig = [
        'pending' => ['bg' => 'bg-amber-50 dark:bg-amber-900/20', 'text' => 'text-amber-700 dark:text-amber-400', 'iconBg' => 'bg-amber-100 dark:bg-amber-900/40', 'icon' => 'clock', 'label' => 'قيد الانتظار'],
        'approved' => ['bg' => 'bg-emerald-50 dark:bg-emerald-900/20', 'text' => 'text-emerald-700 dark:text-emerald-400', 'iconBg' => 'bg-emerald-100 dark:bg-emerald-900/40', 'icon' => 'check-circle', 'label' => 'موثق'],
        'rejected' => ['bg' => 'bg-red-50 dark:bg-red-900/20', 'text' => 'text-red-700 dark:text-red-400', 'iconBg' => 'bg-red-100 dark:bg-red-900/40', 'icon' => 'x-circle', 'label' => 'مرفوض'],
        'cancelled' => ['bg' => 'bg-gray-50 dark:bg-gray-800/50', 'text' => 'text-gray-600 dark:text-gray-400', 'iconBg' => 'bg-gray-100 dark:bg-gray-700', 'icon' => 'slash', 'label' => 'ملغي'],
    ][$return->status] ?? ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'iconBg' => 'bg-gray-100', 'icon' => 'help-circle', 'label' => $return->status];
@endphp

<div class="bg-gray-50 dark:bg-dark-bg/60 rounded-2xl border border-gray-200 dark:border-dark-border mb-3 first:mt-4 md:first:mt-0 last:mb-4 md:last:mb-0 hover:shadow-md transition-all overflow-hidden">
    <div class="flex flex-row-reverse">
        <div class="{{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} px-3 py-4 text-sm font-bold flex flex-col items-center justify-center gap-3 border-r {{ $statusConfig['iconBg'] }}/30 shadow-[inset_-4px_0_8px_-2px_rgba(0,0,0,0.1)]" style="box-shadow: inset -6px 0 12px -2px {{ $return->status === 'pending' ? 'rgba(217,119,6,0.5)' : ($return->status === 'approved' ? 'rgba(16,185,129,0.5)' : ($return->status === 'rejected' ? 'rgba(220,38,38,0.5)' : 'rgba(107,114,128,0.5)')) }};">
            <span class="{{ $statusConfig['iconBg'] }} {{ $statusConfig['text'] }} w-8 h-8 rounded-full flex items-center justify-center shrink-0">
                <i data-lucide="{{ $statusConfig['icon'] }}" class="w-4 h-4"></i>
            </span>
            <span style="writing-mode: vertical-rl; text-orientation: mixed;">{{ $statusConfig['label'] }}</span>
        </div>

        <div class="flex-1 p-4 md:p-6 flex items-center">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 w-full">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <h3 class="text-xl md:text-lg font-black text-gray-900 dark:text-white">#{{ $return->return_number }}</h3>
                    </div>
                    
                    {!! $slot ?? '' !!}
                    
                    <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400 font-medium mt-3">
                        <div class="flex items-center gap-1.5">
                            <i data-lucide="calendar" class="w-4 h-4"></i>
                            <span>{{ $return->created_at->format('Y-m-d') }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <i data-lucide="package" class="w-4 h-4"></i>
                            <span>{{ $return->items->count() }} منتج</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <i data-lucide="coins" class="w-4 h-4"></i>
                            <span class="font-bold text-primary-600 dark:text-primary-400">{{ number_format($return->total_amount, 2) }} دينار</span>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2 md:mr-auto">
                    {!! $actions ?? '' !!}
                </div>
            </div>
        </div>
    </div>
</div>
