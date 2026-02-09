@php
    $statusConfig = [
        'pending' => ['bg' => 'bg-amber-50 dark:bg-amber-900/20', 'text' => 'text-amber-700 dark:text-amber-400', 'iconBg' => 'bg-amber-100 dark:bg-amber-900/40', 'icon' => 'clock', 'label' => 'قيد الانتظار'],
        'approved' => ['bg' => 'bg-emerald-50 dark:bg-emerald-900/20', 'text' => 'text-emerald-700 dark:text-emerald-400', 'iconBg' => 'bg-emerald-100 dark:bg-emerald-900/40', 'icon' => 'check-circle', 'label' => 'موثق'],
        'rejected' => ['bg' => 'bg-red-50 dark:bg-red-900/20', 'text' => 'text-red-700 dark:text-red-400', 'iconBg' => 'bg-red-100 dark:bg-red-900/40', 'icon' => 'x-circle', 'label' => 'مرفوض'],
        'cancelled' => ['bg' => 'bg-gray-50 dark:bg-gray-800/50', 'text' => 'text-gray-600 dark:text-gray-400', 'iconBg' => 'bg-gray-100 dark:bg-gray-700', 'icon' => 'slash', 'label' => 'ملغي'],
    ][$payment->status] ?? ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'iconBg' => 'bg-gray-100', 'icon' => 'help-circle', 'label' => $payment->status];

    $methodLabels = [
        'cash' => 'نقدي',
        'transfer' => 'تحويل بنكي',
        'certified_check' => 'شيك مصدق',
    ];
@endphp

<div class="bg-gray-50 dark:bg-dark-bg/60 rounded-2xl border border-gray-200 dark:border-dark-border mb-3 first:mt-4 md:first:mt-0 last:mb-4 md:last:mb-0 hover:shadow-lg hover:border-gray-300 dark:hover:border-dark-border transition-all overflow-hidden group">
    <div class="flex flex-row-reverse">
        <div class="{{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} px-3 py-4 text-sm font-bold flex flex-col items-center justify-center gap-3 border-r {{ $statusConfig['iconBg'] }}/30 shadow-[inset_-4px_0_8px_-2px_rgba(0,0,0,0.1)]" style="box-shadow: inset -6px 0 12px -2px {{ $payment->status === 'pending' ? 'rgba(217,119,6,0.5)' : ($payment->status === 'approved' ? 'rgba(16,185,129,0.5)' : ($payment->status === 'rejected' ? 'rgba(220,38,38,0.5)' : 'rgba(107,114,128,0.5)')) }};">
            <span class="{{ $statusConfig['iconBg'] }} {{ $statusConfig['text'] }} w-10 h-10 rounded-full flex items-center justify-center shrink-0 shadow-sm">
                <i data-lucide="{{ $statusConfig['icon'] }}" class="w-5 h-5"></i>
            </span>
            <span style="writing-mode: vertical-rl; text-orientation: mixed;" class="text-xs">{{ $statusConfig['label'] }}</span>
        </div>

        <div class="flex-1 p-5 md:p-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex-1 space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center text-emerald-600 dark:text-emerald-400 shadow-sm">
                            <i data-lucide="banknote" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-gray-900 dark:text-white">#{{ $payment->payment_number }}</h3>
                            <p class="text-xs text-gray-400 dark:text-dark-muted font-medium">إيصال قبض</p>
                        </div>
                    </div>
                    
                    {!! $slot ?? '' !!}
                    
                    <div class="flex flex-wrap items-center gap-3 text-sm">
                        <div class="flex items-center gap-1.5 bg-white dark:bg-dark-card px-3 py-1.5 rounded-lg border border-gray-200 dark:border-dark-border">
                            <i data-lucide="calendar" class="w-3.5 h-3.5 text-gray-400"></i>
                            <span class="text-gray-600 dark:text-gray-400 font-medium">{{ $payment->created_at->format('Y-m-d') }}</span>
                        </div>
                        <div class="flex items-center gap-1.5 bg-white dark:bg-dark-card px-3 py-1.5 rounded-lg border border-gray-200 dark:border-dark-border">
                            <i data-lucide="credit-card" class="w-3.5 h-3.5 text-blue-500"></i>
                            <span class="text-gray-600 dark:text-gray-400 font-medium">{{ $methodLabels[$payment->payment_method] ?? $payment->payment_method }}</span>
                        </div>
                        <div class="flex items-center gap-1.5 bg-emerald-50 dark:bg-emerald-900/20 px-3 py-1.5 rounded-lg border border-emerald-200 dark:border-emerald-800">
                            <i data-lucide="dollar-sign" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400"></i>
                            <span class="font-black text-emerald-600 dark:text-emerald-400">{{ number_format($payment->amount, 2) }}</span>
                            <span class="text-xs text-emerald-500 dark:text-emerald-500">دينار</span>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2 md:flex-col">
                    {!! $actions ?? '' !!}
                </div>
            </div>
        </div>
    </div>
</div>
