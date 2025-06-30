<table border="0" class="header">
	<tr>
		<td></td>
		<td colspan="6">
			<h3>
				<b>DETAIL PENGIRIMAN LINEN PENDING </b>
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
				Periode : {{ formatDate(request()->get('start_delivery')) }} - {{ formatDate(request()->get('end_delivery')) }}
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
				<th>STATUS</th>
				<th>NO. DO</th>
				<th>TANGGAL. DO</th>
				<th>NO. BARCODE</th>
				<th>TANGGAL. BARCODE</th>
				<th>NO. RFID</th>
				<th>LINEN </th>
				<th>RUMAH SAKIT</th>
				<th>RUANGAN</th>
				<th>JUMLAH PEMAKAIAN LINEN</th>
				<th>TANGGAL REPORT</th>
				<th>TANGGAL REGISTER</th>
				<th>TANGGAL PENERIMAAN KOTOR</th>
				<th>TANGGAL MASUK PENDING</th>
				<th>TANGGAL KELUAR PENDING</th>
				<th>OPERATOR</th>
			</tr>
		</thead>
		<tbody>
			@forelse($data as $table)
			<tr>
				<td>{{ $loop->iteration }}</td>
				<td>
					{{ str_replace('Bersih', 'Pending', TransactionType::getDescription($table->field_status_bersih)) }}
				</td>
				<td>{{ $table->field_delivery }}</td>
				<td>{{ $table->transaksi_delivery_at }}</td>
				<td>{{ $table->field_barcode }}</td>
				<td>{{ $table->transaksi_barcode_at }}</td>
				<td>{{ $table->field_rfid }}</td>
				<td>{{ $table->view_linen_nama }}</td>
				<td>{{ $table->view_rs_nama }}</td>
				<td>{{ $table->view_ruangan_nama }}</td>
				<td>{{ $table->view_transaksi_cuci_total ?? 0 }}</td>
				<td>{{ formatDate($table->transaksi_report) }}</td>
				<td>{{ formatDate($table->view_tanggal_create) }}</td>
				<td>{{ $table->transaksi_created_at }}</td>
				<td>{{ $table->transaksi_pending_in }}</td>
				<td>{{ $table->transaksi_pending_out }}</td>
				<td>{{ $table->user_delivery ?? '' }}</td>
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