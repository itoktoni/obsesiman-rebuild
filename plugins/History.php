<?php

namespace Plugins;

use App\Dao\Enums\LogType;
use App\Dao\Enums\ProcessType;
use App\Dao\Models\History as ModelsHistory;

class History
{
    public static function log($rfid, $status, $message = null)
    {
        ModelsHistory::updateOrCreate([
            ModelsHistory::field_name() => $rfid,
        ], [
            ModelsHistory::field_name() => $rfid,
            ModelsHistory::field_status() => $status,
            ModelsHistory::field_created_at() => date('Y-m-d H:i:s'),
            ModelsHistory::field_created_by() => auth()->user()->name,
            ModelsHistory::field_description() => LogType::getDescription($status),
        ]);
    }

    public static function bulk($rfid, $status, $message = null)
    {
        $log = [];
        $name = auth()->user()->name ?? 'System';
        foreach($rfid as $item){
            $log[] = [
                ModelsHistory::field_name() => $item,
                ModelsHistory::field_status() => $status,
                ModelsHistory::field_created_by() =>  $name,
                ModelsHistory::field_created_at() => date('Y-m-d H:i:s'),
                ModelsHistory::field_description() => LogType::getDescription($status),
            ];
        }

        if(!empty($log)){

            foreach(array_chunk($log, env('TRANSACTION_CHUNK')) as $save_log){
                ModelsHistory::upsert($save_log, [ModelsHistory::field_name()]);
            }

        }
    }
}
