<x-layout>
    <x-card>
        <x-form :model="$model">

            @bind($model)

            <x-form-select col="6" class="search" label="Rumah sakit" name="dl_id_rs" :options="$rs" />
            <x-form-select col="6" class="search" name="dl_id_ruangan" :options="$ruangan" />
            <x-form-select col="6" class="search" name="dl_id_nama_linen" :options="$name" />

            <div class="form-group col-md-6 ">
                <label>RFID</label>
                <input type="text" {{ $model ? 'readonly' : '' }} class="form-control" value="{{ old('dl_rfid') ?? $model->dl_rfid ?? null }}" name="dl_rfid">
            </div>

            @endbind

        </x-form>
    </x-card>

    <x-card label="History Linen">

        <div class="table-responsive" id="table_data">
            <table class="table table-bordered table-striped overflow">
                <thead>
                    <tr>
                        <th style="width: 170px">Tanggal</th>
                        <th>User</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $table)
                        <tr>
                            <td>{{ $table->field_created_at }}</td>
                            <td>{{ $table->field_created_by }}</td>
                            <td>{{ $table->field_status }}</td>
                            <td>{{ $table->field_description }}</td>
                        </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>

    </x-card>
</x-layout>
