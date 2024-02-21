<?php

namespace App\Http\Controllers;

use App\Dao\Enums\BooleanType;
use App\Dao\Enums\FilterType;
use App\Dao\Enums\ProcessType;
use App\Dao\Enums\TransactionType;
use App\Dao\Models\Opname;
use App\Dao\Models\OpnameDetail;
use App\Dao\Models\User;
use App\Dao\Repositories\OpnameRepository;
use Illuminate\Http\Request;
use Plugins\Query;

class ReportOpnameDetailController extends MinimalController
{
    public $data;

    public function __construct(OpnameRepository $repository)
    {
        self::$repository = self::$repository ?? $repository;
    }

    protected function beforeForm(){

        $filter = FilterType::getOptions();

        self::$share = [
            'rs' => Query::getOpnameList(),
            'filter' => $filter,
        ];
    }

    private function getQuery($opname_id){
        $query = self::$repository->getOpnameByID($opname_id)
            ->where(OpnameDetail::field_ketemu(), BooleanType::Yes);

        if($status = request()->get('status')){

            if($status == FilterType::Kotor){
                $query->where(OpnameDetail::field_transaksi(), TransactionType::Kotor)
                    ->whereNotIn(OpnameDetail::field_proses(), [ProcessType::Pending, ProcessType::Hilang]);
            }

            if($status == FilterType::Retur){
                $query->where(OpnameDetail::field_transaksi(), TransactionType::Retur)
                    ->whereNotIn(OpnameDetail::field_proses(), [ProcessType::Pending, ProcessType::Hilang]);
            }

            if($status == FilterType::Rewash){
                $query->where(OpnameDetail::field_transaksi(), TransactionType::Rewash)
                    ->whereNotIn(OpnameDetail::field_proses(), [ProcessType::Pending, ProcessType::Hilang]);
            }

            if($status == FilterType::Pending){
                $query->where(OpnameDetail::field_proses(), ProcessType::Pending);
            }

            if($status == FilterType::Hilang){
                $query->where(OpnameDetail::field_proses(), ProcessType::Hilang);
            }

            if($status == FilterType::ScanRs){
                $query->where(OpnameDetail::field_scan_rs(), BooleanType::Yes);
            }

            if($status == FilterType::BelumRegister){
                $query->where(OpnameDetail::field_transaksi(), BooleanType::No);
            }
        }

        return $query;
    }

    public function getPrint(Request $request){
        set_time_limit(0);

        $this->data = $this->getQuery($request->opname_id)->get();
        $opname = Opname::with(['has_rs'])->find(request()->get(Opname::field_primary()));

        return moduleView(modulePathPrint(), $this->share([
            'data' => $this->data,
            'opname' => $opname
        ]));
    }
}