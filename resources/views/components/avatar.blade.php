@props(['user'])
@php
    // Iniciais e uma cor estável por pessoa (derivada do id), para reconhecer quem é quem de relance.
    $initials = collect(explode(' ', $user->name))->filter()->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->implode('');
    $hue = ($user->id * 67) % 360;
@endphp
<span {{ $attributes->merge(['class' => 'avatar']) }} style="--hue: {{ $hue }}" title="{{ $user->name }}" aria-hidden="true">{{ mb_strtoupper($initials) }}</span>
