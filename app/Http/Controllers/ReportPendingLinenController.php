<?php

namespace App\Http\Controllers;

use App\Dao\Enums\CuciType;
use App\Dao\Enums\ProcessType;
use App\Dao\Enums\RegisterType;
use App\Dao\Models\Jenis;
use App\Dao\Models\Rs;
use App\Dao\Models\Ruangan;
use App\Dao\Models\ViewDetailLinen;
use App\Dao\Models\ViewLog;
use App\Dao\Repositories\DetailRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportPendingLinenController extends MinimalController
{
    public $data;

    public function __construct(DetailRepository $repository)
    {
        self::$repository = self::$repository ?? $repository;
    }

    protected function beforeForm()
    {
        $rs = Rs::getOptions();
        $jenis = Jenis::getOptions();
        $ruangan = Ruangan::getOptions();
        $cuci = CuciType::getOptions();
        $register = RegisterType::getOptions();

        self::$share = [
            'rs' => $rs,
            'ruangan' => $ruangan,
            'jenis' => $jenis,
            'register' => $register,
            'cuci' => $cuci,
        ];
    }

    private function getQuery($request)
    {
        $query = self::$repository->getPrint()
            ->addSelect([DB::raw('view_detail_linen.*'), ViewLog::field_status()])
            ->leftJoinRelationship(HAS_LOG)
            ->where(ViewDetailLinen::field_status_process(), ProcessType::Pending);

        if ($start_date = $request->start_pending) {
            $query = $query->whereDate(ViewDetailLinen::field_pending_create(), '>=', $start_date);
        }

        if ($end_date = $request->end_pending) {
            $query = $query->whereDate(ViewDetailLinen::field_pending_create(), '<=', $end_date);
        }

        return $query->get();
    }

    public function getPrint(Request $request)
    {
        set_time_limit(0);
        $rs = Rs::find(request()->get(ViewDetailLinen::field_rs_id()));

        $this->data = $this->getQuery($request);

        return moduleView(modulePathPrint(), $this->share([
            'data' => $this->data,
            'rs' => $rs,
        ]));
    }
}
