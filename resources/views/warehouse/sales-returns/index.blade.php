@extends('layouts.app')

@section('title', 'إرجاعات البضاعة من المتاجر')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-[1600px] mx-auto space-y-8 px-2">
        
        <div class="animate-fade-in-down">
            <div class="flex items-center gap-3 mb-2">
                <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                    إدارة الإرجاعات
                </span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                إرجاعات البضاعة من المتاجر
            </h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <div class="lg:col-span-8">
                {{-- Filters --}}
                <details class="bg-white dark:bg-dark-card rounded-2xl shadow-lg shadow-gray-200/50 dark:shadow-none border border-gray-200 dark:border-dark-border mb-6">
                    <summary class="px-4 py-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-dark-bg/50 transition-colors rounded-2xl flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i data-lucide="filter" class="w-5 h-5 text-primary-600 dark:text-primary-400"></i>
                            <span class="font-bold text-gray-900 dark:text-white">فلترة متقدمة</span>
                            @if(request('return_number') || request('from_date') || request('to_date') || request('marketer_id') || request('store_id'))
                                <span class="px-2 py-0.5 bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 text-xs font-bold rounded-full">نشط</span>
                            @endif
                        </div>
                        <i data-lucide="chevron-down" class="w-5 h-5 text-gray-400 transition-transform"></i>
                    </summary>
                    <form method="GET" action="{{ route('warehouse.sales-returns.index') }}" class="p-4 pt-2 border-t border-gray-200 dark:border-dark-border space-y-4">
                        <input type="hidden" name="status" value="{{ request('status') }}">
                        <input type="hidden" name="all" value="{{ request('all') }}">

                        {{-- السطر الأول --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">رقم الإرجاع</label>
                                <input type="text" name="return_number" value="{{ request('return_number') }}" placeholder="ابحث برقم الإرجاع..." class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">المسوق</label>
                                <input type="hidden" id="marketer-id-input" name="marketer_id" value="{{ request('marketer_id') ?? '' }}">
                                <div class="relative" id="marketer-wrapper">
                                    <input type="text" id="marketer-search" placeholder="ابحث عن مسوق..." autocomplete="off"
                                        value="{{ request('marketer_id') ? $marketers->firstWhere('id', request('marketer_id'))?->full_name : '' }}"
                                        class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                                    <div id="marketer-dropdown" class="hidden absolute z-50 w-full mt-1 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl shadow-lg max-h-48 overflow-y-auto"></div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">المتجر</label>
                                <input type="hidden" id="store-id-input" name="store_id" value="{{ request('store_id') ?? '' }}">
                                <div class="relative" id="store-wrapper">
                                    <input type="text" id="store-search" placeholder="ابحث عن متجر..." autocomplete="off"
                                        value="{{ request('store_id') ? $stores->firstWhere('id', request('store_id'))?->name : '' }}"
                                        class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                                    <div id="store-dropdown" class="hidden absolute z-50 w-full mt-1 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl shadow-lg max-h-48 overflow-y-auto"></div>
                                </div>
                            </div>
                        </div>

                        {{-- السطر الثاني --}}
                        <div class="flex flex-col md:flex-row gap-4 items-end">
                            <div class="w-full md:flex-1">
                                <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">من تاريخ</label>
                                <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 dark:[color-scheme:dark]">
                            </div>
                            <div class="w-full md:flex-1">
                                <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">إلى تاريخ</label>
                                <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 dark:[color-scheme:dark]">
                            </div>
                            <div class="flex gap-2 w-full md:w-auto">
                                <button type="submit" class="flex-1 md:flex-none px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2 justify-center">
                                    <i data-lucide="search" class="w-4 h-4"></i>
                                    بحث
                                </button>
                                @if(request('return_number') || request('from_date') || request('to_date') || request('marketer_id') || request('store_id'))
                                    <a href="{{ route('warehouse.sales-returns.index', ['status' => request('status'), 'all' => request('all')]) }}" class="px-6 py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2 justify-center">
                                        <i data-lucide="x" class="w-4 h-4"></i>
                                        إعادة تعيين
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </details>

                @include('shared.sales-returns._status-tabs', ['route' => fn($params) => route('warehouse.sales-returns.index', $params)])

                <div class="bg-white dark:bg-dark-card rounded-[2rem] p-4 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border animate-slide-up">
            @forelse($returns as $return)
                @include('shared.sales-returns._return-card', [
                    'return' => $return,
                    'slot' => '<div class="flex items-center gap-2 mb-[7px]"><i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i><span class="text-sm font-medium text-gray-600 dark:text-gray-400">' . $return->created_at->format('Y-m-d') . '</span></div>
                              <div class="flex items-center gap-2 mb-[7px]"><i data-lucide="user" class="w-4 h-4 text-gray-400"></i><span class="text-sm font-medium text-gray-600 dark:text-gray-400">' . $return->marketer->full_name . '</span></div>',
                    'storeSlot' => '<div class="flex items-center gap-2"><i data-lucide="store" class="w-4 h-4 text-gray-400"></i><span class="text-sm font-bold text-gray-700 dark:text-gray-300">' . $return->store->name . '</span></div>',
                    'actions' => '<a href="' . route('warehouse.sales-returns.show', $return) . '" class="px-5 py-2.5 bg-white dark:bg-dark-card border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-400 dark:hover:border-gray-500 transition-all text-sm flex items-center gap-2 shadow-sm"><i data-lucide="eye" class="w-4 h-4"></i>التفاصيل</a>'
                ])
            @empty
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-gray-100 dark:bg-dark-bg rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="inbox" class="w-10 h-10 text-gray-400 dark:text-gray-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">لا توجد طلبات إرجاع</h3>
                    <p class="text-gray-500 dark:text-dark-muted">لا توجد طلبات إرجاع حالياً</p>
                </div>
            @endforelse

            @if($returns->hasPages())
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-dark-border">
                    {{ $returns->links() }}
                </div>
            @endif
                </div>
            </div>

            <div class="lg:col-span-4">
                @include('shared.sales-returns._timeline-guide')
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();

        const marketers = @json($marketers->map(fn($m) => ['id' => $m->id, 'name' => $m->full_name]));
        const stores    = @json($stores->map(fn($s) => ['id' => $s->id, 'name' => $s->name]));

        function initDropdown(inputId, hiddenId, wrapperId, dropdownId, data) {
            const input    = document.getElementById(inputId);
            const hidden   = document.getElementById(hiddenId);
            const dropdown = document.getElementById(dropdownId);
            const wrapper  = document.getElementById(wrapperId);

            input.addEventListener('input', function () {
                const q = this.value.trim();
                hidden.value = '';

                if (!q) { dropdown.classList.add('hidden'); return; }

                const results = data.filter(m => m.name.includes(q)).slice(0, 10);

                if (!results.length) {
                    dropdown.innerHTML = '<div class="px-4 py-2.5 text-sm text-gray-500 dark:text-gray-400">لا توجد نتائج</div>';
                    dropdown.classList.remove('hidden');
                    return;
                }

                dropdown.innerHTML = results.map(m =>
                    `<div class="px-4 py-2.5 text-sm text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-dark-bg cursor-pointer border-b border-gray-100 dark:border-dark-border last:border-0" data-id="${m.id}" data-name="${m.name}">${m.name}</div>`
                ).join('');

                dropdown.querySelectorAll('[data-id]').forEach(el => {
                    el.addEventListener('click', function () {
                        hidden.value = this.dataset.id;
                        input.value  = this.dataset.name;
                        dropdown.classList.add('hidden');
                    });
                });

                dropdown.classList.remove('hidden');
            });

            document.addEventListener('click', function (e) {
                if (!wrapper.contains(e.target)) dropdown.classList.add('hidden');
            });
        }

        initDropdown('marketer-search', 'marketer-id-input', 'marketer-wrapper', 'marketer-dropdown', marketers);
        initDropdown('store-search',    'store-id-input',    'store-wrapper',    'store-dropdown',    stores);
    });
</script>
@endpush
@endsection
