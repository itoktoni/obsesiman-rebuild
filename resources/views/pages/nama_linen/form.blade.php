<x-layout>
    <x-card>
        <x-form :model="$model" :upload="true">
            <x-action form="form" />

            @bind($model)

            <x-form-select col="6" class="search" label="Rumah sakit" name="nl_id_rs" :options="$rs" />
            <x-form-select col="6" class="search" name="nl_id_kategori" :options="$category" />
            <x-form-input col="6" name="nl_nama" />
            <x-form-input col="6" name="nl_parstock" />
            <x-form-upload col="6" name="upload" />
            <x-form-textarea col="6" name="nl_deskripsi" />

            @endbind

        </x-form>
    </x-card>
</x-layout>
