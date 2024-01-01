<?php

namespace App\Http\Controllers;

use App\Dao\Enums\TransactionType;
use App\Dao\Models\Detail;
use App\Dao\Models\Jenis;
use App\Dao\Models\Opname;
use App\Dao\Models\OpnameDetail;
use App\Dao\Models\Rs;
use App\Dao\Models\Transaksi;
use App\Dao\Models\User;
use App\Dao\Models\ViewDetailLinen;
use App\Dao\Models\ViewMutasi;
use App\Dao\Models\ViewOpname;
use App\Dao\Repositories\OpnameRepository;
use App\Http\Requests\OpnameReportRequest;
use App\Http\Requests\RekapReportRequest;
use Illuminate\Support\Carbon;
use Plugins\Query;

class ReportRekapOpnameController extends MinimalController
{
    public $data;

    public function __construct(OpnameRepository $repository)
    {
        self::$repository = self::$repository ?? $repository;
    }

    protected function beforeForm()
    {
        $opname = Query::getOpnameList();
        $jenis = Jenis::getOptions();

        self::$share = [
            'jenis' => $jenis,
            'opname' => $opname,
        ];
    }

    private function getQueryKotor($request)
    {
        $query = self::$repository->getDetailAllKotor([TransactionType::Rewash]);

        if ($start_date = $request->start_rekap) {
            $query = $query->whereDate(Transaksi::field_created_at(), '>=', $start_date);
        }

        if ($end_date = $request->end_rekap) {
            $query = $query->whereDate(Transaksi::field_created_at(), '<=', $end_date);
        }

        return $query->get();
    }

    private function getQueryBersih($request)
    {
        $query = self::$repository->getDetailAllBersih([TransactionType::BersihRewash]);

        if ($start_date = $request->start_rekap) {
            $bersih_from = Carbon::createFromFormat('Y-m-d', $start_date) ?? false;
            if ($bersih_from) {
                $query = $query->where(Transaksi::field_report(), '>=', $bersih_from->addDay(1)->format('Y-m-d'));
            }
        }

        if ($end_date = $request->end_rekap) {
            $bersih_to = Carbon::createFromFormat('Y-m-d', $end_date) ?? false;
            if ($bersih_to) {
                $query = $query->where(Transaksi::field_report(), '<=', $bersih_to->addDay(1)->format('Y-m-d'));
            }
        }

        return $query->get();
    }

    public function getPrint(OpnameReportRequest $request)
    {
        set_time_limit(0);
        $location = $linen = $lawan = $nama = [];

        $this->data = Opname::with([
            'has_rs',
        ])->find($request->opname_id);

        $opname = ViewOpname::where(Opname::field_primary(), $request->opname_id)
        ->get();

        $rs = $this->data->has_rs ?? false;

        if ($rs) {
            $location = $opname->pluck('ruangan_nama', 'ruangan_id')->unique();
            $linen = $opname->pluck('jenis_nama', 'jenis_id')->unique();
        }

        return moduleView(modulePathPrint(), $this->share([
            'data' => $this->data,
            'rs' => $rs,
            'opname' => $opname,
            'location' => $location,
            'linen' => $linen,
        ]));
    }
}
