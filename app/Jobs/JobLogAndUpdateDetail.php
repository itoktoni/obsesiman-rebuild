<?php

namespace App\Jobs;

use App\Dao\Enums\BooleanType;
use App\Dao\Enums\ProcessType;
use App\Dao\Enums\TransactionType;
use App\Dao\Models\Detail;
use App\Dao\Models\OpnameDetail;
use App\Dao\Models\Transaksi;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Laravie\SerializesQuery\Eloquent;
use Plugins\History;

class JobLogAndUpdateDetail implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $transaksi_id;
    public $status_transaksi;
    public $status_process;

    public function __construct($transaksi_id, $status_transaksi, $status_process)
    {
        $this->transaksi_id = $transaksi_id;
        $this->status_transaksi = $status_transaksi;
        $this->status_process = $status_process;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ini_set('memory_limit', '128M');
        ini_set('max_execution_time', 500); // time in seconds

        $rfid = Transaksi::select(Transaksi::field_rfid())
            ->where(Transaksi::field_key(), $this->transaksi_id)
            ->get()->pluck(Transaksi::field_rfid())->toArray() ?? [];

            // foreach (array_chunk($rfid, env('TRANSACTION_CHUNK')) as $save_detail) {
            //     Detail::whereIn(Detail::field_primary(), $save_detail)
            //     ->update([
            //         Detail::field_status_transaction() => $this->status_transaksi,
            //         Detail::field_status_process() => $this->status_process,
            //         Detail::field_status_history() => $this->status_transaksi,
            //         Detail::field_updated_at() => date('Y-m-d H:i:s'),
            //         Detail::field_updated_by() => auth()->user()->id ?? 101,
            //     ]);
            // }

        History::bulk($rfid, $this->status_transaksi);
    }
}
