<?php

namespace App\Http\Services;

use App\Dao\Enums\CetakType;
use App\Dao\Enums\LogType;
use App\Dao\Enums\ProcessType;
use App\Dao\Enums\TransactionType;
use App\Dao\Models\Cetak;
use App\Dao\Models\Detail;
use App\Dao\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Plugins\History;
use Plugins\Notes;
use Illuminate\Support\Str;

class UpdatePendingService
{
    public function update($rfid, $code, $status, $tanggal, $rs)
    {
        DB::beginTransaction();

        $pending_code = Str::uuid()->toString();

        $startDate = Carbon::createFromFormat('Y-m-d H:i', date('Y-m-d') . ' 13:00');
        $endDate = Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d') . ' 23:59:59');

        $check_date = Carbon::now()->between($startDate, $endDate);
        $report_date = Carbon::now();

        if ($check_date) {
            $report_date = Carbon::now()->addDay(1);
        }

        $transaksi = $status;
        if ($transaksi == TransactionType::BersihKotor)
        {
            $transaksi = TransactionType::Kotor;
        }
        else if ($transaksi == TransactionType::BersihRetur)
        {
            $transaksi = TransactionType::Retur;
        }
        else if ($transaksi == TransactionType::BersihRewash)
        {
            $transaksi = TransactionType::Rewash;
        }
        else if ($transaksi == TransactionType::Unknown)
        {
            $transaksi = TransactionType::Register;
        }


        try {

            $check = Transaksi::where(Transaksi::field_rs_ori(), $rs)
            ->whereNotNull(Transaksi::field_pending_in())
            ->whereNull(Transaksi::field_pending_out())
            ->whereNotNull(Transaksi::field_barcode())
            ->where(Transaksi::field_status_transaction(), $transaksi)
            ->update([
                Transaksi::field_pending() => $pending_code,
                Transaksi::field_pending_out() => $tanggal.' '.date('H:i:s'),
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
                        Detail::field_status_transaction() => $status,
                        Detail::field_status_process() => ProcessType::Bersih,
                        Detail::field_status_history() => LogType::Bersih,
                        Detail::field_updated_by() => auth()->user()->id,
                        Detail::field_pending_created_at() => null,
                        Detail::field_pending_updated_at() => null,
                        Detail::field_hilang_created_at() => null,
                        Detail::field_hilang_updated_at() => null,
                    ]);

                History::bulk($data_rfid, LogType::Bersih);

            } else {
                DB::rollBack();
                return Notes::error('RFID tidak ditemukan!');
            }

            $cetak = Cetak::where(Cetak::field_name(), $code)->first();
            if(!$cetak){
                $cetak = Cetak::create([
                    Cetak::field_date() => date('Y-m-d'),
                    Cetak::field_name() => $code,
                    Cetak::field_type() => CetakType::Delivery,
                    Cetak::field_user() => auth()->user()->name ?? null,
                    Cetak::field_rs_id() => request()->get('rs_id') ?? null,
                ]);
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