@extends('layouts.app')

@section('title', 'النسخ الاحتياطية')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-[1600px] mx-auto px-2">
        
        {{-- Header --}}
        <div class="animate-fade-in-down mb-8">
            <div class="flex items-center gap-3 mb-2">
                <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                    إدارة النظام
                </span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                النسخ الاحتياطية
            </h1>
        </div>

        {{-- Main Layout Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- Backups List --}}
            <div class="lg:col-span-8">
                {{-- Tabs --}}
                <div class="bg-white dark:bg-dark-card rounded-2xl p-2 shadow-lg shadow-gray-200/50 dark:shadow-none border border-gray-200 dark:border-dark-border mb-6">
                    <div class="flex gap-2">
                        <button onclick="switchTab('full')" id="tab-full" class="tab-btn flex-1 px-6 py-3 rounded-xl font-bold transition-all flex items-center justify-center gap-2 bg-primary-600 text-white">
                            <i data-lucide="package" class="w-5 h-5"></i>
                            نسخة كاملة
                        </button>
                        <button onclick="switchTab('database')" id="tab-database" class="tab-btn flex-1 px-6 py-3 rounded-xl font-bold transition-all flex items-center justify-center gap-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-dark-bg">
                            <i data-lucide="database" class="w-5 h-5"></i>
                            قواعد البيانات
                        </button>
                        <button onclick="switchTab('files')" id="tab-files" class="tab-btn flex-1 px-6 py-3 rounded-xl font-bold transition-all flex items-center justify-center gap-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-dark-bg">
                            <i data-lucide="folder" class="w-5 h-5"></i>
                            الملفات
                        </button>
                    </div>
                </div>

                <div class="bg-white dark:bg-dark-card rounded-[2rem] p-8 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border animate-slide-up">
                <div id="content-full" class="tab-content grid grid-cols-1 md:grid-cols-2 gap-6 animate-slide-up">
                    @php
                        $fullBackups = $backups->filter(fn($b) => str_contains($b['name'], '_full'));
                    @endphp
                    @forelse($fullBackups as $backup)
                        <div class="bg-white dark:bg-dark-card rounded-3xl p-6 shadow-lg shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                    
                    {{-- Backup Header --}}
                    <div class="flex items-start gap-4 mb-6">
                        <div class="w-16 h-16 bg-gradient-to-br from-emerald-100 to-emerald-200 dark:from-emerald-900/40 dark:to-emerald-800/40 rounded-2xl flex items-center justify-center text-emerald-600 dark:text-emerald-400 shadow-md group-hover:scale-110 transition-transform">
                            <i data-lucide="database" class="w-8 h-8"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-black text-gray-900 dark:text-white mb-1 truncate">{{ $backup['name'] }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                <i data-lucide="calendar" class="w-3 h-3"></i>
                                {{ $backup['date'] }}
                            </p>
                        </div>
                    </div>

                    {{-- Stats --}}
                    <div class="bg-gray-50 dark:bg-dark-bg rounded-2xl p-4 mb-6">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                <i data-lucide="hard-drive" class="w-3.5 h-3.5"></i>
                                حجم الملف
                            </span>
                            <span class="text-sm font-black text-gray-900 dark:text-white">{{ $backup['size'] }}</span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="space-y-2">
                        <a href="{{ route('admin.backups.download', $backup['name']) }}" class="w-full px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                            <i data-lucide="download" class="w-4 h-4"></i>
                            تحميل
                        </a>
                        
                        <div class="grid grid-cols-2 gap-2">
                            <form method="POST" action="{{ route('admin.backups.restore', $backup['name']) }}" class="restore-form">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2 text-sm">
                                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                                    استعادة
                                </button>
                            </form>
                            
                            <form method="POST" action="{{ route('admin.backups.delete', $backup['name']) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذه النسخة؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2 text-sm">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    حذف
                                </button>
                            </form>
                        </div>
                    </div>

                        </div>
                    @empty
                        <div class="col-span-full text-center py-16">
                            <div class="w-24 h-24 bg-gray-100 dark:bg-dark-bg rounded-full flex items-center justify-center mx-auto mb-4">
                                <i data-lucide="database" class="w-12 h-12 text-gray-400 dark:text-gray-600"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">لا توجد نسخ احتياطية</h3>
                            <p class="text-gray-500 dark:text-dark-muted mb-6">قم بإنشاء نسخة احتياطية جديدة للبدء</p>
                            <form method="POST" action="{{ route('admin.backups.create') }}" class="inline-block">
                                @csrf
                                <button type="submit" class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                                    <i data-lucide="plus" class="w-5 h-5"></i>
                                    إنشاء أول نسخة احتياطية
                                </button>
                            </form>
                        </div>
                    @endforelse
                </div>
                
                <div id="content-database" class="tab-content hidden grid grid-cols-1 md:grid-cols-2 gap-6 animate-slide-up">
                    @php
                        $dbBackups = $backups->filter(fn($b) => str_contains($b['name'], '_database'));
                    @endphp
                    @forelse($dbBackups as $backup)
                        <div class="bg-white dark:bg-dark-card rounded-3xl p-6 shadow-lg shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                    
                    {{-- Backup Header --}}
                    <div class="flex items-start gap-4 mb-6">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900/40 dark:to-blue-800/40 rounded-2xl flex items-center justify-center text-blue-600 dark:text-blue-400 shadow-md group-hover:scale-110 transition-transform">
                            <i data-lucide="database" class="w-8 h-8"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-black text-gray-900 dark:text-white mb-1 truncate">{{ $backup['name'] }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                <i data-lucide="calendar" class="w-3 h-3"></i>
                                {{ $backup['date'] }}
                            </p>
                        </div>
                    </div>

                    {{-- Stats --}}
                    <div class="bg-gray-50 dark:bg-dark-bg rounded-2xl p-4 mb-6">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                <i data-lucide="hard-drive" class="w-3.5 h-3.5"></i>
                                حجم الملف
                            </span>
                            <span class="text-sm font-black text-gray-900 dark:text-white">{{ $backup['size'] }}</span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="space-y-2">
                        <a href="{{ route('admin.backups.download', $backup['name']) }}" class="w-full px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                            <i data-lucide="download" class="w-4 h-4"></i>
                            تحميل
                        </a>
                        
                        <div class="grid grid-cols-2 gap-2">
                            <form method="POST" action="{{ route('admin.backups.restore', $backup['name']) }}" class="restore-form">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2 text-sm">
                                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                                    استعادة
                                </button>
                            </form>
                            
                            <form method="POST" action="{{ route('admin.backups.delete', $backup['name']) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذه النسخة؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2 text-sm">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    حذف
                                </button>
                            </form>
                        </div>
                    </div>

                        </div>
                    @empty
                        <div class="col-span-full text-center py-16">
                            <div class="w-24 h-24 bg-gray-100 dark:bg-dark-bg rounded-full flex items-center justify-center mx-auto mb-4">
                                <i data-lucide="database" class="w-12 h-12 text-gray-400 dark:text-gray-600"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">لا توجد نسخ قواعد بيانات</h3>
                        </div>
                    @endforelse
                </div>
                
                <div id="content-files" class="tab-content hidden grid grid-cols-1 md:grid-cols-2 gap-6 animate-slide-up">
                    @php
                        $filesBackups = $backups->filter(fn($b) => str_contains($b['name'], '_files'));
                    @endphp
                    @forelse($filesBackups as $backup)
                        <div class="bg-white dark:bg-dark-card rounded-3xl p-6 shadow-lg shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                    
                    {{-- Backup Header --}}
                    <div class="flex items-start gap-4 mb-6">
                        <div class="w-16 h-16 bg-gradient-to-br from-emerald-100 to-emerald-200 dark:from-emerald-900/40 dark:to-emerald-800/40 rounded-2xl flex items-center justify-center text-emerald-600 dark:text-emerald-400 shadow-md group-hover:scale-110 transition-transform">
                            <i data-lucide="folder" class="w-8 h-8"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-black text-gray-900 dark:text-white mb-1 truncate">{{ $backup['name'] }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                <i data-lucide="calendar" class="w-3 h-3"></i>
                                {{ $backup['date'] }}
                            </p>
                        </div>
                    </div>

                    {{-- Stats --}}
                    <div class="bg-gray-50 dark:bg-dark-bg rounded-2xl p-4 mb-6">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                <i data-lucide="hard-drive" class="w-3.5 h-3.5"></i>
                                حجم الملف
                            </span>
                            <span class="text-sm font-black text-gray-900 dark:text-white">{{ $backup['size'] }}</span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="space-y-2">
                        <a href="{{ route('admin.backups.download', $backup['name']) }}" class="w-full px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                            <i data-lucide="download" class="w-4 h-4"></i>
                            تحميل
                        </a>
                        
                        <div class="grid grid-cols-2 gap-2">
                            <form method="POST" action="{{ route('admin.backups.restore', $backup['name']) }}" class="restore-form">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2 text-sm">
                                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                                    استعادة
                                </button>
                            </form>
                            
                            <form method="POST" action="{{ route('admin.backups.delete', $backup['name']) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذه النسخة؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2 text-sm">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    حذف
                                </button>
                            </form>
                        </div>
                    </div>

                        </div>
                    @empty
                        <div class="col-span-full text-center py-16">
                            <div class="w-24 h-24 bg-gray-100 dark:bg-dark-bg rounded-full flex items-center justify-center mx-auto mb-4">
                                <i data-lucide="folder" class="w-12 h-12 text-gray-400 dark:text-gray-600"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">لا توجد نسخ ملفات</h3>
                        </div>
                    @endforelse
                </div>
                </div>
            </div>

            {{-- Info Guide --}}
            <div class="lg:col-span-4">
                <div class="bg-white dark:bg-dark-card rounded-[1.5rem] border border-gray-200 dark:border-dark-border p-8 shadow-lg shadow-gray-200/50 dark:shadow-sm lg:sticky lg:top-[150px]">
                    <div class="space-y-3 mb-6">
                        <button onclick="showCreateModal('full')" class="w-full px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                            <i data-lucide="package" class="w-5 h-5"></i>
                            <span>نسخة كاملة</span>
                        </button>
                        <button onclick="showCreateModal('database')" class="w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                            <i data-lucide="database" class="w-5 h-5"></i>
                            <span>قاعدة البيانات فقط</span>
                        </button>
                        <button onclick="showCreateModal('files')" class="w-full px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                            <i data-lucide="folder" class="w-5 h-5"></i>
                            <span>الملفات فقط</span>
                        </button>
                    </div>
                    
                    <h3 class="font-bold text-xl text-gray-900 dark:text-white mb-8 flex items-center gap-3 pt-6 border-t border-gray-200 dark:border-dark-border">
                        <i data-lucide="info" class="w-6 h-6 text-primary-500"></i>
                        ملاحظات مهمة
                    </h3>
                    
                    <div class="space-y-6">
                        <div class="relative">
                            <div class="flex items-start gap-4">
                                <div class="w-11 h-11 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 shadow-sm">
                                    <i data-lucide="database" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 dark:text-white text-base mb-1">نسخة كاملة</h4>
                                    <p class="text-sm text-gray-500 dark:text-dark-muted leading-relaxed">تشمل قاعدة البيانات + جميع الملفات المرفوعة</p>
                                </div>
                            </div>
                        </div>

                        <div class="relative">
                            <div class="flex items-start gap-4">
                                <div class="w-11 h-11 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 shadow-sm">
                                    <i data-lucide="refresh-cw" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 dark:text-white text-base mb-1">الاستعادة</h4>
                                    <p class="text-sm text-gray-500 dark:text-dark-muted leading-relaxed">عملية الاستعادة ستستبدل جميع البيانات الحالية</p>
                                </div>
                            </div>
                        </div>

                        <div class="relative">
                            <div class="flex items-start gap-4">
                                <div class="w-11 h-11 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 flex items-center justify-center shrink-0 shadow-sm">
                                    <i data-lucide="shield" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 dark:text-white text-base mb-1">النسخ الدوري</h4>
                                    <p class="text-sm text-gray-500 dark:text-dark-muted leading-relaxed">يُنصح بإنشاء نسخة احتياطية قبل أي تحديث كبير</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-900/30 rounded-xl p-4 mt-6">
                            <div class="flex gap-3">
                                <i data-lucide="alert-circle" class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5"></i>
                                <div class="text-sm text-amber-800 dark:text-amber-300">
                                    <p class="font-bold mb-1">تحذير:</p>
                                    <p>عملية الاستعادة لا يمكن التراجع عنها. تأكد من اختيار النسخة الصحيحة.</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3 pt-6 border-t border-gray-200 dark:border-dark-border">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Create Backup Modal --}}
