<x-layout>

    <x-card>

        <x-form method="GET" action="{{ moduleRoute('getTable') }}">

                @livewire('dropdown', ['label' => false])

                <div class="container">
                    <div class="row">
                        <x-form-select prepend="Scan RS" col="4" class="search" :label=false name="rs_id" :options="$rs" />
                        <x-form-select prepend="Status" col="4" class="search" :label=false name="status" :options="$status" />
                        <x-form-input prepend="No. Pending" :label=false col="4" name="transaksi_pending" />

                        <x-form-input prepend="No. RFID" :label=false col="4" name="transaksi_rfid" />
                        <x-form-input type="date" prepend="Tanggal Awal" :label=false col="4" name="start_date" />
                        <x-form-input type="date" prepend="Tanggal Akhir" :label=false col="4" name="end_date" />
                    </div>
                </div>

                <x-filter toggle="Filter" hide="false" :fields="$fields" />
        </x-form>

        <x-form method="POST" action="{{ moduleRoute('getTable') }}">

            <div class="container">
                <div class="table-responsive" id="table_data">
                    <table class="table table-bordered table-striped overflow">
                        <thead>
                            <tr>
                                <th class="text-center column-checkbox">{{ __('No.') }}</th>
                                <th>NO. TRANSAKSI</th>
                                <th>NO. RFID</th>
                                <th>TGL KOTOR</th>
                                <th>PENDING IN</th>
                                <th>PENDING OUT</th>
                                <th>PENDING</th>
                                <th>BARCODE</th>
                                <th>DELIVERY</th>
                                <th>LINEN </th>
                                <th>RUMAH SAKIT</th>
                                <th>RUANGAN</th>
                                <th>LOKASI SCAN RUMAH SAKIT</th>
                                <th>STATUS KOTOR</th>
                                <th>OPERATOR</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $i = ($data->currentpage()-1) * $data->perpage() + 1;
                            @endphp
                            @forelse($data as $key => $table)
                                <tr>
                                    <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                                    <td>{{ $table->field_key }}</td>
                                    <td>{{ $table->field_rfid }}</td>
                                    <td>{{ formatDate($table->transaksi_created_at) }}</td>
                                    <td>{{ formatDate($table->transaksi_pending_in) }}</td>
                                    <td>{{ formatDate($table->transaksi_pending_out) }}</td>
                                    <td>{{ $table->transaksi_pending }}</td>
                                    <td>{{ $table->transaksi_barcode }}</td>
                                    <td>{{ $table->transaksi_delivery }}</td>
                                    <td>{{ $table->view_linen_nama }}</td>
                                    <td>{{ $table->view_rs_nama }}</td>
                                    <td>{{ $table->view_ruangan_nama }}</td>
                                    <td>{{ $table->rs_nama }}</td>
                                    <td>{{ TransactionType::getDescription($table->transaksi_status) }}</td>
                                    <td>{{ $table->name }}</td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <x-pagination :data="$data" />
            </div>

        </x-form>

    </x-card>

</x-layout>