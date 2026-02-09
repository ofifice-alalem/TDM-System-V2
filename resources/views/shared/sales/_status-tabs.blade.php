<div class="bg-white dark:bg-dark-card rounded-2xl p-2 shadow-lg shadow-gray-200/50 dark:shadow-none border border-gray-200 dark:border-dark-border mb-6">
    <div class="flex flex-wrap gap-2">
        <a href="{{ $route([]) }}" class="{{ !request('all') && !request('status') ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border-2 border-amber-200 dark:border-amber-800' : 'bg-gray-100 dark:bg-dark-bg text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-dark-border' }} px-3 py-2.5 rounded-xl font-bold transition-all text-sm flex items-center gap-1.5 flex-1 basis-[calc(33.333%-0.5rem)] md:basis-auto justify-center">
            <i data-lucide="clock" class="w-4 h-4"></i>
            قيد الانتظار
        </a>
        <a href="{{ $route(['status' => 'approved']) }}" class="{{ request('status') === 'approved' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border-2 border-emerald-200 dark:border-emerald-800' : 'bg-gray-100 dark:bg-dark-bg text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-dark-border' }} px-3 py-2.5 rounded-xl font-bold transition-all text-sm flex items-center gap-1.5 flex-1 basis-[calc(33.333%-0.5rem)] md:basis-auto justify-center">
            <i data-lucide="check-circle" class="w-4 h-4"></i>
            موثق
        </a>
        <a href="{{ $route(['status' => 'rejected']) }}" class="{{ request('status') === 'rejected' ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 border-2 border-red-200 dark:border-red-800' : 'bg-gray-100 dark:bg-dark-bg text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-dark-border' }} px-3 py-2.5 rounded-xl font-bold transition-all text-sm flex items-center gap-1.5 flex-1 basis-[calc(33.333%-0.5rem)] md:basis-auto justify-center">
            <i data-lucide="x-circle" class="w-4 h-4"></i>
            مرفوض
        </a>
        <a href="{{ $route(['status' => 'cancelled']) }}" class="{{ request('status') === 'cancelled' ? 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-2 border-gray-300 dark:border-gray-600' : 'bg-gray-100 dark:bg-dark-bg text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-dark-border' }} px-3 py-2.5 rounded-xl font-bold transition-all text-sm flex items-center gap-1.5 flex-1 basis-[calc(33.333%-0.5rem)] md:basis-auto justify-center">
            <i data-lucide="slash" class="w-4 h-4"></i>
            ملغي
        </a>
        <a href="{{ $route(['all' => '1']) }}" class="{{ request('all') ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-dark-bg text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-dark-border' }} px-3 py-2.5 rounded-xl font-bold transition-all text-sm flex items-center gap-1.5 flex-1 basis-[calc(33.333%-0.5rem)] md:basis-auto justify-center">
            <i data-lucide="list" class="w-4 h-4"></i>
            الكل
        </a>
    </div>
</div>