<div id="createModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center">
    <div class="bg-white dark:bg-dark-card rounded-3xl p-8 max-w-md w-full mx-4 shadow-2xl">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/40 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="alert-circle" class="w-8 h-8 text-blue-600 dark:text-blue-400"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">تأكيد إنشاء النسخة</h3>
            <p class="text-gray-500 dark:text-gray-400" id="modalMessage">هل تريد حقاً إنشاء نسخة احتياطية؟</p>
        </div>
        <form method="POST" action="{{ route('admin.backups.create') }}" id="createForm">
            @csrf
            <input type="hidden" name="type" id="backupType">
            <div class="flex gap-3">
                <button type="button" onclick="hideCreateModal()" class="flex-1 px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-bold transition-all hover:bg-gray-300 dark:hover:bg-gray-600">
                    إلغاء
                </button>
                <button type="submit" class="flex-1 px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all shadow-md hover:shadow-lg">
                    تأكيد
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Restore Modal --}}
<div id="restoreModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center">
    <div class="bg-white dark:bg-dark-card rounded-3xl p-8 max-w-md w-full mx-4 shadow-2xl">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-900/40 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="refresh-cw" class="w-8 h-8 text-emerald-600 dark:text-emerald-400 animate-spin"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">جاري الاستعادة...</h3>
            <p class="text-gray-500 dark:text-gray-400">يرجى الانتظار حتى تكتمل العملية</p>
        </div>
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4 overflow-hidden">
            <div id="progressBar" class="bg-gradient-to-r from-emerald-500 to-emerald-600 h-full rounded-full transition-all duration-300" style="width: 0%"></div>
        </div>
        <p id="progressText" class="text-center text-sm text-gray-600 dark:text-gray-400 mt-3">0%</p>
    </div>
