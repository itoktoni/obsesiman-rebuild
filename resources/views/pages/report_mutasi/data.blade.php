<table border="0" class="header">
    <tr>
        <td></td>
        <td colspan="6">
            <h3>
                <b>REKAP MUTASI </b>
            </h3>
        </td>
        <td rowspan="3">
            <x-logo />
        </td>
    </tr>
    <tr>
        <td></td>
        <td colspan="10">
            <h3>
                RUMAH SAKIT : {{ $rs->field_name ?? 'Semua Rumah Sakit' }}
            </h3>
        </td>
    </tr>
    <tr>
        <td></td>
        <td colspan="10">
            <h3>
                Periode : {{ formatDate(request()->get('start_date')) }} -
                {{ formatDate(request()->get('end_date')) }}
            </h3>
        </td>
    </tr>
</table>

<div class="table-responsive" id="table_data">
    <table id="export" border="1" style="border-collapse: collapse !important; border-spacing: 0 !important;"
        class="table table-bordered table-striped table-responsive-stack">
        <thead>
            <tr>
                <th rowspan="2" style="width: 10px" width="1">No. </th>
                <th rowspan="2" style="width: 250px" width="20">Nama Linen</th>
                @foreach($tanggal as $tgl)
                <th colspan="4">{{ formatDate($tgl, 'D, d M Y') }}</th>
                @endforeach
            </tr>
            <tr>
                @foreach($tanggal as $tgl)
                <th>Kotor</th>
                <th>Bersih</th>
                <th>-</th>
                <th>+</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php
            $sum_kurang = $sum_lebih = $sum_per_linen = $sum_harga = $sum_berat = $sum_kg = $sum_lawan = 0;
            $total_number = $selisih = 0;
            $total_berat = 0;
            @endphp

            @forelse($linen->sortBy('jenis_nama') as $item)
            @php
            $total_number = $total_number + $loop->iteration;

            $linen_id = $item->jenis_id;
            $nama = $item->jenis_nama;
            $berat = $item->jenis_berat;

            $data_linen = $bersih->where('view_linen_id', $linen_id);

            $qty = $data_linen->sum('view_qty');
            $total = $data_linen->sum('view_total');

            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ strtoupper($nama) }}</td>
                @foreach($tanggal as $tgl)

                <td>
                    @php
                    $date = clone($tgl);
                    $total_tanggal_kotor = $kotor
                    ->where('view_tanggal', $date->addDay(-1)->format('Y-m-d'))
                    ->where('view_linen_id', $linen_id)
                    ->sum('view_qty');
                    @endphp
                    {{ $total_tanggal_kotor > 0 ? $total_tanggal_kotor : '0' }}
                </td>

                <td>
                    @php
                    $total_tanggal_bersih = $bersih
                    ->where('view_tanggal', formatDate($tgl, 'Y-m-d'))
                    ->where('view_linen_id', $linen_id)
                    ->sum('view_qty');
                    @endphp
                    {{ $total_tanggal_bersih > 0 ? $total_tanggal_bersih : '0' }}
                </td>

                @php
                $kurang = $lebih = null;

                $sisa = $total_tanggal_bersih - $total_tanggal_kotor;

                if($sisa < 0){
                    $kurang = $sisa;
                } else{
                    $lebih = $sisa;
                }

                @endphp

                <td>{{ $kurang }}</td>
                <td>{{ $lebih }}</td>
                @endforeach
            </tr>
            @empty
            @endforelse
        </tbody>

    </table>
</div>

<table class="footer">
    <tr>
        <td colspan="2" class="print-date">{{ env('APP_LOCATION') }}, {{ date('d F Y') }}</td>
    </tr>
    <tr>
        <td colspan="2" class="print-person">{{ auth()->user()->name ?? '' }}</td>
    </tr>
</table>