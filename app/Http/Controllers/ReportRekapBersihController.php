<?php

namespace App\Http\Controllers;

use App\Dao\Enums\TransactionType;
use App\Dao\Models\Rs;
use App\Dao\Models\User;
use App\Dao\Models\ViewDetailLinen;
use App\Dao\Repositories\TransaksiRepository;
use App\Http\Requests\TransactionReportRequest;

class ReportRekapBersihController extends MinimalController
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

    private function getQueryKotor($request){
        return self::$repository->getDetailKotor()->get();
    }

    private function getQueryBersih($request){
        return self::$repository->getDetailKotor(TransactionType::BersihKotor)->get();
    }

    public function getPrint(TransactionReportRequest $request){
        set_time_limit(0);
        $location = $linen = $lawan = [];

        $rs = Rs::with([HAS_RUANGAN, HAS_JENIS])->find(request()->get(Rs::field_primary()));
        $location = $rs->has_ruangan;
        $linen = $rs->has_jenis;

        $this->data = $this->getQueryKotor($request);
        $lawan = $this->getQueryBersih($request);

        if($this->data){
            if($location){
                $location = $this->data->mapToGroups(function ($item, $key) {
                    return [$item[ViewDetailLinen::field_ruangan_name()] => $item];
                })->sortKeys();
            }

            if($linen){
                $linen = $this->data->mapToGroups(function ($item, $key) {
                    return [$item[ViewDetailLinen::field_name()] => $item];
                })->sortKeys();;
            }
        }

        if($lawan){
            $lawan = $lawan->mapToGroups(function ($item, $key) {
                return [$item[ViewDetailLinen::field_name()] => $item];
            });
        }

        return moduleView(modulePathPrint(), $this->share([
            'data' => $this->data,
            'rs' => $rs,
            'location' => $location,
            'linen' => $linen,
            'lawan' => $lawan,
        ]));
    }
}