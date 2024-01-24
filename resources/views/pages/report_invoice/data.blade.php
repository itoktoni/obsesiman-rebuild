<table border="0" class="header">
	<tr>
		<td></td>
		<td colspan="6">
			<h3>
				<b>REKAP INVOICE {{ strtoupper(CuciType::getDescription((int)request()->get('view_status'))) }} </b>
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
				Periode : {{ formatDate(request()->get('start_rekap')) }} - {{ formatDate(request()->get('end_rekap')) }}
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
                @foreach($tanggal as $tgl => $item)
                    <th>{{ formatDate($tgl, 'd') }}</th>
                @endforeach
                <th>QTY</th>
                <th>Berat (KG)</th>
                <th>Total (Kg)</th>
                <th>Harga</th>
                <th>Total Invoice</th>
            </tr>
        </thead>
		<tbody>
            @php
                $sum_kurang = $sum_lebih = $sum_per_linen = $sum_harga = $sum_berat = $sum_kg = $sum_lawan = 0;
                $total_number = $selisih = 0;
                $total_berat = 0;
            @endphp
            @forelse($linen as $linen_id => $nama)
                @php
                    $total_number = $total_number + $loop->iteration;
                    $data_linen = $data
                        ->where('view_linen_id', $linen_id);

                    $single_linen = $data_linen->first();
                    $berat = $single_linen->jenis_berat ?? 0;

                    $total_per_linen = $data_linen->sum('view_qty');
                    $total_berat = $total_per_linen * $berat;
                    $sum_berat = $sum_berat + $total_berat;

                    $total_per_linen_kanan = $total_per_linen ?? 0;

                    $sum_per_linen = $sum_per_linen + $total_per_linen_kanan;

                    $harga = 0;
                    if ($single_linen) {
                        $harga = $single_linen->view_status == CuciType::Cuci ? $single_linen->view_harga_cuci : $single_linen->view_harga_sewa;
                    }

                    $total_harga = $total_berat * $harga;
                    $sum_harga = $sum_harga + $total_harga;

                    $total_lawan = 0;

                    $sum_lawan = $sum_lawan + $total_lawan;

                    $total_kg = $data[0]->view_linen_berat * $total_per_linen;
                    $sum_kg = $sum_kg + $total_kg;

                    $selisih = $total_per_linen - $total_lawan;
					$selisih_kurang = $selisih < 0 ? $selisih : 0;
                    $selisih_lebih = $selisih> 0 ? $selisih : 0;
					$sum_kurang = $sum_kurang + $selisih_kurang;
					$sum_lebih = $sum_lebih + $selisih_lebih;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ strtoupper($nama) }}</td>
                    @foreach($tanggal as $tgl => $item)
                        <td>
                            @php
                            $total_tanggal = $item
                                ->where('view_tanggal', $tgl)
                                ->where('view_linen_id', $linen_id)
                                ->sum('view_qty');
                            @endphp
                            {{ $total_tanggal > 0 ? $total_tanggal : '' }}
                        </td>
                        @endforeach
                        <td>{{ $total_per_linen }}</td>
                        <td class="text-right">{{ $berat }}</td>
                        <td class="text-right">{{ $total_berat }}</td>
                        <td class="text-right">{{ number_format($harga) }}</td>
                        <td class="text-right">{{ number_format($total_harga) }}</td>
                </tr>
			@empty
			@endforelse
		</tbody>
		<tr>
            <td colspan="2">Total</td>
            @foreach($tanggal as $tgl => $item)
            @php
            $sum_lokasi = $data->where('view_tanggal', $tgl)->sum('view_qty');
            @endphp
                <td>
                    {{ $sum_lokasi }}
                </td>
            @endforeach
            <td>
                {{ $sum_per_linen }}
            </td>
            <td colspan="2" class="text-right">
                {{ $sum_berat  }}
            </td>
            <td colspan="2" class="text-right">
                {{ number_format($sum_harga) }}
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