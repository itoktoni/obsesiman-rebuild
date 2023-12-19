<table border="0" class="header">
	<tr>
		<td></td>
		<td colspan="6">
			<h3>
				<b>REPORT OPNAME DETAIL : {{ $opname->field_primary ?? '' }} </b>
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
	@if($filter = request()->get('status'))
	<tr>
		<td></td>
		<td colspan="10">
			<h5>
				FILTER : {{ FilterType::getDescription(intval($filter)) }}
			</h5>
		</td>
	</tr>
	@endif
</table>

<div class="table-responsive" id="table_data">
	<table id="export" border="1" style="border-collapse: collapse !important; border-spacing: 0 !important;"
		class="table table-bordered table-striped table-responsive-stack">
		<thead>
			<tr>
				<th width="1">No. </th>
				<th>NO. RFID</th>
				<th>LINEN </th>
				<th>RUMAH SAKIT</th>
				<th>RUANGAN</th>
				<th>SUDAH DI OPNAME</th>
				<th>STATUS TRANSAKSI</th>
				<th>STATUS LINEN</th>
				<th>CUCI/RENTAL</th>
				<th>JUMLAH PEMAKAIAN LINEN</th>
				<th>TANGGAL REGISTER</th>
				<th>OPERATOR</th>
			</tr>
		</thead>
		<tbody>
			@forelse($data as $table)
			<tr>
				<td>{{ $loop->iteration }}</td>
				<td>{{ $table->opname_detail_rfid }}</td>
				<td>{{ $table->view_linen_nama ?? '' }}</td>
				<td>{{ $table->view_rs_nama ?? '' }}</td>
				<td>{{ $table->view_ruangan_nama ?? '' }}</td>
				<td>{{ formatDate($table->opname_detail_waktu) }}</td>
				<td>{{ $table->opname_detail_transaksi ? TransactionType::getDescription($table->opname_detail_transaksi) : 'Belum Register' }}</td>
				<td>{{ $table->opname_detail_proses ? ProcessType::getDescription($table->opname_detail_proses) : 'Belum Register' }}</td>
				<td>{{ empty($table->view_status_cuci) ? '' : CuciType::getDescription($table->view_status_cuci) }}</td>
				<td>{{ $table->view_pemakaian ?? 0 }}</td>
				<td>{{ formatDate($table->view_tanggal_create) }}</td>
				<td>{{ $table->name }}</td>
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