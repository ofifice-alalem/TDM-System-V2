<div class="bg-white dark:bg-dark-card rounded-[2rem] p-8 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border sticky top-24">
    <h3 class="font-bold text-xl text-gray-900 dark:text-white mb-6 flex items-center gap-3">
        <span class="bg-primary-50 dark:bg-primary-900/20 p-2.5 rounded-xl text-primary-600 dark:text-primary-400 shadow-sm border border-primary-100 dark:border-primary-600/30">
            <i data-lucide="info" class="w-5 h-5"></i>
        </span>
        مراحل الإرجاع
    </h3>
    
    <div class="space-y-6">
        <div class="flex gap-4">
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/40 rounded-full flex items-center justify-center text-amber-600 dark:text-amber-400 shadow-sm">
                    <i data-lucide="file-plus" class="w-5 h-5"></i>
                </div>
                <div class="w-0.5 h-full bg-amber-200 dark:bg-amber-800 mt-2"></div>
            </div>
            <div class="flex-1 pb-6">
                <h4 class="font-bold text-gray-900 dark:text-white mb-1">إنشاء الطلب</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">المسوق ينشئ طلب إرجاع من فاتورة موثقة</p>
            </div>
        </div>

        <div class="flex gap-4">
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/40 rounded-full flex items-center justify-center text-blue-600 dark:text-blue-400 shadow-sm">
                    <i data-lucide="package-minus" class="w-5 h-5"></i>
                </div>
                <div class="w-0.5 h-full bg-blue-200 dark:bg-blue-800 mt-2"></div>
            </div>
            <div class="flex-1 pb-6">
                <h4 class="font-bold text-gray-900 dark:text-white mb-1">خصم من المتجر</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">البضاعة تُخصم من مخزون المتجر الفعلي</p>
            </div>
        </div>

        <div class="flex gap-4">
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/40 rounded-full flex items-center justify-center text-purple-600 dark:text-purple-400 shadow-sm">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                </div>
                <div class="w-0.5 h-full bg-purple-200 dark:bg-purple-800 mt-2"></div>
            </div>
            <div class="flex-1 pb-6">
                <h4 class="font-bold text-gray-900 dark:text-white mb-1">مخزون مرحلي</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">البضاعة في مخزون مرحلي بانتظار الموافقة</p>
            </div>
        </div>

        <div class="flex gap-4">
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/40 rounded-full flex items-center justify-center text-emerald-600 dark:text-emerald-400 shadow-sm">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="flex-1">
                <h4 class="font-bold text-gray-900 dark:text-white mb-1">الموافقة والتوثيق</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">أمين المخزن يوافق ويوثق الإرجاع</p>
                <ul class="mt-2 space-y-1 text-xs text-gray-500 dark:text-gray-400">
                    <li class="flex items-center gap-1.5">
                        <i data-lucide="arrow-right" class="w-3 h-3"></i>
                        البضاعة تعود لمخزون المسوق
                    </li>
                    <li class="flex items-center gap-1.5">
                        <i data-lucide="arrow-right" class="w-3 h-3"></i>
                        تقليل دين المتجر
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-dark-border">
        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
            <h4 class="font-bold text-amber-900 dark:text-amber-400 text-sm mb-2 flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                ملاحظة مهمة
            </h4>
            <p class="text-xs text-amber-800 dark:text-amber-300">
                لا يمكن إرجاع كمية أكبر من الموجودة في الفاتورة الأصلية
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>
@endpush
