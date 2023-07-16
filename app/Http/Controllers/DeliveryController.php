<?php

namespace App\Http\Controllers;

use App\Dao\Enums\ProcessType;
use App\Dao\Models\Detail;
use App\Dao\Models\Transaksi;
use App\Dao\Models\ViewDelivery;
use App\Dao\Repositories\TransaksiRepository;
use App\Http\Requests\DeliveryRequest;
use App\Http\Requests\GeneralRequest;
use App\Http\Services\CreateService;
use App\Http\Services\SingleService;
use App\Http\Services\UpdateDeliveryService;
use App\Http\Services\UpdateService;
use Plugins\Alert;
use Plugins\History as PluginsHistory;
use Plugins\Notes;
use Plugins\Query;
use Plugins\Response;

class DeliveryController extends MasterController
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
        $query = self::$repository->dataDelivery();
        return $query;
    }

    public function getTable()
    {
        $data = $this->getData();
        return moduleView(modulePathTable(), [
            'data' => $data,
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
        $transaksi = Transaksi::with([HAS_DETAIL])->findOrFail($code);
        if ($transaksi) {

            Detail::find($transaksi->field_rfid)->update([
                Detail::field_status_process() => ProcessType::Barcode,
            ]);

            PluginsHistory::log($transaksi->field_rfid, ProcessType::DeleteDelivery, 'Data di delete dari barcode ' . $transaksi->field_primary);
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
            PluginsHistory::bulk($rfid, ProcessType::DeleteDelivery, $bulk);
            Notes::delete($bulk);
            Alert::delete();
        }

        return Response::redirectBack($transaksi);
    }

    public function delivery(DeliveryRequest $request, UpdateDeliveryService $service)
    {
        $check = $service->update($request->code, $request->status_transaksi);
        return $check;
    }
}
