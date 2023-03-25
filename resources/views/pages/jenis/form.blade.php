<x-layout>
    <x-card>
        <x-form :model="$model" :upload="true">
            <x-action form="form" />

            @bind($model)

            <x-form-select col="6" class="search" label="Rumah sakit" name="jenis_id_rs" :options="$rs" />
            <x-form-select col="6" class="search" name="jenis_id_kategori" :options="$category" />
            <x-form-input col="6" name="jenis_nama" />
            <x-form-input col="6" name="jenis_berat" />
            <x-form-input col="6" name="jenis_parstok" />
            <x-form-upload col="3" name="upload" />
            @if($model)
            <div class="col-md-3">
                <img class="img-fluid" src="{{ $model->field_image_url }}" alt="$model->field_name">
            </div>
            @endif
            <x-form-textarea col="9" name="jenis_deskripsi" />

            @endbind

        </x-form>
    </x-card>
</x-layout>
