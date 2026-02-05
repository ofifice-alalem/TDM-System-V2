@props(['title' => '', 'icon' => ''])

<div {{ $attributes->merge(['class' => 'premium-card p-6 mb-6']) }}>
    @if($title)
        <div class="flex items-center gap-3 mb-6">
            @if($icon)
                <div class="p-2 bg-amber-50 rounded-lg text-amber-500">
                    {!! $icon !!}
                </div>
            @endif
            <h3 class="text-lg font-bold text-gray-900">{{ $title }}</h3>
        </div>
    @endif
    
    <div>
        {{ $slot }}
    </div>
</div>
