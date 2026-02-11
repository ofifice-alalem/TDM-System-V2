@extends('layouts.app')

@section('title', 'إضافة مستخدم جديد')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-4xl mx-auto space-y-8 px-2">
        
        {{-- Back Button & Header --}}
        <div class="animate-fade-in-down">
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 mb-4 transition-colors">
                <i data-lucide="arrow-right" class="w-5 h-5"></i>
                <span class="font-bold">العودة للمستخدمين</span>
            </a>
            <div class="flex items-center gap-3 mb-2">
                <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                    إضافة جديد
                </span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                إضافة مستخدم جديد
            </h1>
        </div>

        {{-- Form Card --}}
        <div class="bg-white dark:bg-dark-card rounded-3xl p-8 shadow-lg shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border animate-slide-up">
            
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf

                <div class="space-y-6">
                    
                    {{-- Username --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            <i data-lucide="at-sign" class="w-4 h-4 inline-block ml-1"></i>
                            اسم المستخدم <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="username" value="{{ old('username') }}" required
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-dark-bg border-2 border-gray-200 dark:border-dark-border rounded-xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-primary-500 dark:focus:border-primary-500 focus:ring-4 focus:ring-primary-100 dark:focus:ring-primary-500/20 transition-all"
                            placeholder="أدخل اسم المستخدم">
                        @error('username')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            <i data-lucide="lock" class="w-4 h-4 inline-block ml-1"></i>
                            كلمة المرور <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password" required
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-dark-bg border-2 border-gray-200 dark:border-dark-border rounded-xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-primary-500 dark:focus:border-primary-500 focus:ring-4 focus:ring-primary-100 dark:focus:ring-primary-500/20 transition-all"
                            placeholder="أدخل كلمة المرور">
                        @error('password')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Full Name --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            <i data-lucide="user" class="w-4 h-4 inline-block ml-1"></i>
                            الاسم الكامل <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="full_name" value="{{ old('full_name') }}" required
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-dark-bg border-2 border-gray-200 dark:border-dark-border rounded-xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-primary-500 dark:focus:border-primary-500 focus:ring-4 focus:ring-primary-100 dark:focus:ring-primary-500/20 transition-all"
                            placeholder="أدخل الاسم الكامل">
                        @error('full_name')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Role --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            <i data-lucide="shield" class="w-4 h-4 inline-block ml-1"></i>
                            الدور <span class="text-red-500">*</span>
                        </label>
                        <select name="role_id" id="role_id" required
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-dark-bg border-2 border-gray-200 dark:border-dark-border rounded-xl text-gray-900 dark:text-white focus:border-primary-500 dark:focus:border-primary-500 focus:ring-4 focus:ring-primary-100 dark:focus:ring-primary-500/20 transition-all">
                            <option value="">اختر الدور</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" data-name="{{ $role->name }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->display_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Commission Rate (for marketers only) --}}
                    <div id="commission_field" style="display: none;">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            <i data-lucide="percent" class="w-4 h-4 inline-block ml-1"></i>
                            نسبة العمولة (%)
                        </label>
                        <input type="number" name="commission_rate" value="{{ old('commission_rate') }}" step="0.01" min="0" max="100"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-dark-bg border-2 border-gray-200 dark:border-dark-border rounded-xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-primary-500 dark:focus:border-primary-500 focus:ring-4 focus:ring-primary-100 dark:focus:ring-primary-500/20 transition-all"
                            placeholder="أدخل نسبة العمولة">
                        @error('commission_rate')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            <i data-lucide="phone" class="w-4 h-4 inline-block ml-1"></i>
                            رقم الهاتف
                        </label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-dark-bg border-2 border-gray-200 dark:border-dark-border rounded-xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-primary-500 dark:focus:border-primary-500 focus:ring-4 focus:ring-primary-100 dark:focus:ring-primary-500/20 transition-all"
                            placeholder="أدخل رقم الهاتف">
                        @error('phone')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-4 mt-8 pt-6 border-t border-gray-200 dark:border-dark-border">
                    <button type="submit" class="flex-1 px-6 py-4 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                        <i data-lucide="check" class="w-5 h-5"></i>
                        حفظ المستخدم
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="px-6 py-4 bg-gray-100 dark:bg-dark-bg hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-bold transition-all flex items-center justify-center gap-2">
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
        
        const roleSelect = document.getElementById('role_id');
        const commissionField = document.getElementById('commission_field');
        
        roleSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const roleName = selectedOption.dataset.name;
            
            if (roleName === 'marketer') {
                commissionField.style.display = 'block';
            } else {
                commissionField.style.display = 'none';
            }
        });
        
        // Trigger on page load if role is already selected
        if (roleSelect.value) {
            roleSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endpush

@endsection
