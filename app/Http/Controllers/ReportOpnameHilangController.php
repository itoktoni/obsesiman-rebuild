<?php

namespace App\Http\Controllers;

use App\Dao\Enums\BooleanType;
use App\Dao\Models\Opname;
use App\Dao\Models\OpnameDetail;
use App\Dao\Models\Rs;
use App\Dao\Models\User;
use App\Dao\Repositories\OpnameRepository;
use Illuminate\Http\Request;

class ReportOpnameHilangController extends MinimalController
{
    public $data;

    public function __construct(OpnameRepository $repository)
    {
        self::$repository = self::$repository ?? $repository;
    }

    protected function beforeForm(){

        $rs = Opname::with(['has_rs'])
            ->where(Opname::field_start(), '>=', now()->addMonth(-6))
            ->get()->mapWithKeys(function($item){
                $rs = $item->has_rs->field_name ?? 'RS';
                return [
                    $item->opname_id =>
                    $item->opname_id.' | '.
                    $rs.' = '.
                    $item->field_start.'-'.
                    $item->field_end
                ];
            });

        self::$share = [
            'rs' => $rs,
        ];
    }

    private function getQuery($opname_id){
        $query = self::$repository->getOpnameByID($opname_id)
            ->whereNull(OpnameDetail::field_ketemu());

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