<x-layout>
    <x-card>
        <x-form :model="$model">
            <x-action form="form" />

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
</x-layout>
