<table>
    <thead>
        {{-- TITLE --}}
        <tr>
            <th colspan="49" style="font-size: 14pt; font-weight: bold; text-align: center; height: 35px; vertical-align: middle;">
                CHECKLIST PERAWATAN DEVICE
            </th>
        </tr>

        {{-- ROW IDENTITAS 1 --}}
        <tr>
            <th style="font-weight: bold; width: 350px;">
                Kategori Device : {{ $isLaptop ? '[√] Laptop' : '[ ] Laptop' }}
            </th>
            <th colspan="16" style="font-weight: bold;">
                Nama Device : {{ $namaDevice }} ({{ $kodeAset }})
            </th>
            <th colspan="16" style="font-weight: bold;">
                Nama Pengguna : {{ $namaPengguna }}
            </th>
            <th colspan="16" style="font-weight: bold;">
                Divisi : {{ $divisi }}
            </th>
        </tr>

        {{-- ROW IDENTITAS 2 --}}
        <tr>
            <th style="font-weight: bold;">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $isPrinter ? '[√] Printer' : '[ ] Printer' }}
            </th>
            <th colspan="48" style="font-weight: bold; text-align: center; background-color: #f1f5f9;">
                WAKTU PERAWATAN (TAHUN {{ $tahun }})
            </th>
        </tr>

        {{-- ROW IDENTITAS 3 (BULAN) --}}
        <tr>
            <th style="font-weight: bold;">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $isHpTablet ? '[√] HP/Tablet' : '[ ] HP/Tablet' }} {{ $kategoriLain ? '[√] ' . $kategoriLain : '' }}
            </th>
            @for ($b = 1; $b <= 12; $b++)
                <th colspan="4" style="font-weight: bold; text-align: center; background-color: #e2e8f0;">
                    {{ $bulanNames[$b] }}
                </th>
            @endfor
        </tr>

        {{-- ROW SUB-HEADER MINGGU --}}
        <tr>
            <th style="font-weight: bold; background-color: #e2e8f0;">
                JENIS PERAWATAN:
            </th>
            @for ($b = 1; $b <= 12; $b++)
                <th style="font-weight: bold; text-align: center; width: 30px; background-color: #f1f5f9;">1</th>
                <th style="font-weight: bold; text-align: center; width: 30px; background-color: #f1f5f9;">2</th>
                <th style="font-weight: bold; text-align: center; width: 30px; background-color: #f1f5f9;">3</th>
                <th style="font-weight: bold; text-align: center; width: 30px; background-color: #f1f5f9;">4</th>
            @endfor
        </tr>
    </thead>
    <tbody>
        @php
            $displayRows = max(10, count($jenisPerawatanList));
        @endphp
        @for ($i = 0; $i < $displayRows; $i++)
            @php
                $itemText = $jenisPerawatanList[$i] ?? null;
            @endphp
            <tr>
                <td style="height: 25px; vertical-align: middle;">
                    @if ($itemText)
                        {{ $i + 1 }}. {{ $itemText }}
                    @endif
                </td>
                @for ($b = 1; $b <= 12; $b++)
                    @for ($m = 1; $m <= 4; $m++)
                        <td style="text-align: center; vertical-align: middle;">
                            @if ($itemText && !$isBlank && !empty($matrix[$itemText][$b][$m]))
                                {{ $matrix[$itemText][$b][$m] === '✔' ? 'v' : ($matrix[$itemText][$b][$m] === '✖' ? 'x' : $matrix[$itemText][$b][$m]) }}
                            @endif
                        </td>
                    @endfor
                @endfor
            </tr>
        @endfor
        <tr>
            <td colspan="48"></td>
            <td style="font-style: italic; font-weight: bold; text-align: right; height: 30px; vertical-align: bottom;">
                F-IT-001/00
            </td>
        </tr>
    </tbody>
</table>
