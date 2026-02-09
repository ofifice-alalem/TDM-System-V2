<div class="flex gap-2 mb-6 overflow-x-auto pb-2">
    <a href="{{ $route(['all' => 1]) }}" class="px-6 py-3 rounded-xl font-bold transition-all whitespace-nowrap {{ !request()->has('status') && request()->has('all') ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900 shadow-lg' : 'bg-white dark:bg-dark-card text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-dark-bg border border-gray-200 dark:border-dark-border' }}">
        <i data-lucide="list" class="w-4 h-4 inline-block ml-1"></i>
        الكل
    </a>
    <a href="{{ $route([]) }}" class="px-6 py-3 rounded-xl font-bold transition-all whitespace-nowrap {{ !request()->has('status') && !request()->has('all') ? 'bg-amber-500 dark:bg-amber-600 text-white shadow-lg shadow-amber-200/50 dark:shadow-none' : 'bg-white dark:bg-dark-card text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-dark-bg border border-gray-200 dark:border-dark-border' }}">
        <i data-lucide="clock" class="w-4 h-4 inline-block ml-1"></i>
        قيد الانتظار
    </a>
    <a href="{{ $route(['status' => 'approved']) }}" class="px-6 py-3 rounded-xl font-bold transition-all whitespace-nowrap {{ request('status') === 'approved' ? 'bg-emerald-500 dark:bg-emerald-600 text-white shadow-lg shadow-emerald-200/50 dark:shadow-none' : 'bg-white dark:bg-dark-card text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-dark-bg border border-gray-200 dark:border-dark-border' }}">
        <i data-lucide="check-circle" class="w-4 h-4 inline-block ml-1"></i>
        موثق
    </a>
    <a href="{{ $route(['status' => 'cancelled']) }}" class="px-6 py-3 rounded-xl font-bold transition-all whitespace-nowrap {{ request('status') === 'cancelled' ? 'bg-gray-500 dark:bg-gray-600 text-white shadow-lg' : 'bg-white dark:bg-dark-card text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-dark-bg border border-gray-200 dark:border-dark-border' }}">
        <i data-lucide="slash" class="w-4 h-4 inline-block ml-1"></i>
        ملغي
    </a>
</div>
