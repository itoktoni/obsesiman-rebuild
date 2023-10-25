<x-layout>
    <x-card label="Summary Pengiriman Bersih">
        <x-form :model="$model" :spa="false" target="_blank"  method="GET" action="{{ moduleRoute('getPrint') }}" :upload="true">
            <x-action form="print" />
                <input type="hidden" name="report_name" value="Laporan Summary Pengiriman Bersih">
                <x-form-select col="5" class="search" name="rs_id" label="Rumah Sakit" :options="$rs" />
                <x-form-input col="3" type="date" label="Tanggal Awal" name="tanggal" />
                <x-form-select col="4" name="print" label="Print" :options="$cetak" />
            @endbind
        </x-form>
    </x-card>
</x-layout>
