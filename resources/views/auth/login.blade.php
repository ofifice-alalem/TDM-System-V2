<x-guest-layout>
    <div class="text-center mb-8 px-4">
        <h2 class="text-2xl font-medium text-white tracking-tight leading-tight">مرحباً بعودتك</h2>
        <p class="text-gray-400 text-sm mt-2">سجل دخولك للمتابعة</p>
    </div>

    <x-auth-session-status class="mb-4 text-center" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Username -->
        <div>
            <label for="username" class="block text-sm font-medium text-gray-300 mb-2">اسم المستخدم</label>
            <input id="username" 
                   class="block w-full px-5 py-4 bg-[#1a1a1a] border border-white/5 rounded-2xl text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-white/20 transition-all duration-200"
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
            <label for="password" class="block text-sm font-medium text-gray-300 mb-2">كلمة المرور</label>
            <div class="relative">
                <input id="password" 
                       class="block w-full px-5 py-4 bg-[#1a1a1a] border border-white/5 rounded-2xl text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-white/20 transition-all duration-200"
                       type="password"
                       name="password"
                       required 
                       placeholder="أدخل كلمة المرور" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full py-4 bg-[#232323] hover:bg-[#2a2a2a] border border-white/5 text-white font-medium rounded-2xl shadow-lg transition-all duration-200">
                تسجيل الدخول
            </button>
        </div>
    </form>
</x-guest-layout>
