<?php

namespace App\Http\Services;

use App\Dao\Enums\BooleanType;
use App\Dao\Enums\ProcessType;
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
            $original = $data['rfid'];
            $rfid = $data['data'];

            if(!empty($original)){
                foreach(array_chunk($original, env('TRANSACTION_CHUNK', 500)) as $chunk){
                    foreach($chunk as $detail){
                        $add = [
                            OpnameDetail::field_rfid() => $detail,
                            OpnameDetail::field_code() => $opname_id,
                            OpnameDetail::field_ketemu() => BooleanType::Yes,
                            OpnameDetail::field_register() => BooleanType::No,
                            OpnameDetail::field_waktu() => date('y-m-d H:i:s'),
                            OpnameDetail::field_updated_at() => date('Y-m-d H:i:s'),
                            OpnameDetail::field_updated_by() => auth()->user()->id,
                        ];

                        if (isset($rfid[$detail])) {

                            $detail = $rfid[$detail];
                            $add = array_merge($add, [
                                OpnameDetail::field_transaksi() => $detail->field_status_transaction,
                                OpnameDetail::field_proses() => $detail->field_status_process,
                            ]);
                        }

                        $update = OpnameDetail::where(OpnameDetail::field_rfid(), $detail)
                            ->where(OpnameDetail::field_proses(), $detail->field_status_process);

                        if($update->count() > 0) {
                            $update->update($add);
                        } else {
                            OpnameDetail::create($add);
                        }

                        $sent[] = $add;
                    }

                }
            }

            PluginsHistory::bulk($rfid->keys(), ProcessType::Opname, 'Ketemu ketika Opname');

            DB::commit();
           return Notes::create($sent);

        } catch (\Throwable $th) {
            DB::rollBack();
            return Notes::error($th->getMessage());
        }

        return Notes::error('Unknown');
    }
}
