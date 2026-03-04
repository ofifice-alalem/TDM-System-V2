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
                <label class="flex items-center gap-3 px-6 py-4 bg-white dark:bg-dark-card border-2 border-gray-200 dark:border-dark-border rounded-2xl cursor-pointer hover:border-red-500 transition-all">
                    <input type="checkbox" name="show_deleted" value="1" {{ request('show_deleted') ? 'checked' : '' }} onchange="this.form.submit()" class="w-5 h-5 text-red-600 rounded focus:ring-red-500">
                    <span class="text-gray-900 dark:text-white font-bold">المحذوفين</span>
                </label>
                <button type="button" onclick="toggleView()" class="px-6 py-4 bg-white dark:bg-dark-card border-2 border-gray-200 dark:border-dark-border rounded-2xl text-gray-900 dark:text-white hover:border-primary-500 transition-all flex items-center gap-2">
                    <i data-lucide="table" class="w-5 h-5" id="viewIcon"></i>
                    <span id="viewText">شبكة</span>
                </button>
            </form>
        </div>

        {{-- Grid View --}}
        <div id="gridView" class="hidden grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-slide-up">
            @forelse($users as $user)
                <div class="bg-white dark:bg-dark-card rounded-3xl p-6 shadow-lg shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                    
                    {{-- User Header --}}
                    <div class="flex items-start justify-between mb-6">
                        <div class="flex items-center gap-4">
                            @php
                                $roleColors = [
                                    'admin' => ['from' => 'red-100', 'to' => 'red-200', 'dark-from' => 'red-900/40', 'dark-to' => 'red-800/40', 'text' => 'red-600', 'dark-text' => 'red-400'],
                                    'warehouse' => ['from' => 'orange-100', 'to' => 'orange-200', 'dark-from' => 'orange-900/40', 'dark-to' => 'orange-800/40', 'text' => 'orange-600', 'dark-text' => 'orange-400'],
                                    'marketer' => ['from' => 'emerald-100', 'to' => 'emerald-200', 'dark-from' => 'emerald-900/40', 'dark-to' => 'emerald-800/40', 'text' => 'emerald-600', 'dark-text' => 'emerald-400'],
                                    'sales' => ['from' => 'purple-100', 'to' => 'purple-200', 'dark-from' => 'purple-900/40', 'dark-to' => 'purple-800/40', 'text' => 'purple-600', 'dark-text' => 'purple-400'],
                                ];
                                $color = $roleColors[$user->role->name] ?? $roleColors['sales'];
                            @endphp
                            <div class="w-16 h-16 bg-gradient-to-br from-{{ $color['from'] }} to-{{ $color['to'] }} dark:from-{{ $color['dark-from'] }} dark:to-{{ $color['dark-to'] }} rounded-2xl flex items-center justify-center text-{{ $color['text'] }} dark:text-{{ $color['dark-text'] }} shadow-md group-hover:scale-110 transition-transform">
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
                        @php
                            $roleBadgeColors = [
                                'admin' => 'bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 border-red-100 dark:border-red-500/30',
                                'warehouse' => 'bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 border-orange-100 dark:border-orange-500/30',
                                'marketer' => 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-100 dark:border-emerald-500/30',
                                'sales' => 'bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-100 dark:border-purple-500/30',
                            ];
                            $badgeColor = $roleBadgeColors[$user->role->name] ?? $roleBadgeColors['sales'];
                        @endphp
                        <span class="inline-flex items-center gap-2 px-3 py-1.5 {{ $badgeColor }} rounded-lg text-sm font-bold border">
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
                        @if(request('show_deleted'))
                            <form action="{{ route('admin.users.restore', $user->id) }}" method="POST" class="flex-1 restore-form">
                                @csrf
                                <button type="button" class="restore-btn w-full px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2" data-user-name="{{ $user->full_name }}">
                                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                                    استعادة
                                </button>
                            </form>
                            <form action="{{ route('admin.users.force-destroy', $user->id) }}" method="POST" class="force-delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="force-delete-btn w-12 h-12 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition-all shadow-md hover:shadow-lg flex items-center justify-center" title="حذف نهائي" data-user-name="{{ $user->full_name }}">
                                    <i data-lucide="x" class="w-5 h-5"></i>
                                </button>
                            </form>
                        @else
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
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="delete-btn w-12 h-12 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition-all shadow-md hover:shadow-lg flex items-center justify-center" data-user-name="{{ $user->full_name }}">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                            @if($user->phone)
                            <a href="tel:{{ $user->phone }}" class="w-12 h-12 bg-gray-100 dark:bg-dark-bg rounded-xl flex items-center justify-center text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">
                                <i data-lucide="phone" class="w-5 h-5"></i>
                            </a>
                            @endif
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

        {{-- Table View --}}
        <div id="tableView" class="animate-slide-up">
            <div class="bg-white dark:bg-dark-card rounded-3xl shadow-lg border border-gray-200 dark:border-dark-border overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-dark-bg">
                            <tr>
                                <th class="px-6 py-4 text-right text-xs font-black text-gray-700 dark:text-gray-300 uppercase tracking-wider">المستخدم</th>
                                <th class="px-6 py-4 text-right text-xs font-black text-gray-700 dark:text-gray-300 uppercase tracking-wider">الدور</th>
                                <th class="px-6 py-4 text-right text-xs font-black text-gray-700 dark:text-gray-300 uppercase tracking-wider">الهاتف</th>
                                <th class="px-6 py-4 text-right text-xs font-black text-gray-700 dark:text-gray-300 uppercase tracking-wider">العمولة</th>
                                <th class="px-6 py-4 text-right text-xs font-black text-gray-700 dark:text-gray-300 uppercase tracking-wider">الرصيد</th>
                                <th class="px-6 py-4 text-right text-xs font-black text-gray-700 dark:text-gray-300 uppercase tracking-wider">الحالة</th>
                                <th class="px-6 py-4 text-center text-xs font-black text-gray-700 dark:text-gray-300 uppercase tracking-wider">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-dark-border">
                            @forelse($users as $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @php
                                            $roleColors = [
                                                'admin' => ['from' => 'red-100', 'to' => 'red-200', 'dark-from' => 'red-900/40', 'dark-to' => 'red-800/40', 'text' => 'red-600', 'dark-text' => 'red-400'],
                                                'warehouse' => ['from' => 'orange-100', 'to' => 'orange-200', 'dark-from' => 'orange-900/40', 'dark-to' => 'orange-800/40', 'text' => 'orange-600', 'dark-text' => 'orange-400'],
                                                'marketer' => ['from' => 'emerald-100', 'to' => 'emerald-200', 'dark-from' => 'emerald-900/40', 'dark-to' => 'emerald-800/40', 'text' => 'emerald-600', 'dark-text' => 'emerald-400'],
                                                'sales' => ['from' => 'purple-100', 'to' => 'purple-200', 'dark-from' => 'purple-900/40', 'dark-to' => 'purple-800/40', 'text' => 'purple-600', 'dark-text' => 'purple-400'],
                                            ];
                                            $color = $roleColors[$user->role->name] ?? $roleColors['sales'];
                                        @endphp
                                        <div class="w-10 h-10 bg-gradient-to-br from-{{ $color['from'] }} to-{{ $color['to'] }} dark:from-{{ $color['dark-from'] }} dark:to-{{ $color['dark-to'] }} rounded-xl flex items-center justify-center text-{{ $color['text'] }} dark:text-{{ $color['dark-text'] }}">
                                            <i data-lucide="user" class="w-5 h-5"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 dark:text-white">{{ $user->full_name }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $user->username }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $roleBadgeColors = [
                                            'admin' => 'bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400',
                                            'warehouse' => 'bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400',
                                            'marketer' => 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
                                            'sales' => 'bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400',
                                        ];
                                        $badgeColor = $roleBadgeColors[$user->role->name] ?? $roleBadgeColors['sales'];
                                    @endphp
                                    <span class="inline-flex items-center gap-2 px-3 py-1 {{ $badgeColor }} rounded-lg text-sm font-bold">
                                        {{ $user->role->display_name ?? 'غير محدد' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->phone)
                                    <a href="tel:{{ $user->phone }}" class="text-gray-900 dark:text-white font-medium hover:text-primary-600 dark:hover:text-primary-400 transition-colors flex items-center gap-2">
                                        <i data-lucide="phone" class="w-4 h-4"></i>
                                        {{ $user->phone }}
                                    </a>
                                    @else
                                    <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-900 dark:text-white font-bold">{{ $user->commission_rate ? $user->commission_rate . '%' : '-' }}</td>
                                <td class="px-6 py-4">
                                    @if($user->role->name === 'marketer' && isset($user->available_balance))
                                    <span class="text-emerald-600 dark:text-emerald-400 font-bold">{{ number_format($user->available_balance, 2) }} د</span>
                                    @else
                                    <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->is_active)
                                    <span class="px-3 py-1 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-full text-xs font-bold">نشط</span>
                                    @else
                                    <span class="px-3 py-1 bg-gray-50 dark:bg-gray-500/10 text-gray-600 dark:text-gray-400 rounded-full text-xs font-bold">غير نشط</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        @if(request('show_deleted'))
                                            <form action="{{ route('admin.users.restore', $user->id) }}" method="POST" class="restore-form">
                                                @csrf
                                                <button type="button" class="restore-btn w-9 h-9 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-all flex items-center justify-center" data-user-name="{{ $user->full_name }}">
                                                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.users.force-destroy', $user->id) }}" method="POST" class="force-delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="force-delete-btn w-9 h-9 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all flex items-center justify-center" data-user-name="{{ $user->full_name }}">
                                                    <i data-lucide="x" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        @else
                                            @if($user->role->name === 'marketer')
                                            <a href="{{ route('admin.users.details', $user) }}" class="w-9 h-9 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-all flex items-center justify-center">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </a>
                                            @endif
                                            <a href="{{ route('admin.users.edit', $user) }}" class="w-9 h-9 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-all flex items-center justify-center">
                                                <i data-lucide="edit" class="w-4 h-4"></i>
                                            </a>
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="delete-btn w-9 h-9 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all flex items-center justify-center" data-user-name="{{ $user->full_name }}">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="w-24 h-24 bg-gray-100 dark:bg-dark-bg rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i data-lucide="users" class="w-12 h-12 text-gray-400 dark:text-gray-600"></i>
                                    </div>
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">لا يوجد مستخدمين</h3>
                                    <p class="text-gray-500 dark:text-dark-muted">لم يتم إضافة أي مستخدمين بعد</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div id="deleteModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-dark-card rounded-3xl shadow-2xl max-w-md w-full p-8 transform transition-all">
        <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
            <i data-lucide="trash-2" class="w-8 h-8 text-red-600 dark:text-red-400"></i>
        </div>
        <h3 class="text-2xl font-black text-gray-900 dark:text-white text-center mb-2">حذف المستخدم</h3>
        <p class="text-gray-600 dark:text-gray-400 text-center mb-6">هل أنت متأكد من حذف المستخدم "<span id="deleteUserName" class="font-bold"></span>"؟</p>
        <div class="flex gap-3">
            <button onclick="closeDeleteModal()" class="flex-1 px-6 py-3 bg-gray-100 dark:bg-dark-bg hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-bold transition-all">
                إلغاء
            </button>
            <button onclick="confirmDelete()" class="flex-1 px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition-all shadow-lg">
                حذف
            </button>
        </div>
    </div>
