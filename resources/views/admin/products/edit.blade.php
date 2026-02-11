@extends('layouts.app')

@section('title', 'تعديل المنتج')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-4xl mx-auto space-y-8 px-4">
        
        {{-- Header --}}
        <div class="flex items-center justify-between animate-fade-in-down">
            <div>
                <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                    تعديل المنتج
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-2">تعديل بيانات المنتج (باستثناء الكمية)</p>
            </div>
            <a href="{{ route('admin.main-stock.index') }}" class="px-6 py-3 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-50 dark:hover:bg-dark-bg transition-all flex items-center gap-2">
                <i data-lucide="arrow-right" class="w-5 h-5"></i>
                عودة
            </a>
        </div>

        {{-- Form --}}
        <div class="bg-white dark:bg-dark-card rounded-[2rem] p-8 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border">
            <form method="POST" action="{{ route('admin.products.update', $product) }}">
                @csrf
                @method('PATCH')

                <div class="space-y-6">
                    {{-- Name --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">اسم المنتج</label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Barcode --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">الباركود</label>
                        <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                        @error('barcode')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">الوصف</label>
                        <textarea name="description" rows="3" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Price --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">السعر (دينار)</label>
                        <input type="number" step="0.01" name="current_price" value="{{ old('current_price', $product->current_price) }}" required class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                        @error('current_price')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Active Status --}}
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="w-5 h-5 text-primary-600 bg-gray-50 dark:bg-dark-bg border-gray-300 dark:border-dark-border rounded focus:ring-2 focus:ring-primary-500">
                        <label for="is_active" class="text-sm font-bold text-gray-700 dark:text-gray-300">المنتج نشط</label>
                    </div>
                </div>

                <div class="mt-8 flex gap-3">
                    <button type="submit" class="flex-1 bg-primary-600 hover:bg-primary-700 text-white py-3.5 rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                        <i data-lucide="check" class="w-5 h-5"></i>
                        حفظ التعديلات
                    </button>
                    <a href="{{ route('admin.main-stock.index') }}" class="px-8 py-3.5 bg-gray-200 dark:bg-dark-bg hover:bg-gray-300 dark:hover:bg-dark-border text-gray-700 dark:text-gray-300 rounded-xl font-bold transition-all">
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
