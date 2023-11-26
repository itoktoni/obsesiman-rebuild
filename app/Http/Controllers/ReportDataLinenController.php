<?php

namespace App\Http\Controllers;

use App\Dao\Models\Jenis;
use App\Dao\Models\Kategori;
use App\Dao\Models\Rs;
use App\Dao\Models\Ruangan;
use App\Dao\Models\ViewDetailLinen;
use App\Dao\Repositories\DetailRepository;
use App\Jobs\downloadReport;
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
        $ruangan = Ruangan::getOptions();
        $kategori = Kategori::getOptions();
        $jenis = Jenis::getOptions();

        self::$share = [
            'jenis' => $jenis,
            'kategori' => $kategori,
            'ruangan' => $ruangan,
            'rs' => $rs,
        ];
    }

    private function getQuery($request){
        return self::$repository->getPrintDataMaster()->get();
    }

    public function getPrint(Request $request){
        set_time_limit(0);
        $rs = Rs::find(request()->get(ViewDetailLinen::field_rs_id()));

        $this->data = [];
        if($request->action != 'export'){
            $this->data = $this->getQuery($request);
        }

        return moduleView(modulePathPrint(), $this->share([
            'data' => $this->data,
            'rs' => $rs,
        ]));
    }
}