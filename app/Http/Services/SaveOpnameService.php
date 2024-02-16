<?php

namespace App\Http\Services;

use App\Dao\Enums\BooleanType;
use App\Dao\Enums\OpnameType;
use App\Dao\Enums\ProcessType;
use App\Dao\Enums\SyncType;
use App\Dao\Enums\TransactionType;
use App\Dao\Models\OpnameDetail;
use Illuminate\Support\Facades\DB;
use Plugins\History as PluginsHistory;
use Plugins\Notes;

class SaveOpnameService
{
    public function save($opname_id, $data)
    {
        $check = false;
        try {

            DB::beginTransaction();
            $sent = [];
            $scan_rfid = $data['rfid'];
            $data_opname = $data['opname'];
            $code = $data['code'];
            $waktu = date('Y-m-d H:i:s');
            $insert_register = $scan_rs = false;

            foreach($scan_rfid as $rfid){
                $detail = isset($data_opname[$rfid]) ? $data_opname[$rfid] : false;
                $item = [
                    OpnameDetail::field_rfid() => $rfid,
                    OpnameDetail::field_opname() => $opname_id,
                    OpnameDetail::field_code() => $code,
                    OpnameDetail::field_register() => BooleanType::No,
                    OpnameDetail::field_updated_at() => date('Y-m-d H:i:s'),
                    OpnameDetail::field_updated_by() => auth()->user()->id,
                    OpnameDetail::field_transaksi() => TransactionType::Unknown,
                    OpnameDetail::field_proses() => ProcessType::Unknown,
                    OpnameDetail::field_scan_rs() => BooleanType::Yes,
                    OpnameDetail::field_ketemu() => BooleanType::Yes,
                ];

                if (!$detail) {
                    $item = array_merge($item, [
                        OpnameDetail::field_waktu() => $waktu,
                    ]);

                    $insert_register[] = $item;
                    $item = array_merge($item, [
                        OpnameDetail::field_sync() => BooleanType::Yes,
                    ]);

                } else if($detail->opname_detail_ketemu == BooleanType::Yes){
                    $item = array_merge($item, [
                        OpnameDetail::field_register() => BooleanType::Yes,
                        OpnameDetail::field_transaksi() => $detail->opname_detail_transaksi,
                        OpnameDetail::field_proses() => $detail->opname_detail_proses,
                        OpnameDetail::field_scan_rs() => BooleanType::No,
                        OpnameDetail::field_ketemu() => BooleanType::No,
                        OpnameDetail::field_waktu() => $detail->opname_detail_waktu,
                        OpnameDetail::field_sync() => BooleanType::No,
                    ]);
                } else if($detail->opname_detail_ketemu == BooleanType::No){
                    $item = array_merge($item, [
                        OpnameDetail::field_waktu() => $waktu,
                        OpnameDetail::field_sync() => BooleanType::Yes,
                    ]);

                    $scan_rs[] = $rfid;
                }

                $sent[] = $item;
            }

            if($insert_register){
                OpnameDetail::insert($insert_register);
            }

            if($scan_rs){
                OpnameDetail::whereIn(OpnameDetail::field_rfid(), $scan_rs)
                ->update([
                    OpnameDetail::field_ketemu() => BooleanType::Yes,
                    OpnameDetail::field_scan_rs() => BooleanType::Yes,
                    OpnameDetail::field_waktu() => $waktu,
                ]);

                PluginsHistory::bulk($scan_rs, ProcessType::Opname, 'Ketemu ketika Opname');
            }

            DB::commit();
           return Notes::create($sent);

        } catch (\Throwable $th) {
            DB::rollBack();
            return Notes::error($th->getMessage());
        }

        return Notes::error('Unknown');
    }
}
