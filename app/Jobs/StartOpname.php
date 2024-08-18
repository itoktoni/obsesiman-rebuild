<?php

namespace App\Jobs;

use App\Dao\Enums\BooleanType;
use App\Dao\Enums\ProcessType;
use App\Dao\Enums\TransactionType;
use App\Dao\Models\Detail;
use App\Dao\Models\OpnameDetail;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Laravie\SerializesQuery\Eloquent;

class StartOpname implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $opnameID;
    public $userID;
    public $chunkIndex;
    public $chunkSize;
    public $query;

    public function __construct($opnameID, $userID, $query, $chunkIndex, $chunkSize)
    {
        $this->opnameID = $opnameID;
        $this->chunkIndex = $chunkIndex;
        $this->chunkSize = $chunkSize;
        $this->userID = $userID;
        $this->query = $query;
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

        $query = Eloquent::unserialize($this->query);

        if ($this->chunkSize != 1) {
            $data_rfid = $query->skip(($this->chunkIndex - 1) * $this->chunkSize)
            ->take($this->chunkSize)
            ->limit($this->chunkSize)
            ->get();

            $tgl = date('Y-m-d H:i:s');

            $log = [];
            if ($data_rfid) {
                $id = $this->userID;

                foreach ($data_rfid as $item) {

                    $ketemu = $this->checkKetemu($item);
                    $data[] = [
                        OpnameDetail::field_rfid() => $item->detail_rfid,
                        OpnameDetail::field_transaksi() => $item->detail_status_transaksi,
                        OpnameDetail::field_proses() => $item->detail_status_proses,
                        OpnameDetail::field_created_at() => $tgl,
                        OpnameDetail::field_created_by() => $id,
                        OpnameDetail::field_updated_at() => !empty($item->detail_updated_at) ? $item->detail_updated_at : null,
                        OpnameDetail::field_updated_by() => $id,
                        OpnameDetail::field_waktu() => $tgl,
                        OpnameDetail::field_ketemu() => $ketemu,
                        OpnameDetail::field_opname() => $this->opnameID,
                        OpnameDetail::field_pending() => !empty($item->detail_pending_created_at) ? $item->detail_pending_created_at->format('Y-m-d H:i:s') : null,
                        OpnameDetail::field_hilang() => !empty($item->detail_hilang_created_at) ? $item->detail_hilang_created_at->format('Y-m-d H:i:s') : null,
                    ];

                }

                OpnameDetail::insert($data);
            }
        }

    }

    private function checkKetemu($item)
    {

        if (in_array($item->detail_status_proses, [ProcessType::Pending, ProcessType::Hilang])) {
            return BooleanType::Yes;
        }

        if (in_array($item->detail_status_transaksi, [TransactionType::Retur, TransactionType::Rewash])) {
            return BooleanType::Yes;
        }

        return BooleanType::No;
    }
}
