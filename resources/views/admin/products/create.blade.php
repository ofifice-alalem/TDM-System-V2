@extends('layouts.app')

@section('title', 'إضافة منتج جديد')

@section('content')
<div class="min-h-screen py-8">
    <div class="max-w-3xl mx-auto px-4">
        
        <div class="mb-6">
            <a href="{{ route('admin.products.index') }}" class="text-primary-600 hover:text-primary-700 font-bold flex items-center gap-2 mb-4">
                <i data-lucide="arrow-right" class="w-5 h-5"></i>
                رجوع
            </a>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">إضافة منتج جديد</h1>
        </div>

        <div class="bg-white dark:bg-dark-card rounded-2xl shadow-xl p-6">
            <form action="{{ route('admin.products.store') }}" method="POST">
                @csrf

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">اسم المنتج *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                        @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">الباركود</label>
                        <input type="text" name="barcode" value="{{ old('barcode') }}" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                        @error('barcode')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">الوصف</label>
                        <textarea name="description" rows="3" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">{{ old('description') }}</textarea>
                        @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">السعر *</label>
                            <input type="number" step="0.01" name="current_price" value="{{ old('current_price') }}" required class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                            @error('current_price')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">سعر الجملة</label>
                            <input type="number" step="0.01" name="customer_price" value="{{ old('customer_price') }}" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                            @error('customer_price')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button type="submit" class="flex-1 px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all">
                            حفظ المنتج
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-bold transition-all">
                            إلغاء
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
