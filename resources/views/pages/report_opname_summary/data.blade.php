<table border="0" class="header">
    <tr>
        <td></td>
        <td colspan="6">
            <h3>
                <b>REPORT OPNAME SUMMARY </b>
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
                OPNAME ID : {{ $opname->field_primary ?? '' }}
            </h3>
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
                <th>TEMBAK SO</th>
                <th>RETUR</th>
                <th>REWASH</th>
                <th>PENDING</th>
                <th>HILANG</th>
                <th>BELUM REGISTER</th>
                <th>TOTAL OPNAME</th>
                <th>SELISIH</th>
            </tr>
        </thead>
        <tbody>
			@php
			$map = [];
			if(!empty($data)){
				$map = $data->mapToGroups(function($item){
					return [formatDate($item->opname_detail_waktu) => $item];
				})->sortDesc();
			}

            $grand_total = 0;
			@endphp
            @forelse($map as $key => $table)
			@php
			$tembak_so = $table->where('opname_detail_scan_rs', BooleanType::Yes)
                        ->where('opname_detail_ketemu', 1)
                        ->where('opname_detail_transaksi', '!=', 0)
                        ->count();

            $pending = $table->where('opname_detail_proses', ProcessType::Pending)
                        ->where('opname_detail_scan_rs', 0)
                        ->count();

			$hilang = $table->where('opname_detail_proses', ProcessType::Hilang)
                        ->where('opname_detail_scan_rs', 0)
                        ->count();

			$retur = $table->where('opname_detail_transaksi', TransactionType::Retur)
                        ->whereNotIn('opname_detail_proses', [ProcessType::Pending, ProcessType::Hilang])
                        ->count();

			$rewash = $table->where('opname_detail_transaksi', TransactionType::Rewash)
                        ->whereNotIn('opname_detail_proses', [ProcessType::Pending, ProcessType::Hilang])
                        ->count();

			$not_register = $table->where('opname_detail_transaksi', BooleanType::No)->count();
			$total = $tembak_so + $pending + $hilang + $retur + $rewash;
            $grand_total = $grand_total + $total;
			@endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $key ?? '' }}</td>
                <td>{{ $register }}</td>
                <td>{{ $tembak_so }}</td>
                <td>{{ $retur }}</td>
                <td>{{ $rewash }}</td>
                <td>{{ $pending }}</td>
                <td>{{ $hilang }}</td>
                <td>{{ $not_register }}</td>
                <td>{{ $total }}</td>
                <td></td>
            </tr>

            @empty
            @endforelse

			<tr>
				<td colspan="2">Total</td>
				@php
				$sub_tembak_so = $data->where('opname_detail_scan_rs', BooleanType::Yes)
                    ->where('opname_detail_ketemu', 1)
                    ->where('opname_detail_transaksi', '!=', 0)
                    ->count();

				$sub_pending = $data->where('opname_detail_proses', ProcessType::Pending)
                    ->where('opname_detail_scan_rs', 0)
                    ->count();

				$sub_hilang = $data->where('opname_detail_proses', ProcessType::Hilang)
                    ->where('opname_detail_scan_rs', 0)
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
				<td>{{ $sub_tembak_so }}</td>
                <td>{{ $sub_retur }}</td>
				<td>{{ $sub_rewash }}</td>
				<td>{{ $sub_pending }}</td>
				<td>{{ $sub_hilang }}</td>
				<td>{{ $sub_not_register }}</td>
				<td>{{ $grand_total }}</td>
				<td>{{ $register - $grand_total }}</td>
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