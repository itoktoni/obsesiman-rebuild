<?php

namespace App\Http\Controllers;

use App\Dao\Enums\CetakType;
use App\Dao\Enums\DetailType;
use App\Dao\Enums\ProcessType;
use App\Dao\Enums\TransactionType;
use App\Dao\Models\Cetak;
use App\Dao\Models\Detail;
use App\Dao\Models\Jenis;
use App\Dao\Models\Rs;
use App\Dao\Models\Ruangan;
use App\Dao\Models\Transaksi;
use App\Dao\Models\User;
use App\Dao\Models\ViewDelivery;
use App\Dao\Models\ViewDetailLinen;
use App\Dao\Repositories\TransaksiRepository;
use App\Http\Requests\GeneralRequest;
use App\Http\Requests\PendingRequest;
use App\Http\Services\CreateService;
use App\Http\Services\SingleService;
use App\Http\Services\UpdatePendingService;
use App\Http\Services\UpdateService;
use Plugins\Alert;
use Plugins\Notes;
use Plugins\Response;

class PendingController extends MasterController
{
    public function __construct(TransaksiRepository $repository, SingleService $service)
    {
        self::$repository = self::$repository ?? $repository;
        self::$service = self::$service ?? $service;
    }

    public function postCreate(GeneralRequest $request, CreateService $service)
    {
        $data = $service->save(self::$repository, $request);
        return Response::redirectBack($data);
    }

    public function postUpdate($code, GeneralRequest $request, UpdateService $service)
    {
        $data = $service->update(self::$repository, $request, $code);
        return Response::redirectBack($data);
    }

    public function getData()
    {
        set_time_limit(0);

        $query = self::$repository->getQueryReportTransaksi()
            ->whereNotNull(Transaksi::field_pending_in())
            ->leftJoinRelationship(HAS_RS)
            ->orderBy('transaksi_created_at', 'DESC')
        //  ->showSql()
        ;

        if ($status = request()->get('status')) {
            if ($status == DetailType::Register) {
                $query = $query->where(Transaksi::field_status_transaction(), TransactionType::Register);
            } else if ($status == DetailType::LinenBaru) {
                $query = $query->where(Transaksi::field_status_bersih(), TransactionType::Register);
            } else if ($status == DetailType::Kotor) {
                $query = $query->where(Transaksi::field_status_transaction(), TransactionType::Kotor);
            } else if ($status == DetailType::Retur) {
                $query = $query->where(Transaksi::field_status_transaction(), TransactionType::Retur);
            } else if ($status == DetailType::Rewash) {
                $query = $query->where(Transaksi::field_status_transaction(), TransactionType::Rewash);
            } else if ($status == DetailType::BersihKotor) {
                $query = $query->where(Transaksi::field_status_bersih(), TransactionType::BersihKotor);
            } else if ($status == DetailType::BersihRetur) {
                $query = $query->where(Transaksi::field_status_bersih(), TransactionType::BersihRetur);
            } else if ($status == DetailType::BersihRewash) {
                $query = $query->where(Transaksi::field_status_bersih(), TransactionType::BersihRewash);
            } else if ($status == DetailType::Pending) {
                $query = $query->where(ViewDetailLinen::field_status_process(), ProcessType::Pending)
                    ->whereNULL(Transaksi::field_status_bersih())
                    ->groupBy(ViewDetailLinen::field_primary());
            } else if ($status == DetailType::Hilang) {
                $query = $query->where(ViewDetailLinen::field_status_process(), ProcessType::Hilang)
                    ->whereNULL(Transaksi::field_status_bersih())
                    ->groupBy(ViewDetailLinen::field_primary());
            }
        }

        return $query->fastPaginate(200);
    }

    public function getTable()
    {
        $ruangan = Ruangan::getOptions();
        $rs = Rs::getOptions();
        $linen = Jenis::getOptions();
        $status = DetailType::getOptions();
        $user = User::getOptions();
        $data = $this->getData();

        return moduleView(modulePathTable(), [
            'data' => $data,
            'ruangan' => $ruangan,
            'linen' => $linen,
            'rs' => $rs,
            'user' => $user,
            'status' => $status,
            'fields' => self::$repository->delivery->getShowField(),
        ]);
    }

