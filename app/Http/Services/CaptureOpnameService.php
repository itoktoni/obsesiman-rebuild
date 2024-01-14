<?php

namespace App\Http\Services;

use App\Dao\Enums\BooleanType;
use App\Dao\Enums\OpnameType;
use App\Dao\Enums\ProcessType;
use App\Dao\Enums\TransactionType;
use App\Dao\Interfaces\CrudInterface;
use App\Dao\Models\Detail;
use App\Dao\Models\History as ModelsHistory;
use App\Dao\Models\OpnameDetail;
use App\Dao\Models\Transaksi;
use Illuminate\Support\Facades\DB;
use Plugins\Alert;
use Sabre\VObject\Property\Boolean;

class CaptureOpnameService
{
    public function save($model)
    {
        $check = false;
        try {

            DB::beginTransaction();

                $tgl = date('Y-m-d H:i:s');
                $model->opname_capture = $tgl;
                $model->save();

                $opname = $model;
                $opname_id = $opname->opname_id;

                $data_rfid = Detail::where(Detail::field_rs_id(), $opname->opname_id_rs)->get();
                $log = [];
                if($data_rfid){
                    $id = auth()->user()->id;

                    foreach($data_rfid as $item){

                        $ketemu = $this->checkKetemu($item);
                        $data[] = [
                            OpnameDetail::field_rfid() => $item->detail_rfid,
                            OpnameDetail::field_transaksi() => $item->detail_status_transaksi,
                            OpnameDetail::field_proses() => $item->detail_status_proses,
                            OpnameDetail::field_created_at() => $tgl,
                            OpnameDetail::field_created_by() => $id,
                            OpnameDetail::field_updated_at() => !empty($item->detail_updated_at) ? $item->detail_updated_at->format('Y-m-d H:i:s') : null,
                            OpnameDetail::field_updated_by() => $id,
                            OpnameDetail::field_waktu() => $tgl,
                            OpnameDetail::field_ketemu() => $ketemu,
                            OpnameDetail::field_opname() => $opname_id,
                            OpnameDetail::field_pending() => !empty($item->detail_pending_at) ? $item->detail_pending_created_at->format('Y-m-d H:i:s') : null,
                            OpnameDetail::field_hilang() => !empty($item->detail_hilang_at) ? $item->detail_hilang_created_at->format('Y-m-d H:i:s') : null,
                        ];

                        $log[] = [
                            ModelsHistory::field_name() => $item,
                            ModelsHistory::field_status() => ProcessType::OpnameCapture,
                            ModelsHistory::field_created_by() => auth()->user()->name ?? 'System',
                            ModelsHistory::field_created_at() => $tgl,
                            ModelsHistory::field_description() => ProcessType::getDescription(ProcessType::OpnameCapture),
                        ];
                    }

                    foreach(array_chunk($data, env('TRANSACTION_CHUNK')) as $save_transaksi){
                        OpnameDetail::insert($save_transaksi);
                    }

                    foreach(array_chunk($log, env('TRANSACTION_CHUNK')) as $log_transaksi){
                        ModelsHistory::insert($log_transaksi);
                    }
                }

                Alert::create();

            DB::commit();

        } catch (\Throwable $th) {
            DB::rollBack();
            Alert::error($th->getMessage());
            return $th->getMessage();
        }

        return $check;
    }

    private function checkKetemu($item){

        if(in_array($item->detail_status_proses, [ProcessType::Pending, ProcessType::Hilang])){
            return BooleanType::Yes;
        }

        if (in_array($item->detail_status_transaksi, [TransactionType::Retur, TransactionType::Rewash])) {
            return BooleanType::Yes;
        }

        return BooleanType::No;
    }
}