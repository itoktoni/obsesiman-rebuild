<x-layout>
    <x-card>
        <x-form :model="$model" :upload="true">
            <x-action form="form" />

            @bind($model)

            <x-form-select col="6" class="search" label="Rumah sakit" name="jenis_id_rs" :options="$rs" />
            <x-form-select col="6" class="search" name="jenis_id_kategori" :options="$category" />
            <x-form-input col="6" name="jenis_nama" label="Nama Linen"/>
            <x-form-input col="6" name="jenis_berat" />
            <x-form-input type="number" col="6" name="jenis_parstok" />
            <x-form-textarea col="6" rows="4" name="jenis_deskripsi" />

            @endbind

        </x-form>
    </x-card>
</x-layout>
