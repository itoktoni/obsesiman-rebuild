<table border="0" class="header">
	<tr>
		<td></td>
		<td colspan="6">
			<h3>
				<b>REKAP LINEN BARU</b>
			</h3>
		</td>
		<td rowspan="3">
			<x-logo/>
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
                @php
                $start_tanggal_kotor = \Carbon\Carbon::parse(request()->get('start_rekap'))->addDay(-1);
                $end_tanggal_kotor = \Carbon\Carbon::parse(request()->get('end_rekap'))->addDay(-1);
                @endphp

                Tanggal Kotor : {{ formatDate($start_tanggal_kotor) }} - {{ formatDate($end_tanggal_kotor) }}
				<br>
				Tanggal Bersih : {{ formatDate(request()->get('start_rekap')) }} - {{ formatDate(request()->get('end_rekap')) }}
			</h3>
		</td>
	</tr>
</table>

<div class="table-responsive" id="table_data">
	<table id="export" border="1" style="border-collapse: collapse !important; border-spacing: 0 !important;"
		class="table table-bordered table-striped table-responsive-stack">
		<thead>
            <tr>
                <th style="width: 10px" width="1">No. </th>
                <th style="width: 200px" width="20">Nama Linen</th>
                @foreach($location as $loc_id => $loc_name)
                    <th>{{ $loc_name }}</th>
                @endforeach
                <th>Total Linen Baru (Pcs)</th>
            </tr>
        </thead>
		<tbody>
            @php
                $sum_kurang = $sum_lebih = $sum_per_linen = $sum_kotor = $sum_beda_rs = $sum_kg = $sum_lawan = 0;
                $total_number = $selisih = 0;
                $total_lawan = 0;
            @endphp
            @forelse($linen as $linen_id => $name)
                @if(!empty($name))
                @php
                    $total_number++;
                    $total_per_linen = $bersih
                        ->where('view_linen_id', $linen_id)
                        ->where(Transaksi::field_status_bersih(), TransactionType::Register)
                        ->count();

                    $total_per_linen_kanan = $total_per_linen ?? 0;

                    $sum_per_linen = $sum_per_linen + $total_per_linen_kanan;
                @endphp
                <tr>
                    <td>{{ $total_number }}</td>
                    <td>{{ Str::ucfirst($name) }}</td>
                    @foreach($location as $loc_id => $loc_name)
                        <td>
                            @php
                            $total_lokasi = $bersih
                                ->where('view_linen_id', $linen_id)
                                ->where('view_ruangan_id', $loc_id)
                                ->where(Transaksi::field_status_bersih(), TransactionType::Register)
                                ->count();
                            @endphp
                            {{ $total_lokasi > 0 ? $total_lokasi : '' }}
                        </td>
                    @endforeach
                    <td>{{ $total_per_linen_kanan }}</td>
                </tr>
                @endif
			@empty
			@endforelse
		</tbody>
		<tr>
            <td colspan="2">Total</td>
            @foreach($location as $loc_id => $loc_name)
            @php
            $sum_lokasi = $bersih->where('view_ruangan_id', $loc_id)->count();
            @endphp
                <td>
                    {{ $sum_lokasi }}
                </td>
            @endforeach
            <td>
                {{ $sum_per_linen }}
            </td>
        </tr>
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