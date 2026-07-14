@php

$items = [

[
'title'=>'Penerimaan',
'icon'=>'bx bx-download',
'color'=>'success',
'total'=>$dashboard['transaksi']['penerimaan']['total'],
'info'=>$dashboard['transaksi']['penerimaan']['bulan_ini'],
'label'=>'Bulan ini'
],

[
'title'=>'Pemakaian',
'icon'=>'bx bx-desktop',
'color'=>'primary',
'total'=>$dashboard['transaksi']['pemakaian']['total'],
'info'=>$dashboard['transaksi']['pemakaian']['bulan_ini'],
'label'=>'Bulan ini'
],

[
'title'=>'Mutasi',
'icon'=>'bx bx-transfer',
'color'=>'warning',
'total'=>$dashboard['transaksi']['mutasi']['total'],
'info'=>$dashboard['transaksi']['mutasi']['bulan_ini'],
'label'=>'Bulan ini'
],

[
'title'=>'Maintenance',
'icon'=>'bx bx-wrench',
'color'=>'danger',
'total'=>$dashboard['transaksi']['maintenance']['total'],
'info'=>$dashboard['transaksi']['maintenance']['diproses'],
'label'=>'Diproses'
],

[
'title'=>'Peminjaman',
'icon'=>'bx bx-package',
'color'=>'info',
'total'=>$dashboard['transaksi']['peminjaman']['total'],
'info'=>$dashboard['transaksi']['peminjaman']['dipinjam'],
'label'=>'Dipinjam'
],

];

@endphp

<div class="card border-0 shadow-sm h-100">

    <div class="card-header border-0">

        <h5 class="fw-bold mb-1">

            <i class="bx bx-pulse me-2"></i>

            Ringkasan Transaksi

        </h5>

        <small class="text-muted">

            Kondisi transaksi seluruh perusahaan

        </small>

    </div>

    <div class="card-body">

        @foreach($items as $item)

        @php

        $persen = $item['total'] > 0
                    ? round(($item['info']/$item['total'])*100)
                    : 0;

        @endphp

        <div class="mb-4">

            <div class="d-flex justify-content-between align-items-center">

                <div>

                    <span class="badge bg-label-{{ $item['color'] }} me-2">

                        <i class="{{ $item['icon'] }}"></i>

                    </span>

                    <strong>{{ $item['title'] }}</strong>

                </div>

                <span class="fw-bold fs-5">

                    {{ $item['total'] }}

                </span>

            </div>

            <div class="d-flex justify-content-between mt-2">

                <small class="text-muted">

                    {{ $item['label'] }}

                </small>

                <small class="fw-semibold">

                    {{ $item['info'] }}

                </small>

            </div>

            <div class="progress mt-2" style="height:6px;">

                <div class="progress-bar bg-{{ $item['color'] }}"

                     style="width:{{ $persen }}%">

                </div>

            </div>

        </div>

        @endforeach

    </div>

</div>