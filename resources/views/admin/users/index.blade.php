@extends('layouts.app')

@section('title', 'المستخدمين')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-[1600px] mx-auto space-y-8 px-2">
        
        {{-- Header --}}
        <div class="animate-fade-in-down">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-3">
                    <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                        إدارة المستخدمين
                    </span>
                </div>
                <a href="{{ route('admin.users.create') }}" class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                    <i data-lucide="plus" class="w-5 h-5"></i>
                    إضافة مستخدم جديد
                </a>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                المستخدمين
            </h1>
        </div>

        {{-- Search & Filter Bar --}}
        <div class="animate-fade-in">
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col md:flex-row gap-4">
                <div class="relative flex-1">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ $search ?? '' }}"
                        placeholder="ابحث عن مستخدم..." 
                        class="w-full px-6 py-4 pr-14 bg-white dark:bg-dark-card border-2 border-gray-200 dark:border-dark-border rounded-2xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-primary-500 dark:focus:border-primary-500 focus:ring-4 focus:ring-primary-100 dark:focus:ring-primary-500/20 transition-all shadow-sm"
                    >
                    <button type="submit" class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-primary-50 dark:bg-primary-500/10 rounded-xl flex items-center justify-center text-primary-600 dark:text-primary-400 hover:bg-primary-100 dark:hover:bg-primary-500/20 transition-all">
                        <i data-lucide="search" class="w-5 h-5"></i>
                    </button>
                </div>
                <select name="role" onchange="this.form.submit()" class="px-6 py-4 bg-white dark:bg-dark-card border-2 border-gray-200 dark:border-dark-border rounded-2xl text-gray-900 dark:text-white focus:border-primary-500 dark:focus:border-primary-500 focus:ring-4 focus:ring-primary-100 dark:focus:ring-primary-500/20 transition-all shadow-sm">
                    <option value="">كل الأدوار</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ $roleFilter == $role->id ? 'selected' : '' }}>{{ $role->display_name }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        {{-- Users Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-slide-up">
            @forelse($users as $user)
                <div class="bg-white dark:bg-dark-card rounded-3xl p-6 shadow-lg shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                    
                    {{-- User Header --}}
                    <div class="flex items-start justify-between mb-6">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 bg-gradient-to-br from-purple-100 to-purple-200 dark:from-purple-900/40 dark:to-purple-800/40 rounded-2xl flex items-center justify-center text-purple-600 dark:text-purple-400 shadow-md group-hover:scale-110 transition-transform">
                                <i data-lucide="user" class="w-8 h-8"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white mb-1">{{ $user->full_name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                    <i data-lucide="at-sign" class="w-3.5 h-3.5"></i>
                                    {{ $user->username }}
                                </p>
                            </div>
                        </div>
                        @if($user->is_active)
                        <span class="px-3 py-1.5 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-full text-xs font-bold border border-emerald-100 dark:border-emerald-500/30 flex items-center gap-1">
                            <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                            نشط
                        </span>
                        @else
                        <span class="px-3 py-1.5 bg-gray-50 dark:bg-gray-500/10 text-gray-600 dark:text-gray-400 rounded-full text-xs font-bold border border-gray-100 dark:border-gray-500/30 flex items-center gap-1">
                            <i data-lucide="x-circle" class="w-3.5 h-3.5"></i>
                            غير نشط
                        </span>
                        @endif
                    </div>

                    {{-- User Info --}}
                    <div class="mb-4">
                        <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-lg text-sm font-bold border border-blue-100 dark:border-blue-500/30">
                            <i data-lucide="shield" class="w-4 h-4"></i>
                            {{ $user->role->display_name ?? 'غير محدد' }}
                        </span>
                    </div>

                    {{-- Stats --}}
                    <div class="grid grid-cols-2 gap-3 mb-6">
                        @if($user->phone)
                        <div class="bg-gray-50 dark:bg-dark-bg rounded-2xl p-4 text-center">
                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1 flex items-center justify-center gap-1">
                                <i data-lucide="phone" class="w-3.5 h-3.5"></i>
                                الهاتف
                            </div>
                            <div class="text-sm font-black text-gray-900 dark:text-white truncate">{{ $user->phone }}</div>
                        </div>
                        @endif
                        @if($user->commission_rate && $user->role->name === 'marketer')
                        <div class="bg-gray-50 dark:bg-dark-bg rounded-2xl p-4 text-center">
                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1 flex items-center justify-center gap-1">
                                <i data-lucide="percent" class="w-3.5 h-3.5"></i>
                                العمولة
                            </div>
                            <div class="text-sm font-black text-emerald-600 dark:text-emerald-400">{{ $user->commission_rate }}%</div>
                        </div>
                        @endif
                        @if($user->role->name === 'marketer' && isset($user->available_balance))
                        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/30 dark:to-emerald-800/30 rounded-2xl p-4 text-center col-span-2 border border-emerald-200 dark:border-emerald-700">
                            <div class="text-xs text-emerald-600 dark:text-emerald-400 mb-1 flex items-center justify-center gap-1 font-bold">
                                <i data-lucide="wallet" class="w-3.5 h-3.5"></i>
                                الرصيد المستحق
                            </div>
                            <div class="text-lg font-black text-emerald-700 dark:text-emerald-300">{{ number_format($user->available_balance, 2) }} دينار</div>
                        </div>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2">
                        @if($user->role->name === 'marketer')
                        <a href="{{ route('admin.users.details', $user) }}" class="flex-1 px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                            تفاصيل
                        </a>
                        @endif
                        <a href="{{ route('admin.users.edit', $user) }}" class="{{ $user->role->name === 'marketer' ? 'w-12 h-12' : 'flex-1 px-5 py-3' }} bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                            <i data-lucide="edit" class="w-4 h-4"></i>
                            @if($user->role->name !== 'marketer')
                            تعديل
                            @endif
                        </a>
                        @if($user->phone)
                        <a href="tel:{{ $user->phone }}" class="w-12 h-12 bg-gray-100 dark:bg-dark-bg rounded-xl flex items-center justify-center text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">
                            <i data-lucide="phone" class="w-5 h-5"></i>
                        </a>
                        @endif
                    </div>

                </div>
            @empty
                <div class="col-span-full text-center py-16">
                    <div class="w-24 h-24 bg-gray-100 dark:bg-dark-bg rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="users" class="w-12 h-12 text-gray-400 dark:text-gray-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">لا يوجد مستخدمين</h3>
                    <p class="text-gray-500 dark:text-dark-muted">لم يتم إضافة أي مستخدمين بعد</p>
                </div>
            @endforelse
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
