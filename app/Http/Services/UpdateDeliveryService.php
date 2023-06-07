<?php

namespace App\Http\Services;

use App\Dao\Enums\ProcessType;
use App\Dao\Models\Detail;
use App\Dao\Models\Transaksi;
use Illuminate\Support\Facades\DB;
use Plugins\History;
use Plugins\Notes;

class UpdateDeliveryService
{
    public function update($data, $code)
    {
        DB::beginTransaction();

        try {
            Transaksi::whereIn(Transaksi::field_rfid(), $data)
                ->whereNull(Transaksi::field_barcode())
                ->update([
                    Transaksi::field_barcode() => $code,
                    Transaksi::field_barcode_by() => auth()->user()->id,
                    Transaksi::field_barcode_at() => date('Y-m-d H:i:s'),
                ]);

            Detail::whereIn(Detail::field_primary(), $data)
                ->update([
                    Detail::field_status_process() => ProcessType::Barcode,
                ]);

            History::bulk($data, ProcessType::Barcode);
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
