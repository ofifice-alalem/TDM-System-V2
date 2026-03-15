@extends('layouts.app')

@section('title', 'تسجيل دين سابق')

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
                    تسجيل دين سابق
                </h1>
            </div>
            <a href="{{ route('admin.old-debts.index') }}" class="px-6 py-3 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors flex items-center gap-2">
                <i data-lucide="arrow-right" class="w-5 h-5"></i>
                عودة
            </a>
        </div>

        {{-- Form --}}
        <div class="bg-white dark:bg-dark-card rounded-[2rem] p-8 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border animate-slide-up">
            <form action="{{ route('admin.old-debts.store') }}" method="POST">
                @csrf

                {{-- Store --}}
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                        المتجر <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" id="store-search" autocomplete="off" placeholder="ابحث عن المتجر..." class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200 dark:focus:ring-primary-900/50 transition-all">
                        <input type="hidden" name="store_id" id="store-id" required value="{{ old('store_id') }}">
                        <div id="store-dropdown" class="hidden absolute z-[9999] w-full mt-2 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl shadow-xl max-h-60 overflow-y-auto"></div>
                    </div>
                    @error('store_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Amount --}}
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                        المبلغ (دينار) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}" required
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200 dark:focus:ring-primary-900/50 transition-all dark:text-white">
                    @error('amount')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Notes --}}
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                        ملاحظات
                    </label>
                    <textarea name="notes" rows="3"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200 dark:focus:ring-primary-900/50 transition-all dark:text-white resize-none">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Info Box --}}
                <div class="bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-900/30 rounded-xl p-4 mb-6">
                    <div class="flex gap-3">
                        <i data-lucide="alert-circle" class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5"></i>
                        <div class="text-sm text-amber-800 dark:text-amber-300">
                            <p class="font-bold mb-1">تنبيه:</p>
                            <p>سيتم تسجيل هذا المبلغ كدين سابق على المتجر وإضافته لرصيده الحالي فور الحفظ.</p>
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 px-8 py-4 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all shadow-lg flex items-center justify-center gap-2">
                        <i data-lucide="check" class="w-5 h-5"></i>
                        تسجيل الدين
                    </button>
                    <a href="{{ route('admin.old-debts.index') }}" class="px-8 py-4 bg-gray-100 dark:bg-dark-bg text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-200 dark:hover:bg-dark-border transition-all">
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const stores = {!! json_encode($stores->map(fn($s) => ['id' => $s->id, 'name' => $s->name])) !!};

    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();

        const searchInput = document.getElementById('store-search');
        const storeIdInput = document.getElementById('store-id');
        const dropdown    = document.getElementById('store-dropdown');

        // Pre-fill if old value exists
        @if(old('store_id'))
            const preSelected = stores.find(s => s.id == {{ old('store_id') }});
            if (preSelected) searchInput.value = preSelected.name;
        @endif

        searchInput.addEventListener('input', function() {
            storeIdInput.value = '';
            const query = this.value.trim().toLowerCase();
            if (!query) { dropdown.classList.add('hidden'); return; }

            const filtered = stores.filter(s => s.name.toLowerCase().includes(query));

            if (!filtered.length) { dropdown.classList.add('hidden'); return; }

            dropdown.innerHTML = filtered.map(s => `
                <div class="store-option px-4 py-3 hover:bg-gray-100 dark:hover:bg-dark-bg cursor-pointer border-b border-gray-100 dark:border-dark-border last:border-0"
                    data-id="${s.id}" data-name="${s.name}">
                    <div class="font-bold text-gray-900 dark:text-white text-sm">${s.name}</div>
                </div>
            `).join('');
            dropdown.classList.remove('hidden');

            document.querySelectorAll('.store-option').forEach(option => {
                option.addEventListener('click', function() {
                    storeIdInput.value = this.dataset.id;
                    searchInput.value  = this.dataset.name;
                    dropdown.classList.add('hidden');
                });
            });
        });

        document.addEventListener('click', function(e) {
            if (!searchInput.closest('.relative').contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
    });
</script>
@endpush
@endsection
