<?php

namespace App\Http\Controllers;

use App\Dao\Models\Jenis;
use App\Dao\Models\Mutasi;
use App\Dao\Models\Rs;
use App\Dao\Models\ViewDetailLinen;
use App\Dao\Models\ViewInvoice;
use App\Dao\Repositories\MutasiRepository;
use App\Http\Requests\MutasiReportRequest;

class ReportMutasiController extends MinimalController
{
    public $data;

    public function __construct(MutasiRepository $repository)
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

    private function getQueryBersih($request)
    {
        $query = self::$repository->getPrint();

        if ($start_date = $request->start_rekap) {
            $query = $query->where(ViewInvoice::field_tanggal(), '>=', $start_date);
        }

        if ($end_date = $request->end_rekap) {
            $query = $query->where(ViewInvoice::field_tanggal(), '<=', $end_date);
        }

        if ($rs_id = $request->rs_id) {
            $query = $query->where(ViewInvoice::field_rs_id(), $rs_id);
        }

        return $query->get();
    }

    private function getQueryKotor($request)
    {
        $query = self::$repository->getPrint();

        if ($start_date = $request->start_rekap) {
            $query = $query->where(ViewInvoice::field_tanggal(), '>=', $start_date);
        }

        if ($end_date = $request->end_rekap) {
            $query = $query->where(ViewInvoice::field_tanggal(), '<=', $end_date);
        }

        if ($rs_id = $request->rs_id) {
            $query = $query->where(ViewInvoice::field_rs_id(), $rs_id);
        }

        return $query->get();
    }

    private function getQuery($request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        $kotor = $this->getQueryKotor($request);
        $bersih = $this->getQueryBersih($request);

        if($awal = request()->get('start_date')){
            $bersih = $bersih->where(Mutasi::field_tanggal(), '>=', $awal);
            $kotor = $kotor->where(Mutasi::field_tanggal(), '>=', $awal);
        }

        if($akhir = request()->get('end_date')){
            $bersih = $bersih->where(Mutasi::field_tanggal(), '<=', $akhir);
            $kotor = $kotor->where(Mutasi::field_tanggal(), '<=', $akhir);
        }

        if ($rs_id = request()->get(ViewDetailLinen::field_rs_id())) {
            $bersih = $bersih->where(Mutasi::field_rs_id(), $rs_id);
            $kotor = $kotor->where(Mutasi::field_rs_id(), $rs_id);
        }

        if ($linen_id = request()->get(ViewDetailLinen::field_id())) {
            $bersih = $bersih->where(Mutasi::field_linen_id(), $linen_id);
            $kotor = $kotor->where(Mutasi::field_linen_id(), $linen_id);
        }

        $bersih = $bersih->get();
        $kotor = $kotor->get();

        return [
            'bersih' => $bersih,
            'kotor' => $kotor,
        ];

    }

    public function getPrint(MutasiReportRequest $request){
        set_time_limit(0);
        $rs_id = intval($request->view_rs_id);
        $rs = Rs::find($rs_id);

        $this->data = $this->getQuery($request);

        return moduleView(modulePathPrint(), $this->share(array_merge([
            'rs' => $rs,
        ], $this->getQuery($request))));
    }
}