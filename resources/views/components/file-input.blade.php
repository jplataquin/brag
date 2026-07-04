@props([
    'id',
    'name' => '',
    'placeholder' => 'Choose a file...',
    'icon' => 'bi-cloud-arrow-up',
    'color' => 'cyan', // cyan, magenta, lime, warning
    'accept' => '*/*',
    'required' => false,
])

@php
    $borderColor = match($color) {
        'magenta' => 'rgba(255, 0, 255, 0.4)',
        'lime' => 'rgba(57, 255, 20, 0.4)',
        'warning' => 'rgba(255, 221, 0, 0.4)',
        default => 'rgba(0, 240, 255, 0.4)'
    };
    $textColor = match($color) {
        'magenta' => '#ff00ff',
        'lime' => '#39ff14',
        'warning' => '#ffdd00',
        default => '#00f0ff'
    };
    $bgHover = match($color) {
        'magenta' => 'rgba(255, 0, 255, 0.1)',
        'lime' => 'rgba(57, 255, 20, 0.1)',
        'warning' => 'rgba(255, 221, 0, 0.1)',
        default => 'rgba(0, 240, 255, 0.1)'
    };
@endphp

<div class="position-relative neon-file-wrapper" style="overflow: hidden;">
    <input 
        type="file" 
        id="{{ $id }}"
        name="{{ $name ?: $id }}"
        accept="{{ $accept }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'position-absolute w-100 h-100 opacity-0']) }} 
        style="top: 0; left: 0; cursor: pointer; z-index: 10;"
        onchange="document.getElementById('display_{{ $id }}').innerText = this.files[0] ? this.files[0].name : '{{ addslashes($placeholder) }}'; document.getElementById('icon_{{ $id }}').className = this.files[0] ? 'bi bi-file-earmark-check-fill me-2' : 'bi {{ $icon }} me-2';"
        onmouseenter="this.nextElementSibling.style.background = '{{ $bgHover }}'; this.nextElementSibling.style.borderColor = '{{ $textColor }}'; this.nextElementSibling.style.boxShadow = '0 0 15px {{ $borderColor }}'; this.nextElementSibling.style.transform = 'translateY(-2px)';"
        onmouseleave="this.nextElementSibling.style.background = 'rgba(10, 10, 26, 0.5)'; this.nextElementSibling.style.borderColor = '{{ $borderColor }}'; this.nextElementSibling.style.boxShadow = 'none'; this.nextElementSibling.style.transform = 'translateY(0)';"
    >
    <div id="{{ $id }}-dropzone" class="d-flex align-items-center justify-content-between p-3 rounded-3" 
         style="background: rgba(10, 10, 26, 0.5); border: 1px dashed {{ $borderColor }}; transition: all 0.3s ease;">
        <span class="text-truncate text-light opacity-75" style="font-family: 'Orbitron', sans-serif; font-size: 0.85rem; letter-spacing: 1px;">
            <i id="icon_{{ $id }}" class="bi {{ $icon }} me-2" style="color: {{ $textColor }};"></i>
            <span id="display_{{ $id }}">{{ $placeholder }}</span>
        </span>
        <span class="badge border py-2 px-3" style="border-color: {{ $textColor }} !important; color: {{ $textColor }}; background: transparent; font-family: 'Orbitron', sans-serif;">BROWSE</span>
    </div>
</div>
