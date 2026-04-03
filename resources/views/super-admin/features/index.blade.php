@extends('layouts.super-admin')

@section('title', 'إدارة الميزات')

@section('content')
<div class="space-y-6 animate-fade-in">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <span class="text-xs font-black bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 px-3 py-1 rounded-lg">Super Admin</span>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white mt-2">إدارة الميزات</h1>
            <p class="text-gray-400 dark:text-dark-muted text-sm mt-1">تحكم في تفعيل وتعطيل ميزات النظام</p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border text-gray-600 dark:text-gray-300 rounded-xl font-bold text-sm hover:bg-gray-50 dark:hover:bg-dark-bg transition-all shadow-sm">
                <i data-lucide="log-out" class="w-4 h-4"></i> خروج
            </button>
        </form>
    </div>

    {{-- Feature Groups --}}
    @foreach($features->groupBy('role') as $role => $roleFeatures)
        <div class="bg-white dark:bg-dark-card rounded-2xl border border-gray-100 dark:border-dark-border shadow-soft overflow-hidden">

            {{-- Group Header --}}
            <div class="px-6 py-4 bg-gray-50 dark:bg-dark-bg border-b border-gray-100 dark:border-dark-border flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-amber-100 dark:bg-accent-500/10 flex items-center justify-center">
                    <i data-lucide="shield" class="w-4 h-4 text-amber-600 dark:text-accent-400"></i>
                </div>
                <h2 class="font-black text-gray-800 dark:text-gray-200">
                    {{ match($role) {
                        'admin'                => 'المدير',
                        'admin.statistics'     => 'المدير - الإحصائيات',
                        'marketer'             => 'المسوق',
                        'marketer.statistics'  => 'المسوق - الإحصائيات',
                        'warehouse'            => 'المستودع',
                        'sales'                => 'المبيعات',
                        default                => $role
                    } }}
                </h2>
                @if($role === 'admin.statistics')
                <a href="{{ route('admin.statistics.index') }}" target="_blank"
                   class="p-1.5 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-500 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors"
                   title="فتح صفحة الإحصائيات">
                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                </a>
                @elseif($role === 'marketer.statistics')
                <a href="{{ route('marketer.statistics.index') }}" target="_blank"
                   class="p-1.5 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-500 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors"
                   title="فتح صفحة إحصائيات المسوق">
                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                </a>
                @endif
                <span class="text-xs font-bold text-gray-400 bg-gray-100 dark:bg-dark-border px-2.5 py-0.5 rounded-full">{{ $roleFeatures->count() }} ميزة</span>
            </div>

            {{-- Features List --}}
            <div class="divide-y divide-gray-50 dark:divide-dark-border">
                @foreach($roleFeatures as $feature)
                    <div x-data="featureRow({{ $feature->id }}, {{ $feature->is_enabled ? 'true' : 'false' }}, '{{ $feature->mode }}', '{{ $feature->starts_at?->format('Y-m-d\TH:i') ?? '' }}', '{{ $feature->ends_at?->format('Y-m-d\TH:i') ?? '' }}')"
                         class="px-6 py-5">

                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                {{-- Toggle --}}
                                <button @click="toggleEnabled()"
                                    :class="activeNow ? 'bg-emerald-500' : 'bg-red-400'"
                                    class="relative w-12 h-6 rounded-full transition-colors duration-200 focus:outline-none shrink-0 shadow-inner">
                                    <span :class="activeNow ? '-translate-x-1' : '-translate-x-6'"
                                          class="absolute top-1 left-auto right-1 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 block"></span>
                                </button>
                                <div>
                                    <p class="font-black text-gray-900 dark:text-gray-100">{{ $feature->label }}</p>
                                    <p class="text-xs text-gray-400 dark:text-dark-muted font-mono mt-0.5">{{ $feature->key }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <span :class="activeNow ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400' : 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400'"
                                      class="text-xs font-black px-3 py-1 rounded-lg"
                                      x-text="activeNow ? 'مفعّل' : 'معطّل'"></span>
                                @php
                                    $featureUrls = [
                                        'admin.combined-summary'  => route('admin.combined-summary.index'),
                                        'admin.products-pricing'  => route('admin.products-pricing.index'),
                                        'admin.customer-merge'    => route('admin.customer-merge.index'),
                                        'admin.store-merge'       => route('admin.store-merge.index'),
                                        'admin.staff-pricing'     => route('admin.staff-pricing.index'),
                                    ];
                                @endphp
                                @if(isset($featureUrls[$feature->key]))
                                <a href="{{ $featureUrls[$feature->key] }}" target="_blank"
                                   class="p-2 rounded-xl border bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 border-blue-200 dark:border-blue-500/30 hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-all"
                                   title="زيارة الرابط">
                                    <i data-lucide="external-link" class="w-4 h-4"></i>
                                </a>
                                @endif
                                <button @click="showSettings = !showSettings"
                                    :class="showSettings ? 'bg-amber-50 dark:bg-accent-500/10 text-amber-600 dark:text-accent-400 border-amber-200 dark:border-accent-500/30' : 'bg-gray-50 dark:bg-dark-bg text-gray-400 border-gray-100 dark:border-dark-border hover:bg-gray-100 dark:hover:bg-dark-border'"
                                    class="p-2 rounded-xl border transition-all">
                                    <i data-lucide="settings-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Settings Panel --}}
                        <div x-show="showSettings" x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="mt-5 pt-5 border-t border-gray-50 dark:border-dark-border space-y-4">

                            <div>
                                <label class="block text-xs font-black text-gray-500 dark:text-dark-muted mb-2 uppercase tracking-wider">وضع التحكم</label>
                                <div class="flex flex-wrap gap-2">
                                    <button @click="mode = 'permanent'; save()"
                                        :class="mode === 'permanent' ? 'bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-900 shadow-sm' : 'bg-gray-100 dark:bg-dark-bg text-gray-500 dark:text-dark-muted hover:bg-gray-200 dark:hover:bg-dark-border'"
                                        class="px-4 py-2 rounded-xl text-xs font-black transition-all">
                                        دائم
                                    </button>
                                    <button @click="mode = 'scheduled_off'"
                                        :class="mode === 'scheduled_off' ? 'bg-orange-500 text-white shadow-sm' : 'bg-gray-100 dark:bg-dark-bg text-gray-500 dark:text-dark-muted hover:bg-gray-200 dark:hover:bg-dark-border'"
                                        class="px-4 py-2 rounded-xl text-xs font-black transition-all">
                                        تعطيل مؤقت
                                    </button>
                                    <button @click="mode = 'scheduled_on'"
                                        :class="mode === 'scheduled_on' ? 'bg-sky-500 text-white shadow-sm' : 'bg-gray-100 dark:bg-dark-bg text-gray-500 dark:text-dark-muted hover:bg-gray-200 dark:hover:bg-dark-border'"
                                        class="px-4 py-2 rounded-xl text-xs font-black transition-all">
                                        تفعيل مؤقت
                                    </button>
                                </div>
                            </div>

                            <div x-show="mode !== 'permanent'" x-cloak class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-black text-gray-500 dark:text-dark-muted mb-1.5">من</label>
                                    <input type="datetime-local" x-model="startsAt"
                                        class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-3 py-2 text-sm text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-amber-400 dark:focus:ring-accent-500 transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-gray-500 dark:text-dark-muted mb-1.5">إلى</label>
                                    <input type="datetime-local" x-model="endsAt"
                                        class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-3 py-2 text-sm text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-amber-400 dark:focus:ring-accent-500 transition-all">
                                </div>
                            </div>

                            <div x-show="mode !== 'permanent'" x-cloak class="flex items-center gap-3">
                                <button @click="save()"
                                    class="flex items-center gap-2 px-5 py-2 bg-amber-500 dark:bg-accent-500 hover:bg-amber-600 dark:hover:bg-accent-600 text-white rounded-xl text-xs font-black transition-all shadow-sm">
                                    <i data-lucide="save" class="w-3.5 h-3.5"></i>
                                    حفظ التوقيت
                                </button>
                                <p x-show="savedMsg" x-cloak class="text-xs text-emerald-600 dark:text-emerald-400 font-black flex items-center gap-1">
                                    <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> تم الحفظ
                                </p>
                            </div>

                            <p x-show="savedMsg && mode === 'permanent'" x-cloak class="text-xs text-emerald-600 dark:text-emerald-400 font-black flex items-center gap-1">
                                <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> تم الحفظ
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

</div>

@push('scripts')
<script>
    function featureRow(id, isEnabled, mode, startsAt, endsAt) {
        return {
            id, isEnabled, mode, startsAt, endsAt,
            showSettings: false,
            savedMsg: false,
            get activeNow() {
                const now = new Date();
                if (this.mode === 'permanent') return this.isEnabled;
                const s = this.startsAt ? new Date(this.startsAt) : null;
                const e = this.endsAt   ? new Date(this.endsAt)   : null;
                const inRange = s && e && now >= s && now <= e;
                if (this.mode === 'scheduled_off') return !inRange;
                if (this.mode === 'scheduled_on')  return  inRange;
                return true;
            },
            toggleEnabled() {
                this.isEnabled = !this.isEnabled;
                this.mode = 'permanent';
                this.save();
            },
            async save() {
                const res = await axios.patch(`{{ url('super-admin/features') }}/${this.id}`, {
                    is_enabled: this.isEnabled,
                    mode:       this.mode,
                    starts_at:  this.startsAt || null,
                    ends_at:    this.endsAt   || null,
                });
                if (res.data.success) {
                    this.savedMsg = true;
                    setTimeout(() => this.savedMsg = false, 2000);
                    lucide.createIcons();
                }
            }
        };
    }
</script>
@endpush
@endsection
