<x-layout>

    <x-card label="Summary Pengiriman Pending">
        <x-form :model="$model" :spa="false" target="_blank"  method="GET" action="{{ moduleRoute('getPrint') }}" :upload="true">
            <x-action form="print" />
                <input type="hidden" name="report_name" value="Laporan Summary Pengiriman Bersih">
                <x-form-select col="4" class="search" name="rs_id" label="Rumah Sakit" :options="$rs" />
                <x-form-select col="2" name="status_bersih" label="Status" :options="$status" />

                <x-form-input col="3" type="date" label="Tanggal Awal" name="start_delivery" />
                <x-form-input col="3" type="date" label="Tanggal Akhir" name="end_delivery" />
            @endbind
        </x-form>
    </x-card>
</x-layout>
