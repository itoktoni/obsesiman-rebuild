<?php

namespace App\Http\Controllers;

use App\Dao\Models\Jenis;
use App\Dao\Models\Mutasi;
use App\Dao\Models\Rs;
use App\Dao\Models\ViewDetailLinen;
use App\Dao\Models\ViewInvoice;
use App\Dao\Repositories\MutasiRepository;
use App\Http\Requests\MutasiReportRequest;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class ReportMutasiController extends MinimalController
{
    public $data;

    public function __construct(MutasiRepository $repository)
    {
        self::$repository = self::$repository ?? $repository;
    }

    protected function beforeForm(){

        $rs = Rs::getOptions();
        $jenis = Jenis::getOptions();

        self::$share = [
            'jenis' => $jenis,
            'rs' => $rs,
        ];
    }

    private function getQueryBersih($request){

        $query = DB::table('view_rekap_bersih')->where('view_rs_id', $request->view_rs_id);

        if ($start_date = $request->start_date) {
            $bersih_from = Carbon::createFromFormat('Y-m-d', $start_date) ?? false;
            if($bersih_from){
                $query = $query->where('view_tanggal', '>=', $bersih_from->addDay(1)->format('Y-m-d'));
            }
        }

        if ($end_date = $request->end_date) {
            $bersih_to = Carbon::createFromFormat('Y-m-d', $end_date) ?? false;
            if($bersih_to){
                $query = $query->where('view_tanggal', '<=', $bersih_to->addDay(1)->format('Y-m-d'));
            }
        }

        if ($view_linen_id = $request->view_linen_id) {
            $query = $query->where('view_linen_id', $view_linen_id);
        }

        return $query->get();
    }

    private function getQueryKotor($request){

        $query = DB::table('view_rekap_kotor')->where('view_rs_id', $request->view_rs_id);

        if ($start_date = $request->start_date) {
            $bersih_from = Carbon::createFromFormat('Y-m-d', $start_date) ?? false;
            if($bersih_from){
                $query = $query->where('view_tanggal', '>=', $bersih_from->format('Y-m-d'));
            }
        }

        if ($end_date = $request->end_date) {
            $bersih_to = Carbon::createFromFormat('Y-m-d', $end_date) ?? false;
            if($bersih_to){
                $query = $query->where('view_tanggal', '<=', $bersih_to->format('Y-m-d'));
            }
        }

        if ($view_linen_id = $request->view_linen_id) {
            $query = $query->where('view_linen_id', $view_linen_id);
        }

        return $query->get();
    }


    private function getQuery($request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        $kotor = $this->getQueryKotor($request);
        $bersih = $this->getQueryBersih($request);

        return [
            'bersih' => $bersih,
            'kotor' => $kotor,
        ];
    }

    public function getPrint(MutasiReportRequest $request){
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        $rs_id = intval($request->view_rs_id);
        $rs = Rs::with([HAS_RUANGAN, HAS_JENIS])->find($rs_id);

        $linen = $rs->has_jenis;

        if($request->view_linen_id){
            $linen = $linen->where('jenis_id', $request->view_linen_id);
        }

        $this->data = $this->getQuery($request);
        $tanggal = CarbonPeriod::create($request->start_date, $request->end_date);

        return moduleView(modulePathPrint(), $this->share(array_merge([
            'rs' => $rs,
            'linen' => $linen,
            'tanggal' => $tanggal,
        ], $this->data)));
    }
}