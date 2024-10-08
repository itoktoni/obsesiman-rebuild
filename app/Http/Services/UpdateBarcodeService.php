<?php

namespace App\Http\Services;

use App\Dao\Enums\CetakType;
use App\Dao\Enums\LogType;
use App\Dao\Enums\ProcessType;
use App\Dao\Models\Cetak;
use App\Dao\Models\Detail;
use App\Dao\Models\Transaksi;
use Illuminate\Support\Facades\DB;
use Plugins\History;
use Plugins\Notes;

class UpdateBarcodeService
{
    public function update($data, $code, $status, $ruangan, $rs)
    {
        DB::beginTransaction();

        try {
            Transaksi::whereIn(Transaksi::field_rfid(), $data)
            ->whereNull(Transaksi::field_barcode())
            ->where(Transaksi::field_rs_ori(), $rs)
            ->update([
                Transaksi::field_barcode() => $code,
                Transaksi::field_rs_ori() => $rs,
                Transaksi::field_status_bersih() => $status,
                Transaksi::field_barcode_by() => auth()->user()->id,
                Transaksi::field_barcode_at() => date('Y-m-d H:i:s'),
                Transaksi::field_ruangan_id() => $ruangan,
            ]);

            Detail::whereIn(Detail::field_primary(), $data)
            ->where(Detail::field_rs_id(), $rs)
            ->update([
                Detail::field_status_process() => ProcessType::Barcode,
                Detail::field_status_history() => LogType::Barcode,
                Detail::field_updated_at() => date('Y-m-d H:i:s'),
                Detail::field_updated_by() => auth()->user()->id,
                Detail::field_pending_created_at() => null,
                Detail::field_pending_updated_at() => null,
                Detail::field_hilang_created_at() => null,
                Detail::field_hilang_updated_at() => null,
            ]);

            $cetak = Cetak::where(Cetak::field_name(), $code)->first();
            if(!$cetak){
                $cetak = Cetak::create([
                    Cetak::field_date() => date('Y-m-d'),
                    Cetak::field_name() => $code,
                    Cetak::field_type() => CetakType::Barcode,
                    Cetak::field_user() => auth()->user()->name ?? 'Admin',
                    Cetak::field_rs_id() => $rs ?? null,
                    Cetak::field_ruangan_id() => $ruangan ?? null,
                ]);
            }

            History::bulk($data, LogType::Barcode);
            DB::commit();

            $return['code'] = $code;
            $return['rfid'] = $data;

            return Notes::data($return);

        } catch (\Throwable $th) {
            DB::rollBack();
            return Notes::error($th->getMessage());
        }
    }
}