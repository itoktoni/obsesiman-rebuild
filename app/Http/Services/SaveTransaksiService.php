<?php

namespace App\Http\Services;

use App\Dao\Enums\ProcessType;
use App\Dao\Enums\TransactionType;
use App\Dao\Facades\EnvFacades;
use App\Dao\Models\Detail;
use App\Dao\Models\History;
use App\Dao\Models\Transaksi;
use Illuminate\Support\Facades\DB;
use Plugins\Alert;
use Plugins\Notes;

class SaveTransaksiService
{
    public function save($status, $transaksi, $detail, $log, $return)
    {
        $check = false;
        try {

            DB::beginTransaction();
            if(!empty($transaksi)){
                foreach(array_chunk($transaksi, env('TRANSACTION_CHUNK')) as $save_transaksi){
                    Transaksi::insert($save_transaksi);
                }
            }

            if(!empty($detail)){
                foreach(array_chunk($detail, env('TRANSACTION_CHUNK')) as $save_detail){
                    Detail::whereIn(Detail::field_primary(), $save_detail)
                    ->update([
                        Detail::field_status_transaction() => $status,
                        Detail::field_status_process() => ProcessType::Kotor,
                    ]);
                }
            }

            if(!empty($log)){
                foreach(array_chunk($log, env('TRANSACTION_CHUNK')) as $save_log){
                    History::insert($save_log);
                }
            }

            DB::commit();
           return Notes::create($return);

        } catch (\Throwable $th) {
            DB::rollBack();
            return Notes::error($th->getMessage());
        }

        return Notes::error('Unknown');
    }
}
