<x-guest-layout>
    <div class="text-center mb-6 px-4">
        <h2 class="text-xl font-medium text-white tracking-tight leading-tight">مرحباً بعودتك</h2>
        <p class="text-gray-400 text-xs mt-2">سجل دخولك للمتابعة</p>
    </div>

    <x-auth-session-status class="mb-4 text-center" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Username -->
        <div>
            <label for="username" class="block text-xs font-bold text-gray-300 dark:text-gray-300 mb-2">اسم المستخدم</label>
            <input id="username" 
                   class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800/50 border border-gray-300 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                   type="text" 
                   name="username" 
                   value="{{ old('username') }}" 
                   required 
                   autofocus 
                   placeholder="أدخل اسم المستخدم" />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-bold text-gray-300 dark:text-gray-300 mb-2">كلمة المرور</label>
            <div class="relative">
                <input id="password" 
                       class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800/50 border border-gray-300 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                       type="password"
                       name="password"
                       required 
                       placeholder="أدخل كلمة المرور" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full py-3 text-sm bg-[#232323] hover:bg-[#2a2a2a] border border-white/5 text-white font-medium rounded-2xl shadow-lg transition-all duration-200">
                تسجيل الدخول
            </button>
        </div>
    </form>
</x-guest-layout>
