<x-guest-layout>
    <div class="text-center mb-8 px-4">
        <h2 class="text-2xl font-medium text-white tracking-tight leading-tight">Taqnia-Distribution-Manager</h2>
    </div>

    <x-auth-session-status class="mb-4 text-center" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email -->
        <div class="relative">
            <input id="username" 
                   class="block w-full px-5 py-4 bg-[#1a1a1a] border border-white/5 rounded-2xl text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-white/20 transition-all duration-200"
                   type="text" 
                   name="username" 
                   value="{{ old('username') }}" 
                   required 
                   autofocus 
                   placeholder="Email" />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="relative">
            <input id="password" 
                   class="block w-full px-5 py-4 bg-[#1a1a1a] border border-white/5 rounded-2xl text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-white/20 transition-all duration-200"
                   type="password"
                   name="password"
                   required 
                   placeholder="Password" />
            <div class="absolute right-4 top-1/2 -translate-y-1/2 opacity-30 cursor-pointer hover:opacity-100 transition-opacity">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full py-4 bg-[#232323] hover:bg-[#2a2a2a] border border-white/5 text-white font-medium rounded-2xl shadow-lg transition-all duration-200">
                Sign in
            </button>
        </div>
    </form>
</x-guest-layout>
