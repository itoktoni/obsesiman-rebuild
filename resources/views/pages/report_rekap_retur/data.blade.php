<table border="0" class="header">
	<tr>
		<td></td>
		<td colspan="6">
			<h3>
				<b>REKAP RETUR</b>
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
                @foreach($location as $loc_id => $loc_name)
                    @if(!empty($loc_name))
                    <th>{{ $loc_name }}</th>
                    @endif
                @endforeach
                <th>Belum teregister</th>
                <th>Beda RS</th>
                <th>Total Retur (Pcs)</th>
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
                $total_beda_rs = $kotor->where(Transaksi::field_beda_rs(), BedaRsType::Beda)
                    ->whereIn(Transaksi::field_status_transaction(), TransactionType::Retur)
                    ->count();
                $total_belum_register = $kotor->where(Transaksi::field_beda_rs(), BedaRsType::BelumRegister)
                    ->whereIn(Transaksi::field_status_transaction(), TransactionType::Retur)
                    ->count();
            @endphp
            @forelse($linen as $linen_id => $name)
                @if(!empty($name))
                @php
                    $total_number++;
                    $total_per_linen = $kotor
                        ->where(Transaksi::field_beda_rs(), 0)
                        ->whereIn(Transaksi::field_status_transaction(), TransactionType::Retur)
                        ->where('view_linen_id', $linen_id)
                        ->count();

                    $sum_per_linen = $sum_per_linen + $total_per_linen;

                    $total_lawan = $bersih
                        ->whereIn(Transaksi::field_status_bersih(), TransactionType::BersihRetur)
                        ->where('view_linen_id', $linen_id)
                        ->count();

                    $sum_lawan = $sum_lawan + $total_lawan;

                    $selisih = $total_lawan - $total_per_linen;
					$selisih_kurang = $selisih < 0 ? $selisih : 0;
                    $selisih_lebih=$selisih> 0 ? $selisih : 0;
					$sum_kurang = $sum_kurang + $selisih_kurang;
					$sum_lebih = $sum_lebih + $selisih_lebih;
                @endphp
                <tr>
                    <td>{{ $total_number}}</td>
                    <td>{{ $name ?? 'Belum teregister' }}</td>
                    @foreach($location as $loc_id => $loc_name)
                    @if(!empty($loc_name))
                        <td>
                            @php
                                $total_lokasi = $kotor->where(Transaksi::field_beda_rs(), 0)
                                    ->where('view_ruangan_id', $loc_id)
                                    ->where('view_linen_id', $linen_id)
                                    ->count();
                            @endphp
                            {{ $total_lokasi > 0 ? $total_lokasi : '' }}
                        </td>
                    @endif
                    @endforeach
                    <td><!-- tempat belum register --></td>
                    <td><!-- tempat beda rs --></td>
                    <td>{{ $total_per_linen }}</td>
                    <td>
                        {{ $total_lawan }}
                    </td>
                    <td>{{ $selisih < 0 ? $selisih : '' }}</td>
                    <td>{{ $selisih > 0 ? $selisih : '' }}</td>
                </tr>
                @endif
			@empty
			@endforelse
            <tr>
                <td>{{ $total_number + 1 }}</td>
                <td>Belum teregister</td>
                @foreach($location as $loc)
                @if(!empty($loc))
                <td></td>
                @endif
                @endforeach
                <td>{{ $total_belum_register }}</td>
                <td></td>
                <td>{{ $total_belum_register }}</td>
                <td></td>
                <td></td>
                <td>-{{ $total_belum_register }}</td>
                <td></td>
            </tr>
            <tr>
                <td>{{ $total_number + 2 }}</td>
                <td>Linen Lain</td>
                @foreach($location as $loc)
                @if(!empty($loc))
                <td></td>
                @endif
                @endforeach
                <td></td>
                <td>{{ $total_beda_rs }}</td>
                <td>{{ $total_beda_rs }}</td>
                <td></td>
                <td></td>
                <td>-{{ $total_beda_rs }}</td>
                <td></td>
            </tr>
		</tbody>
		<tr>
            <td colspan="2">Total</td>
            @foreach($location as $loc_id => $loc_name)
                @if(!empty($loc_name))
                @php
                $sum_lokasi = $kotor->where(Transaksi::field_beda_rs(), 0)
                                ->where('view_ruangan_id', $loc_id)
                                ->count();
                @endphp
                <td>
                    {{ $sum_lokasi }}
                </td>
                @endif
            @endforeach
            <td>
                {{ $total_belum_register }}
            </td>
            <td>
                {{ $total_beda_rs }}
            </td>
            <td>
                {{ $sum_per_linen + $total_belum_register + $total_beda_rs }}
            </td>
            <td>
                {{ $sum_lawan  }}
            </td>
            <td>
                {{ $sum_kurang + -($total_beda_rs) + -($total_belum_register) }}
            </td>
            <td>
                {{ $sum_lebih  }}
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