    private function getTransaksi($code)
    {
        $view = ViewDelivery::find($code);

        if ($view) {
            $transaksi = Transaksi::with([HAS_DETAIL, HAS_RS, 'has_created_delivery'])
                ->where(Transaksi::field_delivery(), $view->field_primary);

            return $transaksi;
        }

        return $view;
    }

    public function getUpdate($code)
    {
        $transaksi = $this->getTransaksi($code);
        if (!$transaksi) {
            return Response::redirectTo(moduleRoute('getTable'));
        }

        return moduleView(modulePathForm(), $this->share([
            'model' => ViewDelivery::find($code),
            'data' => $transaksi->get(),
        ]));
    }

    public function getDeleteTransaksi($code)
    {
        CheckDelete();

        $transaksi = Transaksi::with([HAS_DETAIL])->findOrFail($code);
        if ($transaksi) {

            Detail::find($transaksi->field_rfid)->update([
                Detail::field_status_process() => ProcessType::Barcode,
            ]);

            //PluginsHistory::log($transaksi->field_rfid, ProcessType::DeleteDelivery, 'Data di delete dari barcode ' . $transaksi->field_primary);
            Notes::delete($transaksi->get()->toArray());
            Alert::delete();

            $transaksi->transaksi_delivery_at = null;
            $transaksi->transaksi_delivery_by = null;
            $transaksi->transaksi_delivery = null;

            $transaksi->save();
        }
        return Response::redirectBack();
    }

    public function getDelete()
    {
        CheckDelete();

        $code = request()->get('code');
        $transaksi = Transaksi::where(Transaksi::field_delivery(), $code);

        if ($transaksi->count() > 0) {

            $clone_rfid = clone $transaksi;
            $transaksi->update([
                Transaksi::field_delivery_at() => null,
                Transaksi::field_delivery_by() => null,
                Transaksi::field_delivery() => null,
            ]);

            $data_rfid = $clone_rfid->get();
            $rfid = $data_rfid->pluck(Transaksi::field_rfid());

            Detail::whereIn(Detail::field_primary(), $rfid)->update([
                Detail::field_status_process() => ProcessType::Barcode,
            ]);

            $bulk = $data_rfid->toArray();
            //PluginsHistory::bulk($rfid, ProcessType::DeleteDelivery, $bulk);
            Notes::delete($bulk);
            Alert::delete();
        }

        return Response::redirectBack($transaksi);
    }

    public function pending(PendingRequest $request, UpdatePendingService $service)
    {
        $check = $service->update($request->rfid, $request->code, $request->status_transaksi, $request->tanggal, $request->rs_id);
        return $check;
    }

    public function print($code)
    {
        $total = Transaksi::where(Transaksi::field_delivery(), $code)
            ->join((new ViewDetailLinen())->getTable(), ViewDetailLinen::field_primary(), Transaksi::field_rfid())
            ->get();

        $data = null;
        $passing = [];

        if ($total->count() > 0) {

            $cetak = Cetak::where(Cetak::field_name(), $code)->first();
            if (!$cetak) {
                $cetak = Cetak::create([
                    Cetak::field_date() => date('Y-m-d'),
                    Cetak::field_name() => $code,
                    Cetak::field_type() => CetakType::Delivery,
                    Cetak::field_user() => auth()->user()->name ?? null,
                    Cetak::field_rs_id() => $total[0]->transaksi_rs_ori ?? null,
                ]);
            }

            $data = $total->mapToGroups(function ($item) {
                $parse = [
                    'id' => $item->view_linen_id,
                    'nama' => $item->view_linen_nama,
                    'lokasi' => $item->view_ruangan_nama,
                ];

                return [$item['view_linen_id'] . '#' . $item['view_ruangan_id'] => $parse];
            });

            foreach ($data as $item) {
                $return[] = [
                    'id' => $item[0]['id'],
                    'nama' => $item[0]['nama'],
                    'lokasi' => $item[0]['lokasi'],
                    'total' => count($item),
                ];
            }

            $passing['total'] = count($total);
            $passing['user'] = $cetak->field_user;
            $passing['rs_nama'] = $cetak->has_rs->field_name ?? 'Admin';
            $passing['tanggal_cetak'] = $cetak->field_date;

            $passing = Notes::data($return, $passing);

        }

        return $passing;
    }
}
