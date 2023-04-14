<?php

namespace App\Http\Controllers;

use App\Dao\Enums\TransactionType;
use App\Dao\Models\Rs;
use App\Dao\Models\ViewDetailLinen;
use App\Dao\Repositories\TransaksiRepository;
use App\Http\Requests\ReportRequest;
use App\Http\Requests\TransactionReportRequest;
use Illuminate\Http\Request;

class ReportTransaksiController extends MinimalController
{
    public $data;

    public function __construct(TransaksiRepository $repository)
    {
        self::$repository = self::$repository ?? $repository;
    }

    protected function beforeForm(){

        $rs = Rs::getOptions();
        $status = TransactionType::getOptions([
            TransactionType::Kotor,
            TransactionType::Retur,
            TransactionType::Rewash,
            TransactionType::BersihKotor,
            TransactionType::BersihRetur,
            TransactionType::BersihRewash,
        ]);

        self::$share = [
            'status' => $status,
            'rs' => $rs,
        ];
    }

    private function getQuery($request){
        return self::$repository->getTransaksiDetail()->get();
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