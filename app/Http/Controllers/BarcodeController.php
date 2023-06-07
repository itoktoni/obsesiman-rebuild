<?php

namespace App\Http\Controllers;

use App\Dao\Enums\ProcessType;
use App\Dao\Models\Detail;
use App\Dao\Models\Transaksi;
use App\Dao\Models\ViewBarcode;
use App\Dao\Repositories\TransaksiRepository;
use App\Http\Requests\BarcodeRequest;
use App\Http\Requests\GeneralRequest;
use App\Http\Services\CreateService;
use App\Http\Services\SingleService;
use App\Http\Services\UpdateBarcodeService;
use App\Http\Services\UpdateService;
use Plugins\Alert;
use Plugins\History as PluginsHistory;
use Plugins\Notes;
use Plugins\Query;
use Plugins\Response;

class BarcodeController extends MasterController
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
        $query = self::$repository->dataBarcode();
        return $query;
    }

    public function getTable()
    {
        $data = $this->getData();
        return moduleView(modulePathTable(), [
            'data' => $data,
            'fields' => self::$repository->barcode->getShowField(),
        ]);
    }

    private function getTransaksi($code)
    {
        $view = ViewBarcode::find($code);

        if ($view) {
            $transaksi = Transaksi::with([HAS_DETAIL, HAS_RS, 'has_created_barcode'])
                ->where(Transaksi::field_barcode(), $view->field_primary);

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
            'model' => ViewBarcode::find($code),
            'data' => $transaksi->get(),
        ]));
    }

    public function getDeleteTransaksi($code)
    {
        $transaksi = Transaksi::with([HAS_DETAIL])->findOrFail($code);
        if ($transaksi) {

            Detail::find($transaksi->field_rfid)->update([
                Detail::field_status_process() => ProcessType::Grouping,
            ]);

            PluginsHistory::log($transaksi->field_rfid, ProcessType::DeleteBarcode, 'Data di delete dari barcode ' . $transaksi->field_primary);
            Notes::delete($transaksi->get()->toArray());
            Alert::delete();

            $transaksi->transaksi_barcode_at = null;
            $transaksi->transaksi_barcode_by = null;
            $transaksi->transaksi_barcode = null;
            $transaksi->save();
        }
        return Response::redirectBack();
    }

    public function getDelete()
    {
        $code = request()->get('code');
        $transaksi = Transaksi::where(Transaksi::field_barcode(), $code);

        if ($transaksi->count() > 0) {

            $clone_rfid = clone $transaksi;
            $transaksi->update([
                Transaksi::field_barcode_at() => null,
                Transaksi::field_barcode_by() => null,
                Transaksi::field_barcode() => null,
            ]);

            $data_rfid = $clone_rfid->get();
            $rfid = $data_rfid->pluck(Transaksi::field_rfid());

            Detail::whereIn(Detail::field_primary(), $rfid)->update([
                Detail::field_status_process() => ProcessType::Grouping,
            ]);

            $bulk = $data_rfid->toArray();
            PluginsHistory::bulk($rfid, ProcessType::DeleteBarcode, $bulk);
            Notes::delete($bulk);
            Alert::delete();
        }

        return Response::redirectBack($transaksi);
    }

    public function barcode(BarcodeRequest $request, UpdateBarcodeService $service)
    {
        $autoNumber = Query::autoNumber(Transaksi::getTableName(), Transaksi::field_barcode(), 'BC' . date('Ymd'), env('AUTO_NUMBER', 15));
        $check = $service->update($request->rfid, $autoNumber);
        return $check;
    }
}
