<?php

namespace App\Http\Controllers;

use App\Dao\Models\Rs;
use App\Dao\Models\ViewDetailLinen;
use App\Dao\Repositories\DetailRepository;
use Illuminate\Http\Request;

class ReportDataLinenController extends MinimalController
{
    public $data;

    public function __construct(DetailRepository $repository)
    {
        self::$repository = self::$repository ?? $repository;
    }

    protected function beforeForm(){

        $rs = Rs::getOptions();

        self::$share = [
            'rs' => $rs,
        ];
    }

    private function getQuery($request){
        return self::$repository->getPrintDataMaster()->get();
    }

    public function getPrint(Request $request){
        set_time_limit(0);
        $rs = Rs::find(request()->get(ViewDetailLinen::field_rs_id()));

        $this->data = $this->getQuery($request);

        return moduleView(modulePathPrint(), $this->share([
            'data' => $this->data,
            'rs' => $rs
        ]));
    }
}