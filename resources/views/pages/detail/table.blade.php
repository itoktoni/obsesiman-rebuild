<x-layout>

    <x-card>

        <x-form method="GET" action="{{ moduleRoute('getTable') }}">
            <x-filter toggle="Filter" hide="true" :fields="$fields" />
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
                                <th class="text-left">NO. RFID</th>
                                <th class="text-left">KATEGORI LINEN</th>
                                <th class="text-left">LINEN</th>
                                <th class="text-left">BERAT</th>
                                <th class="text-left">RUMAH SAKIT</th>
                                <th class="text-left">RUANGAN</th>
                                <th class="text-left">CUCI/RENTAL</th>
                                <th class="text-left">JUMLAH PEMAKAIAN LINEN</th>
                                <th class="text-left">JUMLAH RETUR</th>
                                <th class="text-left">JUMLAH REWASH</th>
                                <th class="text-left">STATUS TRANSAKSI</th>
                                <th class="text-left">POSISI TERAKHIR</th>
                                <th class="text-left">TANGGAL POSISI TERAKHIR</th>
                                <th class="text-left">OPERATOR UPDATE TERAKHIR</th>
                                <th class="text-left">STATUS REGISTER</th>
                                <th class="text-left">TANGGAL REGISTRASI</th>
                                <th class="text-left">OPERATOR REGISTRASI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $key => $table)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="checkbox" name="code[]"
                                            value="{{ $table->field_primary }}">
                                    </td>
                                    <td class="col-md-3 text-center column-action">
                                        @if(!empty($table->field_primary))
                                        <x-crud :model="$table">
                                            <x-button module="getHistory" key="{{ $table->field_primary }}" color="success"
                                                icon="book" />
                                        </x-crud>
                                        @else
                                        @endif
                                    </td>
                                    <td>{{ iteration($data, $key) }}</td>
                                    <td>{{ $table->field_primary }}</td>
                                    <td>{{ $table->view_kategori_nama }}</td>
                                    <td>{{ $table->view_linen_nama }}</td>
                                    <td>{{ $table->view_linen_berat }}</td>
                                    <td>{{ $table->view_rs_nama }}</td>
                                    <td>{{ $table->view_ruangan_nama }}</td>
                                    <td>{{ CuciType::getDescription($table->view_status_cuci) }}</td>
                                    <td>{{ $table->view_transaksi_cuci_total ?? 0 }}</td>
                                    <td>{{ $table->view_transaksi_retur_total ?? 0 }}</td>
                                    <td>{{ $table->view_transaksi_rewash_total ?? 0 }}</td>
                                    <td>{{ $table->field_status_transaction_name }}</td>
                                    <td>{{ $table->field_status_process_name }}</td>
                                    <td>{{ formatDate($table->field_updated_at) }}</td>
                                    <td>{{ $table->view_updated_name }}</td>
                                    <td>{{ $table->field_status_register_name }}</td>
                                    <td>{{ formatDate($table->view_tanggal_create) }}</td>
                                    <td>{{ $table->view_created_name }}</td>
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