</div>

@push('scripts')
<script>
    let currentTab = 'full';
    
    function switchTab(tab) {
        currentTab = tab;
        
        // Update tabs
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('bg-primary-600', 'text-white');
            btn.classList.add('text-gray-600', 'dark:text-gray-400', 'hover:bg-gray-100', 'dark:hover:bg-dark-bg');
        });
        document.getElementById('tab-' + tab).classList.add('bg-primary-600', 'text-white');
        document.getElementById('tab-' + tab).classList.remove('text-gray-600', 'dark:text-gray-400', 'hover:bg-gray-100', 'dark:hover:bg-dark-bg');
        
        // Update content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        document.getElementById('content-' + tab).classList.remove('hidden');
        
        lucide.createIcons();
    }
    
    function showCreateModal(type) {
        const messages = {
            'full': 'هل تريد حقاً إنشاء نسخة احتياطية كاملة؟',
            'database': 'هل تريد حقاً إنشاء نسخة احتياطية لقاعدة البيانات فقط؟',
            'files': 'هل تريد حقاً إنشاء نسخة احتياطية للملفات فقط؟'
        };
        
        document.getElementById('modalMessage').textContent = messages[type];
        document.getElementById('backupType').value = type;
        document.getElementById('createModal').classList.remove('hidden');
        lucide.createIcons();
    }
    
    function hideCreateModal() {
        document.getElementById('createModal').classList.add('hidden');
    }
    
    // Close modal on form submit
    document.getElementById('createForm')?.addEventListener('submit', function() {
        hideCreateModal();
    });
    
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        
        document.querySelectorAll('.restore-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('⚠️ هل أنت متأكد من استعادة هذه النسخة؟\n\nسيتم استبدال جميع البيانات الحالية!')) {
                    e.preventDefault();
                    return;
                }
                
                const modal = document.getElementById('restoreModal');
                const progressBar = document.getElementById('progressBar');
                const progressText = document.getElementById('progressText');
                
                modal.classList.remove('hidden');
                
                let progress = 0;
                const interval = setInterval(() => {
                    progress += 2;
                    if (progress <= 95) {
                        progressBar.style.width = progress + '%';
                        progressText.textContent = progress + '%';
                    }
                }, 100);
                
                setTimeout(() => {
                    clearInterval(interval);
                }, 5000);
            });
        });
    });
</script>
@endpush

@endsection
