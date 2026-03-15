@extends('layouts.app')

@section('title', 'الديون السابقة للعملاء')

@section('content')

<div class="min-h-screen py-8" x-data="{
    editOpen: false, editId: null, editAmount: '', editNotes: '',
    deleteOpen: false, deleteId: null, deleteNumber: '',
    openEdit(id, amount, notes) { this.editId = id; this.editAmount = amount; this.editNotes = notes; this.editOpen = true; },
    openDelete(id, number) { this.deleteId = id; this.deleteNumber = number; this.deleteOpen = true; }
}">
    <div class="max-w-[1600px] mx-auto space-y-8 px-2">

        {{-- Header --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 animate-fade-in-down">
            <div class="lg:col-span-8">
                <div class="flex items-center gap-3 mb-2">
                    <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                        إدارة النظام
                    </span>
                </div>
                <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                    الديون السابقة للعملاء
                </h1>
            </div>
            <div class="lg:col-span-4 lg:translate-y-[30px]">
                <a href="{{ route('admin.old-customer-debts.create') }}" class="px-8 py-4 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all shadow-lg shadow-primary-200/50 dark:shadow-none flex items-center justify-center gap-2 w-full">
                    <i data-lucide="plus-circle" class="w-5 h-5"></i>
                    تسجيل دين سابق
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <div class="lg:col-span-8">

                {{-- Filters --}}
                <details class="bg-white dark:bg-dark-card rounded-2xl shadow-lg shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border mb-6 animate-slide-up">
                    <summary class="px-4 py-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-dark-bg/50 transition-colors rounded-2xl flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i data-lucide="filter" class="w-5 h-5 text-primary-600 dark:text-primary-400"></i>
                            <span class="font-bold text-gray-900 dark:text-white">فلترة متقدمة</span>
                            @if(request('customer_id') || request('from_date') || request('to_date'))
                                <span class="px-2 py-0.5 bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 text-xs font-bold rounded-full">نشط</span>
                            @endif
                        </div>
                        <i data-lucide="chevron-down" class="w-5 h-5 text-gray-400 transition-transform"></i>
                    </summary>
                    <form method="GET" action="{{ route('admin.old-customer-debts.index') }}" class="p-4 pt-2">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">العميل</label>
                                <select name="customer_id" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all text-sm">
                                    <option value="">الكل</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">من تاريخ</label>
                                <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all text-sm [color-scheme:light] dark:[color-scheme:dark]">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">إلى تاريخ</label>
                                <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all text-sm [color-scheme:light] dark:[color-scheme:dark]">
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all flex items-center gap-2">
                                <i data-lucide="search" class="w-4 h-4"></i>
                                بحث
                            </button>
                            @if(request('customer_id') || request('from_date') || request('to_date'))
                                <a href="{{ route('admin.old-customer-debts.index') }}" class="px-6 py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-bold transition-all flex items-center gap-2">
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                    إعادة تعيين
                                </a>
                            @endif
                        </div>
                    </form>
                </details>

                {{-- List --}}
                <div class="bg-white dark:bg-dark-card rounded-[2rem] p-4 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border animate-slide-up">
                    @forelse($debts as $debt)
                        <div class="bg-gradient-to-br from-gray-50 to-white dark:from-dark-bg dark:to-dark-card rounded-2xl border-2 border-orange-200 dark:border-orange-800/40 mb-4 p-5 hover:shadow-lg transition-all">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <span class="bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400 px-3 py-1.5 rounded-lg text-xs font-black border border-amber-300 dark:border-amber-500">
                                        {{ $debt->invoice_number }}
                                    </span>
                                    <span class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5">
                                        <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                                        موثقة
                                    </span>
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $debt->created_at->format('Y-m-d') }}</span>
                            </div>

                            <div class="mt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="user" class="w-4 h-4 text-gray-400"></i>
                                    <span class="font-bold text-gray-900 dark:text-white">{{ $debt->customer->name }}</span>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="flex items-baseline gap-2">
                                        <span class="text-3xl font-black text-orange-600 dark:text-orange-400">{{ number_format($debt->total_amount, 2) }}</span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400 font-bold">دينار</span>
                                    </div>
                                    <div class="flex gap-2">
                                        <button type="button"
                                            @click="openEdit({{ $debt->id }}, '{{ $debt->total_amount }}', '{{ addslashes($debt->notes ?? '') }}')"
                                            class="p-2 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-all">
                                            <i data-lucide="pencil" class="w-4 h-4"></i>
                                        </button>
                                        <button type="button"
                                            @click="openDelete({{ $debt->id }}, '{{ $debt->invoice_number }}')"
                                            class="p-2 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-xl hover:bg-red-200 dark:hover:bg-red-900/50 transition-all">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            @if($debt->notes)
                                <div class="mt-3 pt-3 border-t border-gray-200 dark:border-dark-border">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $debt->notes }}</p>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="w-20 h-20 bg-gray-100 dark:bg-dark-bg rounded-full flex items-center justify-center mx-auto mb-4">
                                <i data-lucide="file-x" class="w-10 h-10 text-gray-400 dark:text-gray-600"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">لا توجد ديون سابقة</h3>
                            <p class="text-gray-500 dark:text-dark-muted">لم يتم تسجيل أي ديون سابقة للعملاء بعد</p>
                        </div>
                    @endforelse

                    @if($debts->hasPages())
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-dark-border">
                            {{ $debts->links() }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Info Guide --}}
            <div class="lg:col-span-4">
                <div class="bg-white dark:bg-dark-card rounded-[1.5rem] border border-gray-200 dark:border-dark-border p-8 shadow-lg shadow-gray-200/50 dark:shadow-sm lg:sticky lg:top-[150px]">
                    <h3 class="font-bold text-xl text-gray-900 dark:text-white mb-8 flex items-center gap-3">
                        <i data-lucide="info" class="w-6 h-6 text-primary-500"></i>
                        حول الديون السابقة
                    </h3>
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="w-11 h-11 rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 flex items-center justify-center shrink-0">
                                <i data-lucide="history" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white text-base mb-1">ديون النظام القديم</h4>
                                <p class="text-sm text-gray-500 dark:text-dark-muted leading-relaxed">تسجيل الأرصدة المتراكمة على العملاء قبل تشغيل النظام الحالي</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-11 h-11 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                                <i data-lucide="tag" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white text-base mb-1">تسلسل الأرقام</h4>
                                <p class="text-sm text-gray-500 dark:text-dark-muted leading-relaxed">تُرقَّم الفواتير بصيغة <span class="font-mono font-bold">CI-OLD-YYYYMMDD-XXXXX</span></p>
                            </div>
                        </div>
                        <div class="bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-900/30 rounded-xl p-4">
                            <div class="flex gap-3">
                                <i data-lucide="alert-circle" class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5"></i>
                                <div class="text-sm text-amber-800 dark:text-amber-300">
                                    <p class="font-bold mb-1">ملاحظة:</p>
                                    <p>هذه الفواتير موثقة فور إنشائها وتُضاف مباشرة لدين العميل.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div x-show="editOpen" style="display:none" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="editOpen = false"></div>
        <div class="relative bg-white dark:bg-dark-card rounded-[2rem] p-8 shadow-2xl w-full max-w-md">
            <h3 class="text-xl font-black text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                <i data-lucide="pencil" class="w-5 h-5 text-blue-500"></i>
                تعديل الدين السابق
            </h3>
            <form :action="'{{ url('admin/old-customer-debts') }}/' + editId" method="POST">
                @csrf
                @method('PATCH')
                <div class="mb-5">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">المبلغ (دينار)</label>
                    <input type="number" step="0.01" min="0.01" name="amount" x-model="editAmount" required
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200 dark:focus:ring-primary-900/50 transition-all dark:text-white">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">ملاحظات</label>
                    <textarea name="notes" rows="3" x-model="editNotes"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200 dark:focus:ring-primary-900/50 transition-all dark:text-white resize-none"></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        حفظ التعديل
                    </button>
                    <button type="button" @click="editOpen = false" class="px-6 py-3 bg-gray-100 dark:bg-dark-bg text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-200 dark:hover:bg-dark-border transition-all">
                        إلغاء
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div x-show="deleteOpen" style="display:none" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="deleteOpen = false"></div>
        <div class="relative bg-white dark:bg-dark-card rounded-[2rem] p-8 shadow-2xl w-full max-w-md">
            <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2 flex items-center gap-3">
                <i data-lucide="trash-2" class="w-5 h-5 text-red-500"></i>
                حذف الدين السابق
            </h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">هل أنت متأكد من حذف الفاتورة <span class="font-bold text-gray-900 dark:text-white" x-text="deleteNumber"></span>؟ سيتم حذفها من دين العميل أيضاً.</p>
            <form :action="'{{ url('admin/old-customer-debts') }}/' + deleteId" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                        تأكيد الحذف
                    </button>
                    <button type="button" @click="deleteOpen = false" class="px-6 py-3 bg-gray-100 dark:bg-dark-bg text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-200 dark:hover:bg-dark-border transition-all">
                        إلغاء
                    </button>
                </div>
            </form>
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
@endsection
