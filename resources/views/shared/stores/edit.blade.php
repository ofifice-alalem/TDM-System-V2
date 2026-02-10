@extends('layouts.app')

@section('title', 'تعديل بيانات المتجر - ' . $store->name)

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-4xl mx-auto space-y-8 px-2">
        
        {{-- Back Button & Header --}}
        <div class="animate-fade-in-down">
            <a href="{{ request()->routeIs('admin.*') ? route('admin.stores.index') : route('warehouse.stores.index') }}" class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 mb-4 transition-colors">
                <i data-lucide="arrow-right" class="w-5 h-5"></i>
                <span class="font-bold">العودة للمتاجر</span>
            </a>
            <div class="flex items-center gap-3 mb-2">
                <span class="bg-amber-100 dark:bg-amber-600/20 text-amber-600 dark:text-amber-400 px-3 py-1 rounded-lg text-xs font-bold border border-amber-100 dark:border-amber-600/30">
                    تعديل البيانات
                </span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                تعديل بيانات المتجر
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2">{{ $store->name }}</p>
        </div>

        {{-- Form Card --}}
        <div class="bg-white dark:bg-dark-card rounded-3xl p-8 shadow-lg shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border animate-slide-up">
            
            <form action="{{ request()->routeIs('admin.*') ? route('admin.stores.update', $store) : route('warehouse.stores.update', $store) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="space-y-6">
                    
                    {{-- Store Name --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            <i data-lucide="store" class="w-4 h-4 inline-block ml-1"></i>
                            اسم المتجر <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $store->name) }}" required
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-dark-bg border-2 border-gray-200 dark:border-dark-border rounded-xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-primary-500 dark:focus:border-primary-500 focus:ring-4 focus:ring-primary-100 dark:focus:ring-primary-500/20 transition-all"
                            placeholder="أدخل اسم المتجر">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Owner Name --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            <i data-lucide="user" class="w-4 h-4 inline-block ml-1"></i>
                            اسم المالك <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="owner_name" value="{{ old('owner_name', $store->owner_name) }}" required
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-dark-bg border-2 border-gray-200 dark:border-dark-border rounded-xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-primary-500 dark:focus:border-primary-500 focus:ring-4 focus:ring-primary-100 dark:focus:ring-primary-500/20 transition-all"
                            placeholder="أدخل اسم المالك">
                        @error('owner_name')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            <i data-lucide="phone" class="w-4 h-4 inline-block ml-1"></i>
                            رقم الهاتف
                        </label>
                        <input type="text" name="phone" value="{{ old('phone', $store->phone) }}"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-dark-bg border-2 border-gray-200 dark:border-dark-border rounded-xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-primary-500 dark:focus:border-primary-500 focus:ring-4 focus:ring-primary-100 dark:focus:ring-primary-500/20 transition-all"
                            placeholder="أدخل رقم الهاتف">
                        @error('phone')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Location --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            <i data-lucide="map-pin" class="w-4 h-4 inline-block ml-1"></i>
                            الموقع
                        </label>
                        <input type="text" name="location" value="{{ old('location', $store->location) }}"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-dark-bg border-2 border-gray-200 dark:border-dark-border rounded-xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-primary-500 dark:focus:border-primary-500 focus:ring-4 focus:ring-primary-100 dark:focus:ring-primary-500/20 transition-all"
                            placeholder="أدخل الموقع">
                        @error('location')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Address --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            <i data-lucide="map" class="w-4 h-4 inline-block ml-1"></i>
                            العنوان التفصيلي
                        </label>
                        <textarea name="address" rows="3"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-dark-bg border-2 border-gray-200 dark:border-dark-border rounded-xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-primary-500 dark:focus:border-primary-500 focus:ring-4 focus:ring-primary-100 dark:focus:ring-primary-500/20 transition-all"
                            placeholder="أدخل العنوان التفصيلي">{{ old('address', $store->address) }}</textarea>
                        @error('address')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Store Status --}}
                    <div class="bg-gray-50 dark:bg-dark-bg rounded-2xl p-6 border-2 border-gray-200 dark:border-dark-border">
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">
                                    <i data-lucide="power" class="w-4 h-4 inline-block ml-1"></i>
                                    حالة المتجر
                                </label>
                                <p class="text-xs text-gray-500 dark:text-gray-400">تفعيل أو إلغاء تفعيل المتجر</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $store->is_active) ? 'checked' : '' }}>
                                <div class="w-14 h-7 bg-gray-300 dark:bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-100 dark:peer-focus:ring-primary-500/20 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-emerald-500"></div>
                            </label>
                        </div>
                    </div>

                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-4 mt-8 pt-6 border-t border-gray-200 dark:border-dark-border">
                    <button type="submit" class="flex-1 px-6 py-4 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-bold transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                        <i data-lucide="check" class="w-5 h-5"></i>
                        حفظ التعديلات
                    </button>
                    <a href="{{ request()->routeIs('admin.*') ? route('admin.stores.index') : route('warehouse.stores.index') }}" class="px-6 py-4 bg-gray-100 dark:bg-dark-bg hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                        <i data-lucide="x" class="w-5 h-5"></i>
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
