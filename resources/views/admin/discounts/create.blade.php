@extends('layouts.app')

@section('title', 'إضافة قاعدة خصم جديدة')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-4xl mx-auto space-y-8 px-4">
        
        {{-- Header --}}
        <div class="flex justify-between items-center animate-fade-in-down">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                        إدارة النظام
                    </span>
                </div>
                <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                    قاعدة خصم جديدة
                </h1>
            </div>

            <a href="{{ route('admin.discounts.index') }}" class="px-6 py-3 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors flex items-center gap-2">
                <i data-lucide="arrow-right" class="w-5 h-5"></i>
                عودة
            </a>
        </div>

        {{-- Form --}}
        <div class="bg-white dark:bg-dark-card rounded-[2rem] p-8 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border animate-slide-up">
            <form action="{{ route('admin.discounts.store') }}" method="POST" x-data="{ discountType: 'percentage' }">
                @csrf

                {{-- Min Amount --}}
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                        الحد الأدنى لقيمة الفاتورة (دينار)
                    </label>
                    <input type="number" step="0.01" name="min_amount" value="{{ old('min_amount') }}" required
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200 dark:focus:ring-primary-900/50 transition-all dark:text-white">
                    @error('min_amount')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Discount Type --}}
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                        نوع الخصم
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="relative cursor-pointer">
                            <input type="radio" name="discount_type" value="percentage" x-model="discountType" checked
                                class="peer sr-only">
                            <div class="px-6 py-4 bg-gray-50 dark:bg-dark-bg border-2 border-gray-200 dark:border-dark-border rounded-xl peer-checked:border-primary-500 peer-checked:bg-primary-50 dark:peer-checked:bg-primary-500/10 dark:peer-checked:border-primary-500 transition-all text-center">
                                <i data-lucide="percent" class="w-6 h-6 mx-auto mb-2 text-gray-400 dark:text-gray-500 peer-checked:text-primary-600 dark:peer-checked:text-primary-400"></i>
                                <span class="font-bold text-gray-700 dark:text-gray-300 peer-checked:text-primary-600 dark:peer-checked:text-primary-400">نسبة مئوية</span>
                            </div>
                        </label>
                        
                        <label class="relative cursor-pointer">
                            <input type="radio" name="discount_type" value="fixed" x-model="discountType"
                                class="peer sr-only">
                            <div class="px-6 py-4 bg-gray-50 dark:bg-dark-bg border-2 border-gray-200 dark:border-dark-border rounded-xl peer-checked:border-primary-500 peer-checked:bg-primary-50 dark:peer-checked:bg-primary-500/10 dark:peer-checked:border-primary-500 transition-all text-center">
                                <i data-lucide="coins" class="w-6 h-6 mx-auto mb-2 text-gray-400 dark:text-gray-500 peer-checked:text-primary-600 dark:peer-checked:text-primary-400"></i>
                                <span class="font-bold text-gray-700 dark:text-gray-300 peer-checked:text-primary-600 dark:peer-checked:text-primary-400">مبلغ ثابت</span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Discount Value --}}
                <div class="mb-6">
                    <div x-show="discountType === 'percentage'">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            نسبة الخصم (%)
                        </label>
                        <input type="number" step="0.01" name="discount_percentage" value="{{ old('discount_percentage') }}"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200 dark:focus:ring-primary-900/50 transition-all dark:text-white">
                        @error('discount_percentage')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div x-show="discountType === 'fixed'" style="display: none;">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            مبلغ الخصم (دينار)
                        </label>
                        <input type="number" step="0.01" name="discount_amount" value="{{ old('discount_amount') }}"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200 dark:focus:ring-primary-900/50 transition-all dark:text-white">
                        @error('discount_amount')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Date Range --}}
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            تاريخ البداية
                        </label>
                        <input type="date" name="start_date" value="{{ old('start_date') }}" required
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200 dark:focus:ring-primary-900/50 transition-all dark:text-white dark:[color-scheme:dark]">
                        @error('start_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            تاريخ النهاية
                        </label>
                        <input type="date" name="end_date" value="{{ old('end_date') }}" required
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200 dark:focus:ring-primary-900/50 transition-all dark:text-white dark:[color-scheme:dark]">
                        @error('end_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Info Box --}}
                <div class="bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-900/30 rounded-xl p-4 mb-6">
                    <div class="flex gap-3">
                        <i data-lucide="alert-circle" class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5"></i>
                        <div class="text-sm text-amber-800 dark:text-amber-300">
                            <p class="font-bold mb-1">ملاحظة مهمة:</p>
                            <p>لا يمكن تعديل قيم الخصم بعد الإنشاء. للتغيير، قم بتعطيل القاعدة القديمة وإنشاء قاعدة جديدة.</p>
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 px-8 py-4 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all shadow-lg flex items-center justify-center gap-2">
                        <i data-lucide="check" class="w-5 h-5"></i>
                        إنشاء القاعدة
                    </button>
                    <a href="{{ route('admin.discounts.index') }}" class="px-8 py-4 bg-gray-100 dark:bg-dark-bg text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-200 dark:hover:bg-dark-border transition-all">
                        إلغاء
                    </a>
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
