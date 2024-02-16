<?php

namespace App\Http\Controllers;

use App\Dao\Models\Rs;
use App\Dao\Models\Transaksi;
use App\Dao\Models\User;
use App\Dao\Repositories\TransaksiRepository;
use App\Http\Requests\DeliveryReportRequest;
use Illuminate\Support\Facades\DB;

class ReportDetailPengirimanBersihController extends MinimalController
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
        $query =  self::$repository->getDetailBersih()
            ->addSelect(['*', DB::raw('user_delivery.name as user_delivery')])
            ->leftJoinRelationship('has_created_delivery', 'user_delivery');

        if ($start_date = $request->start_delivery) {
            $query = $query->where(Transaksi::field_report(), '>=', $start_date);
        }

        if ($end_date = $request->end_delivery) {
            $query = $query->where(Transaksi::field_report(), '<=', $end_date);
        }

        return $query->orderBy('view_linen_nama', 'ASC')->get();
    }

    public function getPrint(DeliveryReportRequest $request){
        set_time_limit(0);
        $rs = Rs::find(request()->get(Rs::field_primary()));

        $this->data = $this->getQuery($request);

        return moduleView(modulePathPrint(), $this->share([
            'data' => $this->data,
            'rs' => $rs
        ]));
    }
}