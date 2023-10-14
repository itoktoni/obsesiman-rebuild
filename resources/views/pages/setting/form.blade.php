<x-layout>
    <x-card>
        <x-form :model="$model" :upload="true">
            <x-action form="form" />

            @bind($model)
                <x-form-input col="6" value="{{ env('APP_NAME') }}" label="Nama Perusahaan" name="name" />
                <x-form-input col="6" value="{{ env('APP_TITLE') }}" label="Nama Title" name="title" />
                <x-form-upload col="3" name="logo" />
                <div class="col-md-3">
                    <img class="img-thumbnail img-fluid" src="{{ env('APP_LOGO') ? url('public/storage/'.env('APP_LOGO')) : url('assets/media/image/logo.png') }}" alt="">
                </div>
                <x-form-input col="6" value="{{ env('APP_LOCATION') }}" label="Lokasi Report" name="location" />
                <x-form-input col="3" value="{{ env('TRANSACTION_DAY_ALLOWED') }}" label="Toleransi hari Tembak kotor" name="transaction_day" />
                <x-form-select col="3" name="transaction_active" :default="env('TRANSACTION_ACTIVE_RS_ONLY')" label="Proteksi transaksi" :options="$active" />
                <x-form-select col="3" name="telescope_enable" :default="env('TELESCOPE_ENABLE')" label="Telescope Debugger" :options="$active" />
                <x-form-input col="3" value="{{ env('TRANSACTION_CHUNK') }}" label="Jumlah Batch Per Transaksi" name="transaction_chunk" />

                <x-form-input col="6" value="{{ env('CODE_BERSIH') }}" label="Kode Bersih" name="code_bersih" />
                <x-form-input col="6" value="{{ env('CODE_KOTOR') }}" label="Kode Kotor" name="code_kotor" />
                <x-form-input col="6" value="{{ env('CODE_RETUR') }}" label="Kode Retur" name="code_retur" />
                <x-form-input col="6" value="{{ env('CODE_REWASH') }}" label="Kode Rewash" name="code_rewash" />
            @endbind

        </x-form>
    </x-card>
</x-layout>
