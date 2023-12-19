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
                <th>HILANG RS</th>
                <th>SCAN RS</th>
                <th>KOTOR</th>
                <th>PENDING</th>
                <th>HILANG</th>
                <th>RETUR</th>
                <th>REWASH</th>
                <th>BELUM REGISTER</th>
                <th>TOTAL</th>
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

            $grand_total
			@endphp
            @forelse($map as $key => $table)
			@php
			$kotor = $table->where('opname_detail_transaksi', TransactionType::Kotor)
                        ->whereNotIn('opname_detail_proses', [ProcessType::Pending, ProcessType::Hilang])
                        ->count();

			$hilang_rs = $table->where('opname_detail_ketemu', BooleanType::No)->count();

			$scan_rs = $table->whereIn('opname_detail_transaksi', BERSIH)
                        ->where('opname_detail_ketemu', BooleanType::Yes)
                        ->count();

			$pending = $table->where('opname_detail_proses', ProcessType::Pending)
                        ->count();

			$hilang = $table->where('opname_detail_proses', ProcessType::Hilang)
                        ->count();

			$retur = $table->where('opname_detail_transaksi', TransactionType::Retur)
                        ->whereNotIn('opname_detail_proses', [ProcessType::Pending, ProcessType::Hilang])
                        ->count();

			$rewash = $table->where('opname_detail_transaksi', TransactionType::Rewash)
                        ->whereNotIn('opname_detail_proses', [ProcessType::Pending, ProcessType::Hilang])
                        ->count();

			$not_register = $table->where('opname_detail_transaksi', BooleanType::No)->count();
			$total = $kotor + $scan_rs + $pending + $hilang + $retur + $rewash;
			@endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $key ?? '' }}</td>
                <td>{{ $register }}</td>
                <td>{{ $hilang_rs }}</td>
                <td>{{ $scan_rs }}</td>
                <td>{{ $kotor }}</td>
                <td>{{ $pending }}</td>
                <td>{{ $hilang }}</td>
                <td>{{ $retur }}</td>
                <td>{{ $rewash }}</td>
                <td>{{ $not_register }}</td>
                <td>{{ $total }}</td>
            </tr>

            @empty
            @endforelse

			<tr>
				<td colspan="2">Total</td>
				@php
				$sub_kotor = $data->where('opname_detail_transaksi', TransactionType::Kotor)
                ->whereNotIn('opname_detail_proses', [ProcessType::Pending, ProcessType::Hilang])
                ->count();

				$sub_hilang_rs = $data->where('opname_detail_ketemu', BooleanType::No)
                    ->count();

				$sub_scan_rs = $data->whereIn('opname_detail_transaksi', BERSIH)
                    ->where('opname_detail_ketemu', BooleanType::Yes)
                    ->count();

				$sub_pending = $data->where('opname_detail_proses', ProcessType::Pending)
                    ->count();

				$sub_hilang = $data->where('opname_detail_proses', ProcessType::Hilang)
                    ->count();

				$sub_retur = $data->where('opname_detail_transaksi', TransactionType::Retur)
                    ->whereNotIn('opname_detail_proses', [ProcessType::Pending, ProcessType::Hilang])
                    ->count();

				$sub_rewash = $data->where('opname_detail_transaksi', TransactionType::Rewash)
                    ->whereNotIn('opname_detail_proses', [ProcessType::Pending, ProcessType::Hilang])
                    ->count();

				$sub_not_register = $data->where('opname_detail_transaksi', BooleanType::No)->count();
				$sub_total = $data->count();
				@endphp
				<td>{{ $register }}</td>
				<td>{{ $sub_hilang_rs }}</td>
				<td>{{ $sub_scan_rs }}</td>
				<td>{{ $sub_kotor }}</td>
				<td>{{ $sub_pending }}</td>
				<td>{{ $sub_hilang }}</td>
				<td>{{ $sub_retur }}</td>
				<td>{{ $sub_rewash }}</td>
				<td>{{ $sub_not_register }}</td>
				<td>{{ $sub_total }}</td>
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