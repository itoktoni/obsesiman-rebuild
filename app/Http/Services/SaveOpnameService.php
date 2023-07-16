<?php

namespace App\Http\Services;

use App\Dao\Enums\ProcessType;
use App\Dao\Models\OpnameDetail;
use Illuminate\Support\Facades\DB;
use Plugins\History as PluginsHistory;
use Plugins\Notes;

class SaveOpnameService
{
    public function save($opname_id, $rfid)
    {
        $check = false;
        try {

            DB::beginTransaction();

            if(!empty($rfid)){
                foreach($rfid->chunk(env('TRANSACTION_CHUNK', 500)) as $chunk){
                    foreach($chunk as $key => $detail){
                        OpnameDetail::where(OpnameDetail::field_rfid(), $key)
                        ->where(OpnameDetail::field_opname(), $opname_id)
                        ->update($detail);
                    }
                }
            }

            PluginsHistory::bulk($rfid->keys(), ProcessType::Opname, 'Ketemu ketika Opname');

            DB::commit();
           return Notes::create($rfid);

        } catch (\Throwable $th) {
            DB::rollBack();
            return Notes::error($th->getMessage());
        }

        return Notes::error('Unknown');
    }
}