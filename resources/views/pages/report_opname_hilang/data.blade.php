<table border="0" class="header">
	<tr>
		<td></td>
		<td colspan="6">
			<h3>
				<b>REPORT OPNAME HILANG RS : {{ $opname->field_primary ?? '' }} </b>
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
</table>

<div class="table-responsive" id="table_data">
	<table id="export" border="1" style="border-collapse: collapse !important; border-spacing: 0 !important;"
		class="table table-bordered table-striped table-responsive-stack">
		<thead>
			<tr>
				<th width="1">No. </th>
				<th>LINEN </th>
				<th>NO. RFID</th>
				<th>TANGGAL REGISTER</th>
				<th>STATUS TERAKHIR</th>
				<th>STATUS LINEN</th>
				<th>TANGGAL TERAKHIR</th>
				<th>LAMA HILANG (hari)</th>
				<th>JUMLAH PEMAKAIAN LINEN</th>
				<th>OPERATOR</th>
			</tr>
		</thead>
		<tbody>
			@forelse($data->sortBy('view_linen_nama') as $table)
			<tr>
				<td>{{ $loop->iteration }}</td>
				<td>{{ $table->view_linen_nama ?? '' }}</td>
				<td>{{ $table->view_linen_rfid }}</td>
				<td>{{ formatDate($table->view_tanggal_create) }}</td>
				<td>{{ $table->opname_detail_transaksi ? TransactionType::getDescription($table->opname_detail_transaksi) : 'Belum Register' }}</td>
				<td>{{ $table->opname_detail_proses ? ProcessType::getDescription($table->opname_detail_proses) : 'Belum Register' }}</td>
				<td>{{ formatDate($table->view_tanggal_update) }}</td>
				<td>{{ $table->view_tanggal_update ? \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $table->view_tanggal_update)->diff(now())->format('%a') : '0' }} Hari</td>
				<td>{{ $table->view_transaksi_cuci_total ?? 0 }}</td>
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