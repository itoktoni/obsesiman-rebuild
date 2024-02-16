<?php

namespace App\Console\Commands;

use App\Dao\Enums\ProcessType;
use App\Dao\Models\Detail;
use App\Dao\Models\Transaksi;
use App\Dao\Models\ViewDetailLinen;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Plugins\History;

class DetailTotal extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'detail:total';

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
       $data = DB::table('view_transaksi_total')
        ->where('detail_tanggal_cek', '!=', date('Y-m-d'))
        ->orWhereNull('detail_tanggal_cek')
        ->limit(ENV('TRANSACTION_DETAIL_CEK', 50))
        ->get();

        Log::info($data);

        if ($data) {
            foreach($data as $item){
                Detail::where(Detail::field_primary(), $item->detail_rfid)->update([
                    Detail::field_total_bersih_kotor() => $item->qty_bersih_kotor,
                    Detail::field_total_bersih_retur() => $item->qty_bersih_retur,
                    Detail::field_total_bersih_rewash() => $item->qty_bersih_rewash,
                    Detail::field_total_kotor() => $item->qty_kotor,
                    Detail::field_total_retur() => $item->qty_retur,
                    Detail::field_total_rewash() => $item->qty_rewash,
                    Detail::field_total_cuci() => $item->qty_cuci,
                    Detail::field_cek() => date('Y-m-d'),
                ]);
            }
        }

        $this->info('The system has been check successfully!');
    }
}
