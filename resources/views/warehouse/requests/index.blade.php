@extends('layouts.app')

@section('title', 'طلبات المسوقين')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-[1600px] mx-auto space-y-8 px-2">
        
        {{-- Header --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 animate-fade-in-down">
            <div class="lg:col-span-12">
                <div class="flex items-center gap-3 mb-2">
                    <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                        إدارة المخزن
                    </span>
                </div>
                <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                    طلبات المسوقين
                </h1>
            </div>
        </div>

        {{-- Requests List --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- Main List --}}
            <div class="lg:col-span-8">
                {{-- Filters --}}
                <div class="bg-white dark:bg-dark-card rounded-2xl p-4 shadow-lg shadow-gray-200/50 dark:shadow-none border border-gray-200 dark:border-dark-border mb-6">
                    <form method="GET" action="{{ route('warehouse.requests.index') }}" class="flex flex-col md:flex-row gap-3">
                        <input type="hidden" name="status" value="{{ request('status') }}">
                        <input type="hidden" name="all" value="{{ request('all') }}">
                        
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">من تاريخ</label>
                            <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 dark:[color-scheme:dark]">
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">إلى تاريخ</label>
                            <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 dark:[color-scheme:dark]">
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">المسوق</label>
                            <input list="marketers-list" name="marketer_id" value="{{ request('marketer_id') ? $marketers->firstWhere('id', request('marketer_id'))?->full_name : '' }}" placeholder="ابحث عن مسوق..." class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <datalist id="marketers-list">
                                @foreach($marketers as $marketer)
                                    <option value="{{ $marketer->full_name }}" data-id="{{ $marketer->id }}">
                                @endforeach
                            </datalist>
                        </div>
                        <div class="flex gap-2 items-end">
                            <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2 justify-center">
                                <i data-lucide="filter" class="w-4 h-4"></i>
                                فلترة
                            </button>
                            @if(request('from_date') || request('to_date') || request('marketer_id'))
                                <a href="{{ route('warehouse.requests.index', ['status' => request('status'), 'all' => request('all')]) }}" class="px-6 py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2 justify-center">
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                    إلغاء
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                @include('shared.requests._status-tabs', ['route' => fn($params) => route('warehouse.requests.index', $params)])

                <div class="bg-white dark:bg-dark-card rounded-[2rem] p-4 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border animate-slide-up">
            @forelse($requests as $request)
                @php
                    $statusConfig = [
                        'pending' => ['bg' => 'bg-amber-50 dark:bg-amber-900/20', 'text' => 'text-amber-700 dark:text-amber-400', 'iconBg' => 'bg-amber-100 dark:bg-amber-900/40'],
                        'approved' => ['bg' => 'bg-blue-50 dark:bg-blue-900/20', 'text' => 'text-blue-700 dark:text-blue-400', 'iconBg' => 'bg-blue-100 dark:bg-blue-900/40'],
                        'documented' => ['bg' => 'bg-emerald-50 dark:bg-emerald-900/20', 'text' => 'text-emerald-700 dark:text-emerald-400', 'iconBg' => 'bg-emerald-100 dark:bg-emerald-900/40'],
                        'rejected' => ['bg' => 'bg-red-50 dark:bg-red-900/20', 'text' => 'text-red-700 dark:text-red-400', 'iconBg' => 'bg-red-100 dark:bg-red-900/40'],
                        'cancelled' => ['bg' => 'bg-gray-50 dark:bg-gray-800/50', 'text' => 'text-gray-600 dark:text-gray-400', 'iconBg' => 'bg-gray-100 dark:bg-gray-700'],
                    ][$request->status] ?? ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'iconBg' => 'bg-gray-100'];
                @endphp
                @include('shared.requests._request-card', [
                    'request' => $request,
                    'slot' => '<div class="mb-2"><span class="inline-flex items-center gap-1.5 ' . $statusConfig['bg'] . ' ' . $statusConfig['text'] . ' px-3 py-1.5 rounded-lg text-sm font-bold border ' . str_replace('bg-', 'border-', $statusConfig['iconBg']) . '"><i data-lucide="user" class="w-4 h-4"></i>' . $request->marketer->full_name . '</span></div>',
                    'actions' => '<a href="' . route('warehouse.requests.show', $request) . '" class="px-5 py-2.5 bg-white dark:bg-dark-card border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-400 dark:hover:border-gray-500 transition-all text-sm flex items-center gap-2 shadow-sm"><i data-lucide="eye" class="w-4 h-4"></i>التفاصيل</a>'
                ])
            @empty
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-gray-100 dark:bg-dark-bg rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="inbox" class="w-10 h-10 text-gray-400 dark:text-gray-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">لا توجد طلبات</h3>
                    <p class="text-gray-500 dark:text-dark-muted mb-6">لا توجد طلبات من المسوقين حالياً</p>
                </div>
            @endforelse

            {{-- Pagination --}}
            @if($requests->hasPages())
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-dark-border">
                    {{ $requests->links() }}
                </div>
            @endif
                </div>
            </div>

            {{-- Timeline Guide --}}
            <div class="lg:col-span-4">
                @include('shared.requests._timeline-guide')
            </div>
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
