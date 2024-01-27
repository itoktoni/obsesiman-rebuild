<?php

namespace App\Http\Controllers;

use App\Dao\Enums\CuciType;
use App\Dao\Models\Jenis;
use App\Dao\Models\Rs;
use App\Dao\Models\User;
use App\Dao\Models\ViewInvoice;
use App\Dao\Repositories\ViewInvoiceRepository;
use App\Http\Requests\InvoiceReportRequest;
use Carbon\CarbonPeriod;
use DateInterval;
use DatePeriod;
use DateTime;

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
        ini_set('memory_limit', '512M');
        $tanggal = $linen = $lawan = $nama = [];

        $rs_id = request()->get(Rs::field_primary());
        $linen = Jenis::select([
            Jenis::field_primary(),
            Jenis::field_name(),
            Jenis::field_weight(),
        ])
        ->where(Jenis::field_rs_id(), $rs_id)
        ->orderBy(Jenis::field_name(), 'ASC')->get() ?? [];

        $rs = Rs::find($rs_id);

        $this->data = $this->getQueryBersih($request);

        $tanggal = CarbonPeriod::create($request->start_rekap, $request->end_rekap);

        return moduleView(modulePathPrint(), $this->share([
            'data' => $this->data,
            'rs' => $rs,
            'tanggal' => $tanggal,
            'linen' => $linen,
        ]));
    }
}
