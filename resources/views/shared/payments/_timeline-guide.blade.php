<div class="bg-white dark:bg-dark-card rounded-[2rem] p-6 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border sticky top-8 animate-slide-up" style="animation-delay: 0.1s">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 bg-primary-50 dark:bg-primary-500/10 rounded-xl flex items-center justify-center text-primary-600 dark:text-primary-400">
            <i data-lucide="info" class="w-5 h-5"></i>
        </div>
        <h3 class="text-lg font-black text-gray-900 dark:text-white">دليل الإجراءات</h3>
    </div>

    <div class="space-y-4">
        <div class="flex gap-3">
            <div class="flex flex-col items-center">
                <div class="w-8 h-8 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                </div>
                <div class="w-0.5 h-full bg-gray-200 dark:bg-dark-border mt-2"></div>
            </div>
            <div class="pb-4">
                <h4 class="font-bold text-gray-900 dark:text-white mb-1">قيد الانتظار</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">تم إنشاء الإيصال وبانتظار التوثيق من أمين المخزن</p>
            </div>
        </div>

        <div class="flex gap-3">
            <div class="flex flex-col items-center">
                <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                </div>
                <div class="w-0.5 h-full bg-gray-200 dark:bg-dark-border mt-2"></div>
            </div>
            <div class="pb-4">
                <h4 class="font-bold text-gray-900 dark:text-white mb-1">موثق</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">تم توثيق الإيصال وخصم المبلغ من الدين وتسجيل العمولة</p>
            </div>
        </div>

        <div class="flex gap-3">
            <div class="flex flex-col items-center">
                <div class="w-8 h-8 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center shrink-0">
                    <i data-lucide="x-circle" class="w-4 h-4"></i>
                </div>
                <div class="w-0.5 h-full bg-gray-200 dark:bg-dark-border mt-2"></div>
            </div>
            <div class="pb-4">
                <h4 class="font-bold text-gray-900 dark:text-white mb-1">مرفوض</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">تم رفض الإيصال من أمين المخزن</p>
            </div>
        </div>

        <div class="flex gap-3">
            <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 flex items-center justify-center shrink-0">
                <i data-lucide="slash" class="w-4 h-4"></i>
            </div>
            <div>
                <h4 class="font-bold text-gray-900 dark:text-white mb-1">ملغي</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">تم إلغاء الإيصال من المسوق</p>
            </div>
        </div>
    </div>

    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-dark-border">
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
            <div class="flex gap-2 mb-2">
                <i data-lucide="lightbulb" class="w-5 h-5 text-blue-600 dark:text-blue-400 shrink-0"></i>
                <h4 class="font-bold text-blue-900 dark:text-blue-300">ملاحظة</h4>
            </div>
            <p class="text-sm text-blue-700 dark:text-blue-400">يتم احتساب العمولة تلقائياً عند توثيق الإيصال بناءً على نسبة عمولة المسوق</p>
        </div>
    </div>
</div>
