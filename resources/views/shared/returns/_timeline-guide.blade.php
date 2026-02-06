{{-- Timeline Guide --}}
<div class="bg-white dark:bg-dark-card rounded-[1.5rem] border border-gray-200 dark:border-dark-border p-8 shadow-lg shadow-gray-200/50 dark:shadow-sm lg:sticky lg:top-[150px]">
    <h3 class="font-bold text-xl text-gray-900 dark:text-white mb-8 flex items-center gap-3">
        <i data-lucide="info" class="w-6 h-6 text-primary-500"></i>
        دليل حالات الإرجاع
    </h3>
    
    <div class="relative space-y-8 before:absolute before:inset-0 before:mr-[21px] before:h-full before:w-0.5 before:bg-gradient-to-b before:from-gray-200 dark:before:from-dark-border before:via-gray-100 dark:before:via-dark-bg before:to-transparent">
        
        <div class="relative flex items-start gap-5">
            <div class="w-11 h-11 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 shadow-sm z-10 border-2 border-white dark:border-dark-card">
                <i data-lucide="clock" class="w-5 h-5"></i>
            </div>
            <div>
                <h4 class="font-bold text-gray-900 dark:text-white text-base mb-1">قيد الانتظار</h4>
                <p class="text-sm text-gray-500 dark:text-dark-muted leading-relaxed">تم الإنشاء وبانتظار المراجعة</p>
            </div>
        </div>

        <div class="relative flex items-start gap-5">
            <div class="w-11 h-11 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 shadow-sm z-10 border-2 border-white dark:border-dark-card">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
            </div>
            <div>
                <h4 class="font-bold text-gray-900 dark:text-white text-base mb-1">تمت الموافقة</h4>
                <p class="text-sm text-gray-500 dark:text-dark-muted leading-relaxed">تمت الموافقة، بانتظار الاستلام</p>
            </div>
        </div>

        <div class="relative flex items-start gap-5">
            <div class="w-11 h-11 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 shadow-sm z-10 border-2 border-white dark:border-dark-card">
                <i data-lucide="file-check" class="w-5 h-5"></i>
            </div>
            <div>
                <h4 class="font-bold text-gray-900 dark:text-white text-base mb-1">موثق</h4>
                <p class="text-sm text-gray-500 dark:text-dark-muted leading-relaxed">تم التوثيق وارجاع البضاعة</p>
            </div>
        </div>

        <div class="relative flex items-start gap-5">
            <div class="w-11 h-11 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center shrink-0 shadow-sm z-10 border-2 border-white dark:border-dark-card">
                <i data-lucide="x-circle" class="w-5 h-5"></i>
            </div>
            <div>
                <h4 class="font-bold text-gray-900 dark:text-white text-base mb-1">مرفوض</h4>
                <p class="text-sm text-gray-500 dark:text-dark-muted leading-relaxed">تم الرفض من قبل المخزن</p>
            </div>
        </div>

        <div class="relative flex items-start gap-5">
            <div class="w-11 h-11 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-500 flex items-center justify-center shrink-0 shadow-sm z-10 border-2 border-white dark:border-dark-card">
                <i data-lucide="slash" class="w-5 h-5"></i>
            </div>
            <div>
                <h4 class="font-bold text-gray-900 dark:text-white text-base mb-1">ملغي</h4>
                <p class="text-sm text-gray-500 dark:text-dark-muted leading-relaxed">تم الإلغاء من قبل المسوق</p>
            </div>
        </div>
        
    </div>
</div>
