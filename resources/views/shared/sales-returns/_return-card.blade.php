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
        <div class="{{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} px-3 py-4 text-sm font-bold flex flex-col items-center justify-center gap-3 border-r {{ $statusConfig['iconBg'] }}/30">
            <span class="{{ $statusConfig['iconBg'] }} {{ $statusConfig['text'] }} w-8 h-8 rounded-full flex items-center justify-center shrink-0">
                <i data-lucide="{{ $statusConfig['icon'] }}" class="w-4 h-4"></i>
            </span>
            <span style="writing-mode: vertical-rl; text-orientation: mixed;">{{ $statusConfig['label'] }}</span>
        </div>

        <div class="flex-1 p-4 md:p-6 flex items-center">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 w-full">
                <div class="flex-1 md:order-1">
                    <div class="flex items-center gap-3 mb-2">
                        <h3 class="text-xl md:text-lg font-black text-gray-900 dark:text-white">#{{ $return->return_number }}</h3>
                    </div>
                    
                    {!! $slot ?? '' !!}
                    
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 md:gap-4 bg-white dark:bg-dark-card rounded-xl p-3 border border-gray-200 dark:border-dark-border">
                        {!! $storeSlot ?? '' !!}
                        <div class="flex items-center gap-1.5 text-base text-gray-600 dark:text-gray-400 font-medium">
                            <i data-lucide="dollar-sign" class="w-5 h-5"></i>
                            <span class="font-bold">{{ number_format($return->total_amount, 2) }} دينار</span>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2 md:order-2">
                    {!! $actions ?? '' !!}
                </div>
            </div>
        </div>
    </div>
</div>
