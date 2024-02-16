<table border="0" class="header">
	<tr>
		<td></td>
		<td colspan="6">
			<h3>
				<b>REKAP LINEN HILANG</b>
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
				Periode : {{ formatDate(request()->get('start_hilang')) }} - {{ formatDate(request()->get('end_hilang')) }}
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
				<th>NO. RFID</th>
				<th>LINEN</th>
				<th>RUMAH SAKIT</th>
				<th>RUANGAN</th>
				<th>JUMLAH PEMAKAIAN LINEN</th>
				<th>TANGGAL REGISTER</th>
				<th>LAMA HILANG</th>
				<th>TANGGAL PENERIMAAN</th>
				<th>STATUS</th>
				<th>PROSES TERAKHIR</th>
			</tr>
		</thead>
		<tbody>
			@php
			$total_berat = 0;
			@endphp

			@forelse($data as $table)
			<tr>
				<td>{{ $loop->iteration }}</td>
				<td>{{ $table->field_primary }}</td>
				<td>{{ $table->field_name }}</td>
				<td>{{ $table->field_rs_name }}</td>
				<td>{{ $table->field_ruangan_name }}</td>
				<td class="text-right">{{ $table->view_pemakaian ?? 0 }}</td>
				<td>{{ formatDate($table->field_tanggal_create) }}</td>
				<td>{{ $table->view_hilang_create ? \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $table->view_hilang_create)->diff(now())->format('%a') : '0' }} Hari</td>
				<td>{{ formatDate($table->field_tanggal_update, true) }}</td>
				<td>{{ TransactionType::getDescription($table->view_status_transaksi) }}</td>
				<td>{{ ProcessType::getDescription($table->view_log_status) }}</td>
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