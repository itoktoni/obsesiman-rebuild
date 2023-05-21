<x-layout>
    <x-card>
        <x-form :model="$model" :spa="false" target="_blank"  method="GET" action="{{ moduleRoute('getPrint') }}" :upload="true">
            <x-action form="print" />
                <input type="hidden" name="report_name" value="Laporan Transaksi">
                <x-form-input col="3" type="date" label="Tanggal Awal" name="start_date" />
                <x-form-input col="3" type="date" label="Tanggal Akhir" name="end_date" />
                <x-form-select col="6" class="search" name="transaksi_id_rs" label="Scan RS" :options="$rs" />
                <x-form-select col="6" name="transaksi_status" label="Transaksi Status" :options="$status" />
                <x-form-select col="6" class="search" name="rs_id" label="Rumah Sakit" :options="$rs" />
            @endbind

        </x-form>
    </x-card>
</x-layout>
