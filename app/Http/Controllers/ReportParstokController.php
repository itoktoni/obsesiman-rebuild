<?php

namespace App\Http\Controllers;

use App\Dao\Models\Jenis;
use App\Dao\Models\Kategori;
use App\Dao\Models\Rs;
use App\Dao\Models\ViewDetailLinen;
use App\Dao\Repositories\JenisRepository;
use Illuminate\Http\Request;

class ReportParstokController extends MinimalController
{
    public $data;

    public function __construct(JenisRepository $repository)
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

    private function getQuery($request){
        return self::$repository->getParstok()->get();
    }

    public function getPrint(Request $request){
        set_time_limit(0);
        $rs = Rs::find(request()->get(Jenis::field_rs_id()));

        $this->data = $this->getQuery($request);

        return moduleView(modulePathPrint(), $this->share([
            'data' => $this->data,
            'rs' => $rs
        ]));
    }
}