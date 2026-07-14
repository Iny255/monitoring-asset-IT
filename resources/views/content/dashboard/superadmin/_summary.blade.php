<div class="row g-3 mb-4">

@php

$cards = [

[
'title'=>'Perusahaan',
'total'=>$dashboard['executive']['perusahaan'],
'icon'=>'bx-buildings',
'color'=>'primary'
],

[
'title'=>'Inventaris',
'total'=>$dashboard['executive']['inventaris'],
'icon'=>'bx-package',
'color'=>'success'
],

[
'title'=>'User',
'total'=>$dashboard['executive']['user'],
'icon'=>'bx-user',
'color'=>'info'
],

[
'title'=>'Mapping',
'total'=>$dashboard['executive']['mapping'],
'icon'=>'bx-git-branch',
'color'=>'warning'
],

[
'title'=>'Maintenance',
'total'=>$dashboard['executive']['maintenance'],
'icon'=>'bx-wrench',
'color'=>'danger'
],

[
'title'=>'Peminjaman',
'total'=>$dashboard['executive']['peminjaman'],
'icon'=>'bx-transfer',
'color'=>'secondary'
],

];

@endphp

@foreach($cards as $card)

<div class="col-xl-2 col-md-4">

<div class="card h-100 border-0 shadow-sm">

<div class="card-body text-center">

<div class="dashboard-icon bg-{{ $card['color'] }} mx-auto mb-3">

<i class="bx {{ $card['icon'] }}"></i>

</div>

<h3 class="fw-bold">

{{ number_format($card['total']) }}

</h3>

<small class="text-muted">

{{ $card['title'] }}

</small>

</div>

</div>

</div>

@endforeach

</div>