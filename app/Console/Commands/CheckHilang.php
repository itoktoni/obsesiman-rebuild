<?php

namespace App\Console\Commands;

use App\Dao\Enums\ProcessType;
use App\Dao\Models\Detail;
use App\Dao\Models\Transaksi;
use App\Dao\Models\ViewDetailLinen;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Plugins\History;

class CheckHilang extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:hilang';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Commands To copy web frontend to vendor console';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        // $outstanding = Detail::whereDate(Detail::UPDATED_AT, '<=', Carbon::now()->subMinutes(4320)->toDateString())
        //     ->whereNotIn(Detail::field_status_transaction(), BERSIH)
        //     ->where(Detail::field_status_process(), '!=', ProcessType::Hilang)
        //     ->get();

            $outstanding = Transaksi::query()
                ->select(Transaksi::field_rfid())
                ->joinRelationship(HAS_DETAIL)
                ->whereDate(ViewDetailLinen::field_tanggal_update(), '<=', Carbon::now()->subMinutes(4320)->toDateString())
                ->whereNull(Transaksi::field_status_bersih())
                ->whereNotIn(ViewDetailLinen::field_status_trasaction(), BERSIH)
                ->where(ViewDetailLinen::field_status_process(), '!=', ProcessType::Hilang)
                ->get();

        if ($outstanding) {

            $rfid = $outstanding->pluck(Transaksi::field_rfid());

            History::bulk($rfid, ProcessType::Pending, 'RFID Hilang');
            Detail::whereIn(Detail::field_primary(), $rfid)->update([
                Detail::field_pending_created_at() => null,
                Detail::field_pending_updated_at() => null,
                Detail::field_status_process() => ProcessType::Hilang,
                Detail::field_hilang_updated_at() => date('Y-m-d H:i:s'),
                Detail::field_hilang_created_at() => date('Y-m-d H:i:s'),
            ]);
        }

        $this->info('The system has been check successfully!');
    }
}
