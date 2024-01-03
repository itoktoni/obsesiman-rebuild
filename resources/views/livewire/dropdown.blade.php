<div class="container row">
    <x-form-select col="4" wire:model.live="id_rs" label="Rumah sakit" name="view_rs_id" value="{{ $id_rs }}" :options="$data_rs" />
    <x-form-select col="4" wire:model="id_ruangan" label="Ruangan" name="view_ruangan_id" value="{{ $id_ruangan }}" :options="$data_ruangan" />
    <x-form-select col="4" wire:model="id_jenis" label="Jenis" name="view_linen_id" value="{{ $id_jenis }}" :options="$data_jenis" />
    <div class="col-md-12">
        <hr style="margin-top: 0px;">
    </div>
</div>