<div class="container row">
    <x-form-select col="4" wire:model.live="id_rs" label="Rumah sakit" name="view_rs_id" :options="$data_rs" />
    <x-form-select col="4" wire:model="id_ruangan" label="Ruangan" name="view_ruangan_id" :options="$data_ruangan" />
    <x-form-select col="4" wire:model="id_jenis" label="Jenis" name="view_jenis_id" :options="$data_jenis" />
</div>