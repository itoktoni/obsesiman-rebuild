<?php

namespace App\Http\Services;

use App\Dao\Enums\ProcessType;
use App\Dao\Models\Detail;
use App\Dao\Models\Transaksi;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Plugins\History;
use Plugins\Notes;

class UpdateDeliveryService
{
    public function update($data, $code, $status_transaksi)
    {
        DB::beginTransaction();

        try {

            $startDate = Carbon::createFromFormat('Y-m-d H:i', date('Y-m-d') . ' 13:00');
            $endDate = Carbon::createFromFormat('Y-m-d H:i', date('Y-m-d') . ' 23:59');

            $check = Carbon::now()->between($startDate, $endDate);
            $report_date = Carbon::now();
            if ($check) {
                $report_date = Carbon::now()->addDay(1);
            }

            $check = Transaksi::whereIn(Transaksi::field_barcode(), $data)
                ->whereNull(Transaksi::field_delivery())
                ->update([
                    Transaksi::field_delivery() => $code,
                    Transaksi::field_delivery_by() => auth()->user()->id,
                    Transaksi::field_delivery_at() => date('Y-m-d H:i:s'),
                    Transaksi::field_report() => $report_date->format('Y-m-d'),
                ]);

            $rfid = Transaksi::select(Transaksi::field_rfid())
                ->where(Transaksi::field_delivery(), $code)
                ->get();

            if ($rfid && $check) {

                $data_rfid = $rfid->pluck(Transaksi::field_rfid());
                Detail::whereIn(Detail::field_primary(), $data_rfid)
                    ->update([
                        Detail::field_status_process() => ProcessType::Delivery,
                    ]);

                History::bulk($data_rfid, ProcessType::Delivery);

                Detail::whereIn(Detail::field_primary(), $data_rfid)
                    ->update([
                        Detail::field_status_transaction() => $status_transaksi,
                        Detail::field_status_process() => ProcessType::Bersih,
                    ]);

                History::bulk($data_rfid, ProcessType::Bersih);
            } else {
                DB::rollBack();
                return Notes::error('RFID tidak ditemukan!');
            }

            DB::commit();

            $return['code'] = $code;
            $return['rfid'] = $data_rfid;

            return Notes::data($return);

        } catch (\Throwable $th) {
            DB::rollBack();
            return Notes::error($th->getMessage());
        }
    }
}
