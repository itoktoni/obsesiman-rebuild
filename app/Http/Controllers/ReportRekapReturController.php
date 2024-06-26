<?php

namespace App\Http\Controllers;

use App\Dao\Enums\TransactionType;
use App\Dao\Models\Rs;
use App\Dao\Models\Transaksi;
use App\Dao\Models\User;
use App\Dao\Repositories\TransaksiRepository;
use App\Http\Requests\RekapReportRequest;
use Illuminate\Support\Carbon;

class ReportRekapReturController extends MinimalController
{
    public $data;

    public function __construct(TransaksiRepository $repository)
    {
        self::$repository = self::$repository ?? $repository;
    }

    protected function beforeForm()
    {
        $rs = Rs::getOptions();
        $user = User::getOptions();

        self::$share = [
            'user' => $user,
            'rs' => $rs,
        ];
    }

    private function getQueryKotor($request)
    {
        $query = self::$repository->getDetailAllKotor([TransactionType::Retur]);

        if ($start_date = $request->start_rekap) {
            $query = $query->whereDate(Transaksi::field_created_at(), '>=', $start_date);
        }

        if ($end_date = $request->end_rekap) {
            $query = $query->whereDate(Transaksi::field_created_at(), '<=', $end_date);
        }

        return $query->get();
    }

    private function getQueryBersih($request)
    {
        $query = self::$repository->getDetailAllBersih([TransactionType::BersihRetur]);

        if ($start_date = $request->start_rekap) {
            $bersih_from = Carbon::createFromFormat('Y-m-d', $start_date) ?? false;
            if ($bersih_from) {
                $query = $query->where(Transaksi::field_report(), '>=', $bersih_from->addDay(1)->format('Y-m-d'));
            }
        }

        if ($end_date = $request->end_rekap) {
            $bersih_to = Carbon::createFromFormat('Y-m-d', $end_date) ?? false;
            if ($bersih_to) {
                $query = $query->where(Transaksi::field_report(), '<=', $bersih_to->addDay(1)->format('Y-m-d'));
            }
        }

        return $query->get();
    }

    public function getPrint(RekapReportRequest $request)
    {
        set_time_limit(0);
        $location = $linen = $lawan = $nama = [];

        $rs = Rs::with([HAS_RUANGAN, HAS_JENIS])->find(request()->get(Rs::field_primary()));
        $location = $rs->has_ruangan;
        $linen = $rs->has_jenis;

        $kotor = $this->getQueryKotor($request);
        $bersih = $this->getQueryBersih($request);

        $this->data = $kotor->merge($bersih);

        if ($this->data) {
            $location = $this->data->mapWithKeys(function ($item) {
                return [$item->view_ruangan_id => $item->view_ruangan_nama];
            })->sort();

            $linen = $this->data->mapWithKeys(function ($item) {
                return [$item->view_linen_id => $item->view_linen_nama];
            })->sort();
        }

        return moduleView(modulePathPrint(), $this->share([
            'data' => $this->data,
            'rs' => $rs,
            'location' => $location,
            'linen' => $linen,
            'bersih' => $bersih,
            'kotor' => $kotor,
        ]));
    }
}
