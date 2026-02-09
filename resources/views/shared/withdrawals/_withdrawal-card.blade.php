<div class="bg-white dark:bg-dark-card rounded-2xl p-6 shadow-lg shadow-gray-200/50 dark:shadow-none border border-gray-200 dark:border-dark-border hover:shadow-xl transition-all duration-300 group mb-4">
    <div class="flex items-start justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-100 to-amber-200 dark:from-amber-900/30 dark:to-amber-800/30 flex items-center justify-center">
                <i data-lucide="wallet" class="w-6 h-6 text-amber-600 dark:text-amber-400"></i>
            </div>
            <div>
                <h3 class="font-black text-lg text-gray-900 dark:text-white">طلب سحب #{{ $withdrawal->id }}</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $withdrawal->created_at->format('Y-m-d H:i') }}</p>
            </div>
        </div>
        @if($withdrawal->status === 'pending')
            <span class="px-3 py-1.5 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 rounded-xl text-xs font-bold">قيد الانتظار</span>
        @elseif($withdrawal->status === 'approved')
            <span class="px-3 py-1.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 rounded-xl text-xs font-bold">موافق عليه</span>
        @elseif($withdrawal->status === 'rejected')
            <span class="px-3 py-1.5 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-xl text-xs font-bold">مرفوض</span>
        @else
            <span class="px-3 py-1.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl text-xs font-bold">ملغي</span>
        @endif
    </div>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div class="bg-gray-50 dark:bg-dark-bg rounded-xl p-3">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">المبلغ المطلوب</p>
            <p class="text-lg font-black text-gray-900 dark:text-white">{{ number_format($withdrawal->requested_amount, 2) }} دينار</p>
        </div>
        <div class="bg-gray-50 dark:bg-dark-bg rounded-xl p-3">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">المسوق</p>
            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $withdrawal->marketer->full_name }}</p>
        </div>
    </div>

    <a href="{{ $viewRoute }}" class="block w-full py-3 bg-gray-100 dark:bg-dark-bg hover:bg-amber-50 dark:hover:bg-amber-500/10 text-gray-700 dark:text-gray-300 hover:text-amber-700 dark:hover:text-amber-400 rounded-xl font-bold text-center transition-all group-hover:shadow-md flex items-center justify-center gap-2">
        <i data-lucide="eye" class="w-4 h-4"></i>
        عرض التفاصيل
    </a>
</div>
