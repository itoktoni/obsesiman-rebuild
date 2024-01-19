<?php

namespace App\Http\Services;

use App\Dao\Enums\ProcessType;
use App\Dao\Enums\TransactionType;
use App\Dao\Models\Detail;
use App\Dao\Models\Transaksi;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Plugins\History;
use Plugins\Notes;

class UpdateDeliveryService
{
    public function update($code, $status_transaksi)
    {
        DB::beginTransaction();

        try {

            $startDate = Carbon::createFromFormat('Y-m-d H:i', date('Y-m-d') . ' 13:00');
            $endDate = Carbon::createFromFormat('Y-m-d H:i', date('Y-m-d') . ' 23:59');

            $check_date = Carbon::now()->between($startDate, $endDate);
            $report_date = Carbon::now();
            if ($check_date) {
                $report_date = Carbon::now()->addDay(1);
            }

            $transaksi = $status_transaksi;
            if ($transaksi == TransactionType::BersihKotor) {
                $transaksi = TransactionType::Kotor;
            } else if ($transaksi == TransactionType::BersihRetur){
                $transaksi = TransactionType::Retur;
            } else if ($transaksi == TransactionType::BersihRewash){
                $transaksi = TransactionType::Rewash;
            } else if ($transaksi == TransactionType::Unknown){
                $transaksi = TransactionType::Register;
            }

            $check = Transaksi::query()
                ->whereNull(Transaksi::field_delivery())
                ->where(Transaksi::field_rs_ori(), request()->get('rs_id'))
                ->where(Transaksi::field_status_transaction(), $transaksi)
                ->whereNotNull(Transaksi::field_barcode())
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
                        Detail::field_status_transaction() => $status_transaksi,
                        Detail::field_status_process() => ProcessType::Bersih,
                        Detail::field_updated_at() => date('Y-m-d H:i:s'),
                        Detail::field_updated_by() => auth()->user()->id,
                        Detail::field_pending_created_at() => null,
                        Detail::field_pending_updated_at() => null,
                        Detail::field_hilang_created_at() => null,
                        Detail::field_hilang_updated_at() => null,
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
