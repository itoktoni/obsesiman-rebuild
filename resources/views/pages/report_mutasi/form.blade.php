<x-layout>
    <x-card>
        <x-form :model="$model" :spa="false" target="_blank"  method="GET" action="{{ moduleRoute('getPrint') }}" :upload="true">
            <x-action form="print" />
                <input type="hidden" name="report_name" value="Laporan Mutasi">
                <x-form-select col="6" class="search" name="mutasi_rs_id" label="Rumah Sakit" :options="$rs" />
                <x-form-select col="6" class="search" name="mutasi_linen_id" label="Jenis Linen" :options="$jenis" />
                <x-form-input col="6" type="date" label="Tanggal Awal" name="start_date" />
                <x-form-input col="6" type="date" label="Tanggal Akhir" name="end_date" />
            @endbind

        </x-form>
    </x-card>
</x-layout>
