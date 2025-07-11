<?php

namespace App\Http\Services;

use App\Dao\Enums\ProcessType;
use App\Dao\Models\Detail;
use App\Dao\Models\History;
use App\Dao\Models\Transaksi;
use App\Dao\Models\ViewDetailLinen;
use App\Http\Resources\DetailResource;
use Illuminate\Support\Facades\DB;
use Plugins\Notes;

class SaveTransaksiService
{
    public function save($status, $process, $transaksi, $linen, $log, $return = null)
    {
        $check = false;
        try {

            DB::beginTransaction();
            if(!empty($transaksi)){
                foreach(array_chunk($transaksi, env('TRANSACTION_CHUNK')) as $save_transaksi){
                    Transaksi::insert($save_transaksi);
                }
            }


            if(!empty($linen)){
                foreach(array_chunk($linen, env('TRANSACTION_CHUNK')) as $save_detail){

                    $detail = Detail::whereIn(Detail::field_primary(), $save_detail)->first();
                    if($detail->detail_status_proses != ProcessType::Pending)
                    {
                        Detail::whereIn(Detail::field_primary(), $save_detail)
                        ->update([
                            Detail::field_status_transaction() => $status,
                            Detail::field_status_process() => $process,
                            Detail::field_updated_by() => auth()->user()->id,
                            // Detail::field_updated_at() => date('Y-m-d H:i:s'),
                            // Detail::field_pending_created_at() => null,
                            // Detail::field_pending_updated_at() => null,
                            // Detail::field_hilang_created_at() => null,
                            // Detail::field_hilang_updated_at() => null,
                        ]);
                    }

                }
            }

            if(!empty($log)){
                foreach(array_chunk($log, env('TRANSACTION_CHUNK')) as $save_log){
                    History::upsert($save_log, [History::field_name()]);
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
