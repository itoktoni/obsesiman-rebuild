<table border="0" class="header">
	<tr>
		<td></td>
		<td colspan="6">
			<h3>
				<b>REKAP BERSIH </b>
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
                @foreach($location as $loc)
                    <th>{{ $loc->field_name }}</th>
                @endforeach
                <th>Total Bersih (Pcs)</th>
                <th>(Kg) Bersih</th>
                <th>Total Kotor (Pcs)</th>
            </tr>
        </thead>
		<tbody>
            @php
                $sum_kurang = $sum_lebih = $sum_per_linen = $sum_kotor = $sum_beda_rs = $sum_kg = $sum_lawan = 0;
                $total_number = $selisih = 0;
                $total_lawan = 0;
            @endphp
            @forelse($linen->sortBy('jenis_nama') as $jenis)
                @php
                $name = $jenis->jenis_nama;
                $total_number++;
                @endphp
                <tr>
                    <td>{{ $total_number }}</td>
                    <td>{{ $name }}</td>
                    @foreach($location as $loc)
                        <td>
                            @php
                            $total_ruangan = $bersih
                            ->where('view_ruangan_id', $loc->ruangan_id)
                            ->where('view_linen_id', $jenis->jenis_id)
                            ->sum('view_qty');
                            @endphp
                            {{ $total_ruangan > 0 ? $total_ruangan : '0' }}
                        </td>
                    @endforeach
                    <td>
                        @php
                        $total_bersih = $bersih
                        ->where('view_linen_id', $jenis->jenis_id)
                        ->sum('view_qty');
                        @endphp
                        {{ $total_bersih > 0 ? $total_bersih : '0' }}
                    </td>
                    <td>
                        @php
                        $total_kg = $bersih
                        ->where('view_linen_id', $jenis->jenis_id)
                        ->sum('view_kg');
                        @endphp
                        {{ $total_kg > 0 ? $total_kg : '0' }}
                    </td>
                    <td>
                        @php
                        $total_kotor = $kotor
                        ->where('view_linen_id', $jenis->jenis_id)
                        ->sum('view_qty_total');
                        @endphp
                        {{ $total_kotor > 0 ? $total_kotor : '0' }}
                    </td>
                </tr>
			@empty
			@endforelse

		</tbody>
	</table>
</div>