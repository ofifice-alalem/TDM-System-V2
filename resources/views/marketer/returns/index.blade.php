@extends('layouts.app')

@section('title', 'طلبات الإرجاع')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-[1600px] mx-auto space-y-8 px-2">
        
        {{-- Header & Quick Actions --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 animate-fade-in-down">
            <div class="lg:col-span-8">
                <div class="flex items-center gap-3 mb-2">
                    <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                        إدارة الإرجاع
                    </span>
                </div>
                <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                    طلبات إرجاع البضاعة
                </h1>
            </div>

            <div class="lg:col-span-4 lg:translate-y-[30px]">
                <a href="{{ route('marketer.returns.create') }}" class="px-8 py-4 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all shadow-lg shadow-primary-200/50 dark:shadow-none flex items-center justify-center gap-2 w-full">
                    <i data-lucide="package-x" class="w-5 h-5"></i>
                    طلب إرجاع جديد
                </a>
            </div>
        </div>

        {{-- Returns List --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- Main List --}}
            <div class="lg:col-span-8">
                @include('shared.returns._status-tabs', ['route' => fn($params) => route('marketer.returns.index', $params)])

                <div class="bg-white dark:bg-dark-card rounded-[2rem] p-4 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border animate-slide-up">
            @forelse($requests as $request)
                @include('shared.returns._return-card', [
                    'request' => $request,
                    'slot' => '',
                    'actions' => '<a href="' . route('marketer.returns.show', $request) . '" class="px-5 py-2.5 bg-white dark:bg-dark-card border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-400 dark:hover:border-gray-500 transition-all text-sm flex items-center gap-2 shadow-sm"><i data-lucide="eye" class="w-4 h-4"></i>التفاصيل</a>'
                ])
            @empty
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-gray-100 dark:bg-dark-bg rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="package-x" class="w-10 h-10 text-gray-400 dark:text-gray-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">لا توجد طلبات إرجاع</h3>
                    <p class="text-gray-500 dark:text-dark-muted mb-6">لم تقم بإنشاء أي طلبات إرجاع بعد</p>
                    <a href="{{ route('marketer.returns.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all">
                        <i data-lucide="package-x" class="w-5 h-5"></i>
                        إنشاء طلب إرجاع جديد
                    </a>
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
                @include('shared.returns._timeline-guide')
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
