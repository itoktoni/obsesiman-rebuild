<x-layout>
    <x-card>
        <x-form :model="$model">
            <x-action form="form" />

            @bind($model)

            <x-form-select col="6" class="search" name="opname_id_rs" label="Rumah Sakit" :options="$rs" />
            <x-form-input col="3" type="date" label="Tanggal Mulai" name="opname_mulai" />
            <x-form-input col="3" type="date" label="Tanggal Selesai" name="opname_selesai" />
            <x-form-select col="6" class="search" name="opname_status" label="Status" :options="$status" />
            <x-form-textarea col="6" rows="4" label="Keterangan" name="opname_nama" />

            @endbind

        </x-form>
    </x-card>

    <x-card label="Data Opname">
        <div class="table-responsive" id="table_data">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th style="width: 100px;">No. RFID</th>
                        <th>Jenis Linen</th>
                        <th>Ruangan</th>
                        <th>Cuci/Rental</th>
                        <th>Pemakaian</th>
                        <th>Tgl Terakhir</th>
                        <th>Proses</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($detail as $table)
                    @php
                    $view = $table->has_view ?? false;
                    @endphp
                    <tr>
                        <td>{{ $table->field_rfid }}</td>
                        <td>{{ $view->field_name ?? '' }}</td>
                        <td>{{ $view->field_ruangan_name ?? '' }}</td>
                        <td>{{ $view->field_status_cuci_name ?? '' }}</td>
                        <td>{{ $view->field_pemakaian ?? '0' }}</td>
                        <td>{{ $view ? formatDate($view->field_tanggal_update) : '' }} </td>
                        <td>{{ $view->field_status_process_name ?? 'Belum Register' }} </td>
                        <td>{{ $table->field_ketemu == 1 ? 'Ketemu' : '-' }}</td>
                    </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

</x-layout>
