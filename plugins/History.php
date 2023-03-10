<?php

namespace Plugins;

use App\Dao\Models\History as ModelsHistory;

class History
{
    public static function log($rfid, $status, $message = null)
    {
        ModelsHistory::create([
            ModelsHistory::field_name() => $rfid,
            ModelsHistory::field_status() => $status,
            ModelsHistory::field_created_at() => date('Y-m-d H:i:s'),
            ModelsHistory::field_created_by() => auth()->user()->name,
            ModelsHistory::field_description() => json_encode($message),
        ]);
    }
}
