@props(['perusahaan' => null, 'size' => 'normal'])

@php
    $p = $perusahaan;
    $name = $p?->nama_perusahaan ?? '-';
    $primaryColor = $p?->primary_color ?? '#0b2f57';
    $secondaryColor = $p?->secondary_color ?? '#154b87';
    $isSmall = $size === 'small';
@endphp

@if($p)
    <span class="badge company-theme-badge"
          style="background-color: {{ $primaryColor }}15; color: {{ $primaryColor }}; border: 1px solid {{ $primaryColor }}40; {{ $isSmall ? 'font-size: 0.7rem; padding: 0.2rem 0.45rem;' : 'font-size: 0.76rem; padding: 0.32rem 0.65rem;' }}"
          title="{{ $name }}">
        <span style="width: {{ $isSmall ? '5px' : '7px' }}; height: {{ $isSmall ? '5px' : '7px' }}; border-radius: 50%; background-color: {{ $primaryColor }}; display: inline-block; box-shadow: 0 0 4px {{ $primaryColor }}80;"></span>
        <span class="text-truncate" style="max-width: 220px;">{{ strtoupper($name) }}</span>
    </span>
@else
    <span class="badge bg-label-secondary" style="font-size: 0.75rem;">-</span>
@endif