</div>

{{-- Force Delete Confirmation Modal --}}
<div id="forceDeleteModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-dark-card rounded-3xl shadow-2xl max-w-md w-full p-8 transform transition-all">
        <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
            <i data-lucide="alert-triangle" class="w-8 h-8 text-red-600 dark:text-red-400"></i>
        </div>
        <h3 class="text-2xl font-black text-red-600 dark:text-red-400 text-center mb-2">تحذير: حذف نهائي</h3>
        <p class="text-gray-600 dark:text-gray-400 text-center mb-6">الحذف النهائي لا يمكن التراجع عنه!<br><br>هل أنت متأكد من حذف "<span id="forceDeleteUserName" class="font-bold"></span>" نهائياً؟</p>
        <div class="flex gap-3">
            <button onclick="closeForceDeleteModal()" class="flex-1 px-6 py-3 bg-gray-100 dark:bg-dark-bg hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-bold transition-all">
                إلغاء
            </button>
            <button onclick="confirmForceDelete()" class="flex-1 px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition-all shadow-lg">
                حذف نهائياً
            </button>
        </div>
    </div>
</div>

{{-- Restore Confirmation Modal --}}
<div id="restoreModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-dark-card rounded-3xl shadow-2xl max-w-md w-full p-8 transform transition-all">
        <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
            <i data-lucide="rotate-ccw" class="w-8 h-8 text-emerald-600 dark:text-emerald-400"></i>
        </div>
        <h3 class="text-2xl font-black text-gray-900 dark:text-white text-center mb-2">استعادة المستخدم</h3>
        <p class="text-gray-600 dark:text-gray-400 text-center mb-6">هل أنت متأكد من استعادة المستخدم "<span id="restoreUserName" class="font-bold"></span>"؟</p>
        <div class="flex gap-3">
            <button onclick="closeRestoreModal()" class="flex-1 px-6 py-3 bg-gray-100 dark:bg-dark-bg hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-bold transition-all">
                إلغاء
            </button>
            <button onclick="confirmRestore()" class="flex-1 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition-all shadow-lg">
                استعادة
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentDeleteForm = null;
    let currentForceDeleteForm = null;
    let currentRestoreForm = null;

    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();

        // Delete confirmation
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                currentDeleteForm = this.closest('form');
                document.getElementById('deleteUserName').textContent = this.dataset.userName;
                document.getElementById('deleteModal').classList.remove('hidden');
            });
        });

        // Force delete confirmation
        document.querySelectorAll('.force-delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                currentForceDeleteForm = this.closest('form');
                document.getElementById('forceDeleteUserName').textContent = this.dataset.userName;
                document.getElementById('forceDeleteModal').classList.remove('hidden');
            });
        });

        // Restore confirmation
        document.querySelectorAll('.restore-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                currentRestoreForm = this.closest('form');
                document.getElementById('restoreUserName').textContent = this.dataset.userName;
                document.getElementById('restoreModal').classList.remove('hidden');
            });
        });
    });

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        currentDeleteForm = null;
    }

    function confirmDelete() {
        if (currentDeleteForm) {
            currentDeleteForm.submit();
        }
    }

    function closeForceDeleteModal() {
        document.getElementById('forceDeleteModal').classList.add('hidden');
        currentForceDeleteForm = null;
    }

    function confirmForceDelete() {
        if (currentForceDeleteForm) {
            currentForceDeleteForm.submit();
        }
    }

    function closeRestoreModal() {
        document.getElementById('restoreModal').classList.add('hidden');
        currentRestoreForm = null;
    }

    function confirmRestore() {
        if (currentRestoreForm) {
            currentRestoreForm.submit();
        }
    }

    function toggleView() {
        const gridView = document.getElementById('gridView');
        const tableView = document.getElementById('tableView');
        const viewIcon = document.getElementById('viewIcon');
        const viewText = document.getElementById('viewText');
        
        if (tableView.classList.contains('hidden')) {
            gridView.classList.add('hidden');
            gridView.classList.remove('grid');
            tableView.classList.remove('hidden');
            viewIcon.setAttribute('data-lucide', 'table');
            viewText.textContent = 'شبكة';
        } else {
            gridView.classList.remove('hidden');
            gridView.classList.add('grid');
            tableView.classList.add('hidden');
            viewIcon.setAttribute('data-lucide', 'layout-grid');
            viewText.textContent = 'جدول';
        }
        lucide.createIcons();
    }
</script>
@endpush

@endsection
