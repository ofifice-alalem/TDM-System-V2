@extends('layouts.app')
@section('title', 'دمج المتاجر')

@section('content')
<div class="min-h-screen py-8">
    <div class="max-w-2xl mx-auto px-4 space-y-6">

        {{-- Header --}}
        <div>
            <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">إدارة النظام</span>
            <h1 class="text-3xl font-black text-gray-900 dark:text-white mt-2">دمج المتاجر</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">اختر الحساب الأصلي والمكرر، سيتم نقل جميع بيانات المكرر إلى الأصلي ثم حذفه</p>
        </div>

        @if(session('success'))
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl px-4 py-3 flex items-center gap-3">
                <i data-lucide="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400 shrink-0"></i>
                <p class="text-green-800 dark:text-green-300 font-bold text-sm">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl px-4 py-3 flex items-center gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 dark:text-red-400 shrink-0"></i>
                <p class="text-red-800 dark:text-red-300 font-bold text-sm">{{ session('error') }}</p>
            </div>
        @endif

        {{-- Form --}}
        <div class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border p-6 shadow-sm"
             x-data="storeMerge()">

            <form method="POST" action="{{ route('admin.store-merge.store') }}"
                  @submit.prevent="confirmMerge($event)">
                @csrf

                {{-- الحساب الأصلي --}}
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                        الحساب الأصلي
                        <span class="text-xs font-normal text-gray-400 mr-1">(سيبقى هذا الحساب)</span>
                    </label>
                    <input type="hidden" name="primary_id" x-model="primaryId">
                    <div class="relative">
                        <input type="text" x-model="primaryQuery"
                            @input="searchPrimary()" @focus="primaryOpen = true" @click.outside="primaryOpen = false"
                            placeholder="ابحث باسم المتجر أو هاتفه..."
                            class="w-full bg-gray-50 dark:bg-dark-bg border-2 border-green-300 dark:border-green-700 rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-400 transition-all">
                        <div class="absolute left-3 top-3.5">
                            <i data-lucide="shield-check" class="w-4 h-4 text-green-500"></i>
                        </div>
                        <ul x-show="primaryOpen && primaryResults.length"
                            class="absolute z-30 w-full bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl shadow-lg mt-1 max-h-52 overflow-y-auto">
                            <template x-for="c in primaryResults" :key="c.id">
                                <li @click="selectPrimary(c)"
                                    class="px-4 py-2.5 hover:bg-green-50 dark:hover:bg-green-900/20 cursor-pointer flex items-center justify-between"
                                    :class="c.id == duplicateId ? 'opacity-40 pointer-events-none' : ''">
                                    <span x-text="c.name" class="font-bold text-gray-900 dark:text-white text-sm"></span>
                                    <span x-text="c.phone" class="text-xs font-mono font-bold bg-gray-100 dark:bg-dark-bg text-gray-700 dark:text-gray-300 px-2 py-0.5 rounded-lg"></span>
                                </li>
                            </template>
                        </ul>
                    </div>
                    <template x-if="primaryId">
                        <p class="mt-1.5 text-xs text-green-600 dark:text-green-400 font-bold flex items-center gap-1">
                            <i data-lucide="check" class="w-3 h-3"></i>
                            <span>تم الاختيار — ID: <span x-text="primaryId"></span></span>
                        </p>
                    </template>
                </div>

                {{-- سهم --}}
                <div class="flex items-center justify-center my-4">
                    <div class="flex flex-col items-center gap-1 text-gray-400">
                        <i data-lucide="arrow-down" class="w-6 h-6"></i>
                        <span class="text-xs font-bold">يُدمج في</span>
                    </div>
                </div>

                {{-- الحساب المكرر --}}
                <div class="mb-8">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                        الحساب المكرر
                        <span class="text-xs font-normal text-gray-400 mr-1">(سيُحذف هذا الحساب بعد نقل بياناته)</span>
                    </label>
                    <input type="hidden" name="duplicate_id" x-model="duplicateId">
                    <div class="relative">
                        <input type="text" x-model="duplicateQuery"
                            @input="searchDuplicate()" @focus="duplicateOpen = true" @click.outside="duplicateOpen = false"
                            placeholder="ابحث باسم المتجر أو هاتفه..."
                            class="w-full bg-gray-50 dark:bg-dark-bg border-2 border-red-300 dark:border-red-700 rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-400 transition-all">
                        <div class="absolute left-3 top-3.5">
                            <i data-lucide="trash-2" class="w-4 h-4 text-red-400"></i>
                        </div>
                        <ul x-show="duplicateOpen && duplicateResults.length"
                            class="absolute z-30 w-full bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl shadow-lg mt-1 max-h-52 overflow-y-auto">
                            <template x-for="c in duplicateResults" :key="c.id">
                                <li @click="selectDuplicate(c)"
                                    class="px-4 py-2.5 hover:bg-red-50 dark:hover:bg-red-900/20 cursor-pointer flex items-center justify-between"
                                    :class="c.id == primaryId ? 'opacity-40 pointer-events-none' : ''">
                                    <span x-text="c.name" class="font-bold text-gray-900 dark:text-white text-sm"></span>
                                    <span x-text="c.phone" class="text-xs font-mono font-bold bg-gray-100 dark:bg-dark-bg text-gray-700 dark:text-gray-300 px-2 py-0.5 rounded-lg"></span>
                                </li>
                            </template>
                        </ul>
                    </div>
                    <template x-if="duplicateId">
                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 font-bold flex items-center gap-1">
                            <i data-lucide="x" class="w-3 h-3"></i>
                            <span>تم الاختيار — ID: <span x-text="duplicateId"></span></span>
                        </p>
                    </template>
                </div>

                {{-- تأكيد --}}
                <div x-show="primaryId && duplicateId"
                     class="bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800 rounded-xl p-4 mb-6">
                    <div class="flex gap-3">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5"></i>
                        <div class="text-sm text-amber-800 dark:text-amber-300">
                            <p class="font-bold mb-1">تأكيد الدمج:</p>
                            <p>سيتم نقل جميع فواتير ومدفوعات ومرتجعات وسجلات الدين من
                                <strong x-text="'«' + duplicateQuery + '»'"></strong>
                                إلى
                                <strong x-text="'«' + primaryQuery + '»'"></strong>
                                ثم حذف الحساب المكرر نهائياً.
                            </p>
                        </div>
                    </div>
                </div>

                <button type="submit"
                    :disabled="!primaryId || !duplicateId"
                    class="w-full py-3 bg-red-600 hover:bg-red-700 disabled:bg-gray-300 dark:disabled:bg-gray-700 disabled:cursor-not-allowed text-white rounded-xl font-black transition-all flex items-center justify-center gap-2">
                    <i data-lucide="git-merge" class="w-5 h-5"></i>
                    تنفيذ الدمج
                </button>
            </form>
        </div>

    </div>
