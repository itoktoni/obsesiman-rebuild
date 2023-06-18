<table border="0" class="header">
	<tr>
		<td></td>
		<td colspan="6">
			<h3>
				<b>REKAP KOTOR </b>
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
				Periode : {{ formatDate(request()->get('start_date')) }} - {{ formatDate(request()->get('end_date')) }}
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
                <th>Beda RS</th>
                <th>Total Kotor (Pcs)</th>
                <th>(Kg) Bersih</th>
                <th>Total Bersih (Pcs)</th>
                <th>-</th>
                <th>+</th>
            </tr>
        </thead>
		<tbody>
            @php
                $sum_kurang = $sum_lebih = $sum_per_linen = $sum_kotor = $sum_beda_rs = $sum_kg = $sum_lawan = 0;
                $total_number = $selisih = 0;
                $total_lawan = 0;
                $total_beda_rs = $kotor->where(Transaksi::field_beda_rs(), 1)
                    ->whereIn(Transaksi::field_status_transaction(), KOTOR)
                    ->count();
            @endphp
            @forelse($linen as $linen_id => $name)
                @php
                    $total_number = $total_number +  $loop->iteration ;
                    $total_per_linen = $kotor
                        ->where(Transaksi::field_beda_rs(), 0)
                        ->whereIn(Transaksi::field_status_transaction(), KOTOR)
                        ->where('view_linen_id', $linen_id)
                        ->count();

                    $sum_per_linen = $sum_per_linen + $total_per_linen;

                    $total_lawan = $bersih
                        ->whereIn(Transaksi::field_status_bersih(), BERSIH)
                        ->where('view_linen_id', $linen_id)
                        ->count();

                    $sum_lawan = $sum_lawan + $total_lawan;

                    $berat = $bersih
                        ->whereIn(Transaksi::field_status_bersih(), BERSIH)
                        ->where('view_linen_id', $linen_id)
                        ->first()->view_linen_berat ?? 0;

                    $total_kg = $berat * $total_lawan;
                    $sum_kg = $sum_kg + $total_kg;

                    $selisih = $total_lawan - $total_per_linen;
					$selisih_kurang = $selisih < 0 ? $selisih : 0;
                    $selisih_lebih=$selisih> 0 ? $selisih : 0;
					$sum_kurang = $sum_kurang + $selisih_kurang;
					$sum_lebih = $sum_lebih + $selisih_lebih;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $name }}</td>
                    @foreach($location as $loc_id => $loc_name)
                        <td>
                            @php
                                $total_lokasi = $kotor->where(Transaksi::field_beda_rs(), 0)
                                    ->where('view_ruangan_id', $loc_id)
                                    ->where('view_linen_id', $linen_id)
                                    ->count();
                            @endphp
                            {{ $total_lokasi > 0 ? $total_lokasi : '' }}
                        </td>
                    @endforeach
                    <td><!-- tempat beda rs --></td>
                    <td>{{ $total_per_linen }}</td>
                    <td>{{ $total_kg }}</td>
                    <td>
                        {{ $total_lawan }}
                    </td>
                    <td>{{ $selisih < 0 ? $selisih : '' }}</td>
                    <td>{{ $selisih > 0 ? $selisih : '' }}</td>
                </tr>
			@empty
			@endforelse
            <tr>
                <td>{{ $total_number }}</td>
                <td>Linen Lain</td>
                @foreach($location as $loc)
                <td></td>
                @endforeach
                <td>{{ $total_beda_rs }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>{{ $total_beda_rs }}</td>
            </tr>
		</tbody>
		<tr>
            <td colspan="2">Total</td>
            @foreach($location as $loc_id => $loc_name)
                @php
                $sum_lokasi = $kotor->where(Transaksi::field_beda_rs(), 0)
                                ->where('view_ruangan_id', $loc_id)
                                ->count();
                @endphp
                <td>
                    {{ $sum_lokasi }}
                </td>
            @endforeach
            <td>
                {{ $total_beda_rs }}
            </td>
            <td>
                {{ $sum_per_linen }}
            </td>
            <td>
                {{ $sum_kg }}
            </td>
            <td>
                {{ $sum_lawan  }}
            </td>
            <td>
                {{ $sum_kurang }}
            </td>
            <td>
                {{ $sum_lebih + $total_beda_rs }}
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