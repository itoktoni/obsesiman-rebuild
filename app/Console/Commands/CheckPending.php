<?php

namespace App\Console\Commands;

use App\Dao\Enums\LogType;
use App\Dao\Enums\ProcessType;
use App\Dao\Enums\TransactionType;
use App\Dao\Models\Detail;
use App\Dao\Models\Transaksi;
use App\Dao\Models\ViewDetailLinen;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CheckPending extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Commands check is there any pending rfid';

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

        $outstanding = Transaksi::query()
            ->select(Transaksi::field_rfid())
            ->where(Transaksi::field_created_at(), '<=', Carbon::now()->subMinutes(1440)->toDateTimeString())
            ->where(Transaksi::field_status_transaction(), '!=', TransactionType::Register)
            ->whereNull(Transaksi::field_report())
            ->whereNull(Transaksi::field_pending_in())
            ->limit(env('TRANSACTION_CHUNK', 200))
            ->get();

        if ($outstanding) {

            foreach ($outstanding as $detail) {
                Detail::where(Detail::field_primary(), $detail->transaksi_rfid)->update([
                    Detail::field_status_process() => ProcessType::Pending,
                    Detail::field_status_history() => LogType::Pending,
                    Detail::field_pending_created_at() => date('Y-m-d H:i:s'),
                    Detail::field_pending_updated_at() => date('Y-m-d H:i:s'),
                ]);

                Transaksi::where(Transaksi::field_rfid(), $detail->transaksi_rfid)
                ->whereNull(Transaksi::field_delivery())
                ->whereNull(Transaksi::field_pending_in())
                ->update([
                    Transaksi::field_pending_in() => date('Y-m-d H:i:s')
                ]);
            }
        }

        $this->info('The system has been check successfully!');
    }
}
