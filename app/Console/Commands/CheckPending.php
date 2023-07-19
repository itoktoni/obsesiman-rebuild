<?php

namespace App\Console\Commands;

use App\Dao\Enums\ProcessType;
use App\Dao\Models\Detail;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Plugins\History as PluginsHistory;

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

        $outstanding = Detail::whereDate(Detail::UPDATED_AT, '>=', Carbon::now()->subMinutes(1440)->toDateString())
            ->whereDate(Detail::UPDATED_AT, '<', Carbon::now()->toDateString())
            ->whereNotIn(Detail::field_status_transaction(), BERSIH)
            ->where(Detail::field_status_process(), '!=', ProcessType::Pending)
            ->get();

        if ($outstanding) {

            $rfid = $outstanding->pluck(Detail::field_primary());

            PluginsHistory::bulk($rfid, ProcessType::Pending, 'RFID Pending');
            Detail::whereIn(Detail::field_primary(), $rfid)->update([
                Detail::field_status_process() => ProcessType::Pending,
                Detail::field_pending_created_at() => date('Y-m-d H:i:s'),
                Detail::field_pending_update_at() => date('Y-m-d H:i:s'),
            ]);
        }

        $this->info('The system has been check successfully!');
    }
}
