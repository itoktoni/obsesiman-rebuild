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
                @foreach($location as $loc_name => $loc)
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
                $sum_kurang = $sum_lebih = $sum_per_linen = $sum_kotor = $sum_beda_rs = $sum_kg = $sum_lawan = $selisih = 0;
            @endphp
            @forelse($linen as $name => $table)
                @php
                    $total_per_linen = $table->count();
                    $total_beda_rs = $table->where('transaksi_beda_rs', 1)->count();
                    $total_beda_plus_linen = $total_per_linen + $total_beda_rs;

                    $total_lawan = isset($lawan[$name]) ? $lawan[$name]->count() : 0;
                    $total_kg = $table[0]->view_linen_berat * $total_lawan;

                    $sum_beda_rs = $sum_beda_rs + $total_beda_rs;
                    $sum_per_linen = $sum_per_linen + $total_beda_plus_linen;
                    $sum_kg = $sum_kg + $total_kg;
                    $sum_lawan = $sum_lawan + $total_lawan;

                    $selisih = $total_lawan - $total_per_linen;
					$selisih_kurang = $selisih < 0 ? $selisih : 0; $selisih_lebih=$selisih> 0 ? $selisih : 0;
					$sum_kurang = $sum_kurang + $selisih_kurang;
					$sum_lebih = $sum_lebih + $selisih_lebih;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $name }}</td>
                    @foreach($location as $loc_name => $loc)
                        <td>
                            @php
                                $total_lokasi = $table->where('view_ruangan_nama', $loc_name)->count();
                            @endphp
                            {{ $total_lokasi > 0 ? $total_lokasi : '' }}
                        </td>
                    @endforeach
                    <td>{{ $total_beda_rs }}</td>
                    <td>{{ $total_beda_plus_linen }}</td>
                    <td>{{ $total_kg }}</td>
                    <td>
                        {{ $total_lawan }}
                    </td>
                    <td>{{ $selisih < 0 ? $selisih : '' }}</td>
                    <td>{{ $selisih > 0 ? $selisih : '' }}</td>
                </tr>
			@empty
			@endforelse
		</tbody>
		<tr>
            <td colspan="2">Total</td>
            @foreach($location as $loc_name => $loc)
                <td>
                    {{ $loc->count() }}
                </td>
            @endforeach
            <td>
                {{ $sum_beda_rs }}
            </td>
            <td>
                {{ $sum_per_linen }}
            </td>
            <td>
                {{ $sum_kg }}
            </td>
            <td>
                {{ $sum_lawan }}
            </td>
            <td>
                {{ $sum_kurang }}
            </td>
            <td>
                {{ $sum_lebih }}
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