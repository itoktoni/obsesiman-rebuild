<table border="0" class="header">
	<tr>
		<td></td>
		<td colspan="10">
			<h3>
				<b>REPORT PAR-STOK</b>
			</h3>
		</td>
		<td rowspan="3">
			<img width="200" style="position: absolute;left:40%;top:20px" src="{{ env('APP_LOGO') ? url('storage/'.env('APP_LOGO')) : url('assets/media/image/logo.png') }}" alt="logo">
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
				Periode : {{ request()->get('start_date') }} - {{ request()->get('end_date') }}
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
				<th>LINEN</th>
				<th>RUMAH SAKIT</th>
				<th>JUMLAH REGISTER</th>
				<th>BERAT (KG)</th>
				<th>TOTAL (KG)</th>
				<th>PAR-STOCK</th>
				<th>KURANG LEBIH</th>
			</tr>
		</thead>
		<tbody>
			@php
			$total_berat = 0;
			@endphp

			@forelse($data as $table)
			<tr>
				<td>{{ $loop->iteration }}</td>
				<td>{{ $table->field_name }}</td>
				<td>{{ $table->field_rs_name }}</td>
				<td>{{ $table->field_total }}</td>
				<td>{{ $table->field_weight }}</td>
				<td>{{ $table->field_total * $table->field_weight }}</td>
				<td>{{ $table->field_parstock }}</td>
				<td>{{ $table->field_total - $table->field_parstock }}</td>
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