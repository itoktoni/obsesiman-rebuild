<x-layout>

    <x-card>

        <x-form method="GET" action="{{ moduleRoute('getTable') }}">

                @livewire('dropdown', ['label' => false])

                <div class="container">
                    <div class="row">
                        <x-form-select prepend="Scan RS" col="4" class="search" :label=false name="rs_id" :options="$rs" />
                        <x-form-select prepend="Status" col="4" class="search" :label=false name="status" :options="$status" />
                        <x-form-select prepend="Operator" col="4" class="search" :label=false name="transaksi_created_by" :options="$user" />

                        <x-form-input prepend="No. Transaksi" :label=false col="4" name="transaksi_key" />
                        <x-form-input prepend="No. DO" :label=false col="4" name="transaksi_delivery" />
                        <x-form-input prepend="No. Barcode" :label=false col="4" name="transaksi_barcode" />

                        <x-form-input prepend="No. RFID" :label=false col="4" name="transaksi_rfid" />
                        <x-form-input type="date" prepend="Tanggal Awal" :label=false col="4" name="start_date" />
                        <x-form-input type="date" prepend="Tanggal Akhir" :label=false col="4" name="end_date" />
                    </div>
                </div>

                <x-filter toggle="Filter" hide="false" :fields="$fields" />
        </x-form>

        <x-form method="POST" action="{{ moduleRoute('getTable') }}">

            <x-action/>

            <div class="container">
                <div class="table-responsive" id="table_data">
                    <table class="table table-bordered table-striped overflow">
                        <thead>
                            <tr>
                                <th width="9" class="center">
                                    <input class="btn-check-d" type="checkbox">
                                </th>
                                <th style="width: 100px" class="text-center column-action">{{ __('Action') }}</th>
                                <th class="text-center column-checkbox">{{ __('No.') }}</th>
                                <th>NO. TRANSAKSI</th>
                                <th>TANGGAL KOTOR</th>
                                <th>NO. RFID</th>
                                <th>LINEN </th>
                                <th>RUMAH SAKIT</th>
                                <th>RUANGAN</th>
                                <th>LOKASI SCAN RUMAH SAKIT</th>
                                <th>STATUS KOTOR</th>
                                <th>PENDING</th>
                                <th>HILANG</th>
                                <th>OPERATOR</th>
                                <th>No. BARCODE</th>
                                <th>No. DELIVERY</th>
                                <th>STATUS BERSIH</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $i = ($data->currentpage()-1) * $data->perpage() + 1;
                            @endphp
                            @forelse($data as $key => $table)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="checkbox" name="code[]"
                                            value="{{ $table->field_primary }}">
                                    </td>
                                    <td class="col-md-3 text-center column-action">
                                        @if(!empty($table->field_primary))
                                        <x-crud :model="$table">

                                        </x-crud>
                                        @else
                                        @endif
                                    </td>
                                    <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                                    <td>{{ $table->field_key }}</td>
                                    <td>{{ formatDate($table->transaksi_created_at) }}</td>
                                    <td>{{ $table->field_rfid }}</td>
                                    <td>{{ $table->view_linen_nama }}</td>
                                    <td>{{ $table->view_rs_nama }}</td>
                                    <td>{{ $table->view_ruangan_nama }}</td>
                                    <td>{{ $table->rs_nama }}</td>
                                    <td>{{ TransactionType::getDescription($table->transaksi_status) }}</td>
                                    <td>{{ formatDate($table->view_pending_create) }}</td>
                                    <td>{{ formatDate($table->view_hilang_create) }}</td>
                                    <td>{{ $table->name }}</td>
                                    <td>{{ $table->transaksi_barcode }}</td>
                                    <td>{{ $table->transaksi_delivery }}</td>
                                    <td>{{ TransactionType::getDescription($table->transaksi_bersih) }}</td>
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