<table border="0" class="header">
    <tr>
        <td></td>
        <td colspan="6">
            <h3>
                <b>REPORT OPNAME SUMMARY : {{ $opname->field_primary ?? '' }} </b>
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
                RUMAH SAKIT : {{ $opname->has_rs->field_name ?? 'Semua Rumah Sakit' }}
            </h3>
        </td>
    </tr>
    <tr>
        <td></td>
        <td colspan="10">
            <h3>
                Periode : {{ formatDate($opname->field_start) }} - {{ formatDate($opname->field_end) }}
            </h3>
        </td>
    </tr>
</table>

<div class="table-responsive" id="table_data">
    <table id="export" border="1" style="border-collapse: collapse !important; border-spacing: 0 !important;"
        class="table table-bordered table-striped table-responsive-stack">
        <thead>
            <tr>
                <th width="1">No. </th>
                <th>TANGGAL </th>
                <th>REGISTER</th>
                <th>SCAN RS</th>
                <th>KOTOR</th>
                <th>BERSIH</th>
                <th>PENDING</th>
                <th>HILANG</th>
                <th>RETUR</th>
                <th>REWASH</th>
                <th>TOTAL</th>
                <th>SELISIH</th>
            </tr>
        </thead>
        <tbody>
			@php
			$map = [];
			if(!empty($data)){
				$map = $data->mapToGroups(function($item){
					return [formatDate($item->opname_detail_updated_at) => $item];
				});
			}
			@endphp
            @forelse($map as $key => $table)
			@php
			$kotor = $table->where('opname_detail_transaksi', TransactionType::Kotor)->count();
			$bersih = $table->whereIn('opname_detail_transaksi', BERSIH)->count();
			$pending = $table->whereIn('opname_detail_process', ProcessType::Pending)->count();
			$hilang = $table->whereIn('opname_detail_process', ProcessType::Hilang)->count();
			$retur = $table->whereIn('opname_detail_transaksi', TransactionType::Retur)->count();
			$rewash = $table->whereIn('opname_detail_transaksi', TransactionType::Rewash)->count();
			$total = $kotor + $bersih + $pending + $hilang + $retur + $rewash;
			$selisih = $register - $total;
			@endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $key ?? '' }}</td>
                <td>{{ $register }}</td>
                <td>-</td>
                <td>{{ $kotor }}</td>
                <td>{{ $bersih }}</td>
                <td>{{ $pending }}</td>
                <td>{{ $hilang }}</td>
                <td>{{ $retur }}</td>
                <td>{{ $rewash }}</td>
                <td>{{ $total }}</td>
                <td>{{ -$selisih }}</td>
            </tr>

            @empty
            @endforelse

			<tr>
				<td colspan="2">Total</td>
				@php
				$sub_kotor = $data->where('opname_detail_transaksi', TransactionType::Kotor)->count();
				$sub_bersih = $data->whereIn('opname_detail_transaksi', BERSIH)->count();
				$sub_pending = $data->whereIn('opname_detail_process', ProcessType::Pending)->count();
				$sub_hilang = $data->whereIn('opname_detail_process', ProcessType::Hilang)->count();
				$sub_retur = $data->whereIn('opname_detail_transaksi', TransactionType::Retur)->count();
				$sub_rewash = $data->whereIn('opname_detail_transaksi', TransactionType::Rewash)->count();
				$sub_total = $data->count();
				@endphp
				<td>{{ $register }}</td>
				<td>-</td>
				<td>{{ $sub_kotor }}</td>
				<td>{{ $sub_bersih }}</td>
				<td>{{ $sub_pending }}</td>
				<td>{{ $sub_hilang }}</td>
				<td>{{ $sub_retur }}</td>
				<td>{{ $sub_rewash }}</td>
				<td>{{ $sub_total }}</td>
				<td>{{ -$sub_total }}</td>
			</tr>

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