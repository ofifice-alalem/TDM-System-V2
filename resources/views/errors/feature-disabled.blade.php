@extends('layouts.app')
@section('title', 'الميزة غير متاحة')

@section('content')
<div class="min-h-screen flex items-center justify-center py-16 px-4">
    <div class="text-center max-w-md">
        <div class="w-24 h-24 bg-red-100 dark:bg-red-900/20 rounded-full flex items-center justify-center mx-auto mb-6">
            <i data-lucide="lock" class="w-12 h-12 text-red-500 dark:text-red-400"></i>
        </div>
        <h1 class="text-2xl font-black text-gray-900 dark:text-white mb-3">الميزة غير متاحة</h1>
        <p class="text-gray-500 dark:text-gray-400 mb-8 leading-relaxed">
            لا يمكنك الوصول إلى هذه الميزة في الوقت الحالي.<br>
            راجع الشركة للاشتراك أو تفعيل هذه الميزة.
        </p>
        <a href="{{ url('/dashboard') }}"
            class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all">
            <i data-lucide="home" class="w-4 h-4"></i>
            العودة للرئيسية
        </a>
    </div>
</div>
@push('scripts')
<script>document.addEventListener('DOMContentLoaded', () => lucide.createIcons());</script>
@endpush
@endsection
