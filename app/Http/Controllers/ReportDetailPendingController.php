<?php

namespace App\Http\Controllers;

use App\Dao\Models\Rs;
use App\Dao\Models\Transaksi;
use App\Dao\Models\User;
use App\Dao\Repositories\TransaksiRepository;
use App\Http\Requests\TransactionReportRequest;
use Illuminate\Http\Request;

class ReportDetailPendingController extends MinimalController
{
    public $data;

    public function __construct(TransaksiRepository $repository)
    {
        self::$repository = self::$repository ?? $repository;
    }

    protected function beforeForm(){

        $rs = Rs::getOptions();
        $user = User::getOptions();

        self::$share = [
            'user' => $user,
            'rs' => $rs,
        ];
    }

    private function getQuery($request){
        $query = self::$repository->getQueryReportTransaksi()
            ->whereNotNull(Transaksi::field_pending_in())
            ->leftJoinRelationship(HAS_RS);

        if($start = $request->pending_in)
        {
            $query = $query->whereDate(Transaksi::field_created_at(), '>=', $start);
        }

        if($end = $request->pending_out)
        {
            $query = $query->whereDate(Transaksi::field_created_at(), '<=', $end);
        }

        return $query->get();
    }

    public function getPrint(Request $request){
        set_time_limit(0);
        $rs = Rs::find(request()->get(Rs::field_primary()));

        $this->data = $this->getQuery($request);

        return moduleView(modulePathPrint(), $this->share([
            'data' => $this->data,
            'rs' => $rs
        ]));
    }
}