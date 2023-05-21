<x-layout>
    <x-card>
        <x-form :model="$model" :spa="false" target="_blank"  method="GET" action="{{ moduleRoute('getPrint') }}" :upload="true">
            <x-action form="print" />
                <input type="hidden" name="report_name" value="Laporan Data Linen">
                <x-form-select col="6" class="search" name="view_rs_id" label="Rumah Sakit" :options="$rs" />
            @endbind

        </x-form>
    </x-card>
</x-layout>
