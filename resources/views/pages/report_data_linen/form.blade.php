<x-layout>
    <x-card>
        <x-form :model="$model" :spa="false" target="_blank"  method="GET" action="{{ moduleRoute('getPrint') }}" :upload="true">

            <x-action form="print">
                <x-button type="submit" class="btn btn-success" label="Export" name="action" value="export"/>
            </x-action>

            <input type="hidden" name="report_name" value="Laporan Data Linen">
            <x-form-select col="6" class="search" name="view_rs_id" label="Rumah Sakit" :options="$rs" />
            <x-form-select col="6" class="search" name="view_ruangan_id" label="Ruangan" :options="$ruangan" />
            <x-form-select col="6" class="search" name="view_kategori_id" label="Kategori" :options="$kategori" />
            <x-form-select col="6" class="search" name="view_linen_id" label="Jenis Linen" :options="$jenis" />
        @endbind

        </x-form>
    </x-card>
</x-layout>
