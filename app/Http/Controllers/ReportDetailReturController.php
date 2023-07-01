<?php

namespace App\Http\Controllers;

use App\Dao\Enums\TransactionType;
use App\Dao\Models\Rs;
use App\Dao\Models\User;
use App\Dao\Repositories\TransaksiRepository;
use App\Http\Requests\TransactionReportRequest;

class ReportDetailReturController extends MinimalController
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
        return self::$repository
            ->getDetailKotor(TransactionType::Retur)
            ->leftJoinRelationship(HAS_RETUR)
            ->leftJoinRelationship(HAS_CUCI)
            ->get();
    }

    public function getPrint(TransactionReportRequest $request){
        set_time_limit(0);
        $rs = Rs::find(request()->get(Rs::field_primary()));

        $this->data = $this->getQuery($request);

        return moduleView(modulePathPrint(), $this->share([
            'data' => $this->data,
            'rs' => $rs
        ]));
    }
}