</div>

{{-- Confirm Modal --}}
<div id="confirm-modal" style="display:none" class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50"></div>
    <div class="relative bg-white dark:bg-dark-card rounded-2xl p-8 shadow-2xl w-full max-w-md">
        <h3 class="text-xl font-black text-gray-900 dark:text-white mb-3 flex items-center gap-3">
            <i data-lucide="alert-triangle" class="w-6 h-6 text-red-500"></i>
            تأكيد الدمج النهائي
        </h3>
        <p class="text-gray-600 dark:text-gray-400 mb-6 text-sm leading-relaxed">
            هذه العملية <strong class="text-red-600">لا يمكن التراجع عنها</strong>. هل أنت متأكد من دمج الحسابين؟
        </p>
        <div class="flex gap-3">
            <button id="confirm-yes" class="flex-1 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-black transition-all">
                نعم، نفّذ الدمج
            </button>
            <button id="confirm-no" class="px-6 py-3 bg-gray-100 dark:bg-dark-bg text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-200 transition-all">
                إلغاء
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => lucide.createIcons());

    const allStores = @json($stores);

    function storeMerge() {
        return {
            primaryId: '', primaryQuery: '', primaryResults: [], primaryOpen: false,
            duplicateId: '', duplicateQuery: '', duplicateResults: [], duplicateOpen: false,

            searchPrimary() {
                this.primaryId = '';
                this.primaryResults = this._filter(this.primaryQuery);
            },
            searchDuplicate() {
                this.duplicateId = '';
                this.duplicateResults = this._filter(this.duplicateQuery);
            },
            selectPrimary(c) {
                this.primaryId = c.id; this.primaryQuery = c.name; this.primaryOpen = false;
            },
            selectDuplicate(c) {
                this.duplicateId = c.id; this.duplicateQuery = c.name; this.duplicateOpen = false;
            },
            _filter(q) {
                if (!q) return allStores.slice(0, 10);
                return allStores.filter(c =>
                    c.name.includes(q) || (c.phone && c.phone.includes(q))
                ).slice(0, 10);
            },
            confirmMerge(e) {
                if (!this.primaryId || !this.duplicateId) return;
                const modal = document.getElementById('confirm-modal');
                modal.style.display = 'flex';
                document.getElementById('confirm-yes').onclick = () => { modal.style.display = 'none'; e.target.submit(); };
                document.getElementById('confirm-no').onclick  = () => { modal.style.display = 'none'; };
            }
        };
    }
</script>
@endpush
@endsection
