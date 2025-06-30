<table border="0" class="header">
	<tr>
		<td></td>
		<td colspan="6">
			<h3>
				<b>DETAIL TRANSAKSI PENDING </b>
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
				Periode : {{ formatDate(request()->get('pending_in')) }} - {{ formatDate(request()->get('pending_out')) }}
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
				<th>NO. TRANSAKSI</th>
				<th>NO. RFID</th>
				<th>LINEN </th>
				<th>RUMAH SAKIT</th>
				<th>RUANGAN</th>
				<th>LOKASI SCAN RUMAH SAKIT</th>
				<th>STATUS TRANSAKSI</th>
				<th>STATUS LINEN</th>
				<th>CUCI/RENTAL</th>
				<th>JUMLAH PEMAKAIAN LINEN</th>
				<th>TANGGAL REGISTER</th>
				<th>NO. BARCODE</th>
				<th>NO. DELIVERY</th>
				<th>NO. PENDING</th>
				<th>TANGGAL PENERIMAAN KOTOR</th>
				<th>TANGGAL MASUK PENDING</th>
				<th>TANGGAL KELUAR PENDING</th>
			</tr>
		</thead>
		<tbody>
			@forelse($data as $table)
			<tr>
				<td>{{ $loop->iteration }}</td>
				<td>{{ $table->field_key }}</td>
				<td>{{ $table->field_rfid }}</td>
				<td>{{ $table->view_linen_nama }}</td>
				<td>{{ $table->view_rs_nama }}</td>
				<td>{{ $table->view_ruangan_nama }}</td>
				<td>{{ $table->field_rs_name }}</td>
				<td>{{ $table->field_status_transaction_name }}</td>
				<td>{{ empty($table->view_status_proses) ? 'Belum Register' : ProcessType::getDescription($table->view_status_proses) }}</td>
				<td>{{ empty($table->view_status_cuci) ? '' : CuciType::getDescription($table->view_status_cuci) }}</td>
				<td>{{ $table->view_transaksi_cuci_total ?? 0 }}</td>
				<td>{{ formatDate($table->view_tanggal_create) }}</td>
				<td>{{ $table->transaksi_barcode }}</td>
				<td>{{ $table->transaksi_delivery }}</td>
				<td>{{ $table->transaksi_pending }}</td>
				<td>{{ $table->transaksi_created_at }}</td>
				<td>{{ $table->transaksi_pending_in }}</td>
				<td>{{ $table->transaksi_pending_out }}</td>
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