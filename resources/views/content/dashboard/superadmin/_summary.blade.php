<div class="row g-3 mb-4">

@php

$cards = [

[
'title'=>'Perusahaan',
'total'=>$dashboard['executive']['perusahaan'],
'icon'=>'bx-buildings',
'desc'=>'Total Perusahaan'
],

[
'title'=>'Inventaris',
'total'=>$dashboard['executive']['inventaris'],
'icon'=>'bx-package',
'desc'=>'Total Inventaris'
],

[
'title'=>'User',
'total'=>$dashboard['executive']['user'],
'icon'=>'bx-user',
'desc'=>'Total Pengguna'
],

[
'title'=>'Mapping',
'total'=>$dashboard['executive']['mapping'],
'icon'=>'bx-git-branch',
'desc'=>'Total Mapping'
],

[
'title'=>'Maintenance',
'total'=>$dashboard['executive']['maintenance'],
'icon'=>'bx-wrench',
'desc'=>'Total Maintenance'
],

[
'title'=>'Peminjaman',
'total'=>$dashboard['executive']['peminjaman'],
'icon'=>'bx-transfer',
'desc'=>'Total Peminjaman'
],

];

@endphp

@foreach($cards as $card)

<div class="col-xl-2 col-md-4 col-sm-6">

<div class="card dashboard-card h-100 border-0 shadow-sm category-theme-card">

<div class="card-body text-center p-3">

<div class="dashboard-icon category-theme-icon mx-auto mb-3">

<i class="bx {{ $card['icon'] }}"></i>

</div>

<h3 class="dashboard-number fw-bold mb-1" style="color: var(--primary-theme, #0b2f57);">

{{ number_format($card['total']) }}

</h3>

<span class="dashboard-label d-block text-dark fw-semibold" style="font-size: 0.85rem;">

{{ $card['title'] }}

</span>

<small class="text-muted" style="font-size: 0.75rem;">

{{ $card['desc'] }}

</small>

</div>

</div>

</div>

@endforeach

</div>