@props(['label'])

<div {{ $attributes->merge(['class' =>
    'border rounded-xl p-4 bg-white/70 dark:bg-slate-700/40 backdrop-blur-sm'
]) }}>
    <span class="block text-xs font-semibold uppercase text-slate-500 mb-1">
        {{ $label }}
    </span>
    <div class="text-sm">
        {{ $slot }}
    </div>
</div>