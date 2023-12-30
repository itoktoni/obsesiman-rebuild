<?php

namespace App\Http\Controllers;

use App\Dao\Enums\CuciType;
use App\Dao\Models\Rs;
use App\Dao\Models\User;
use App\Dao\Models\ViewInvoice;
use App\Dao\Repositories\ViewInvoiceRepository;
use App\Http\Requests\InvoiceReportRequest;

class ReportInvoiceController extends MinimalController
{
    public $data;

    public function __construct(ViewInvoiceRepository $repository)
    {
        self::$repository = self::$repository ?? $repository;
    }

    protected function beforeForm()
    {
        $rs = Rs::getOptions();
        $user = User::getOptions();
        $status = CuciType::getOptions([
            CuciType::Cuci,
            CuciType::Sewa,
        ]);

        self::$share = [
            'status' => $status,
            'user' => $user,
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

    public function getPrint(InvoiceReportRequest $request)
    {
        set_time_limit(0);
        $tanggal = $linen = $lawan = $nama = [];

        $rs = Rs::with([HAS_RUANGAN, HAS_JENIS])->find(request()->get(Rs::field_primary()));
        $linen = $rs->has_jenis;

        $this->data = $this->getQueryBersih($request);

        if ($this->data) {
            $tanggal = $this->data->mapWithKeys(function ($item) {
                return [$item->view_tanggal => $item];
            })->sort();

            $linen = $this->data->mapWithKeys(function ($item) {
                return [$item->view_linen_id => strtoupper($item->view_linen_nama)];
            })->sort();
        }

        return moduleView(modulePathPrint(), $this->share([
            'data' => $this->data,
            'rs' => $rs,
            'tanggal' => $tanggal,
            'linen' => $linen,
        ]));
    }
}
