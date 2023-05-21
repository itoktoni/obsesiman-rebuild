<?php

namespace App\Http\Controllers;

use App\Dao\Enums\BooleanType;
use App\Dao\Enums\ProcessType;
use App\Dao\Enums\TransactionType;
use App\Dao\Models\Detail;
use App\Dao\Models\History;
use App\Dao\Models\Rs;
use App\Dao\Models\Transaksi;
use App\Dao\Models\ViewTransaksi;
use App\Dao\Repositories\TransaksiRepository;
use App\Http\Requests\BarcodeRequest;
use App\Http\Requests\GeneralRequest;
use App\Http\Requests\TransactionRequest;
use App\Http\Services\CreateService;
use App\Http\Services\SaveTransaksiService;
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

    public function get($code = null, $relation = null)
    {
        return Transaksi::where(Transaksi::field_barcode(), $code)->first();
    }

    public function getTable()
    {
        $data = $this->getData();
        return moduleView(modulePathTable(), [
            'data' => $data,
            'fields' => self::$repository->barcode->getShowField(),
        ]);
    }

    public function getData()
    {
        $query = self::$repository->dataBarcode();
        return $query;
    }

    private function getTransaksi($code){
        $view = ViewTransaksi::where(Transaksi::field_barcode(), $code);

        if($view){
            $transaksi = Transaksi::with([HAS_DETAIL, HAS_RS])
            ->where(Transaksi::field_barcode(), $code);

            return $transaksi;
        }

        return $view;
    }

    public function getUpdate($code)
    {
        dd($code);
        $transaksi = $this->getTransaksi($code);
        dd($transaksi->get());
        if(!$transaksi){
            return Response::redirectTo(moduleRoute('getTable'));
        }

        dd($this->get($code));

        return moduleView(modulePathForm(), $this->share([
            'model' => $this->get($code),
            'data' => $transaksi->get(),
        ]));
    }

    public function getDeleteTransaksi($code)
    {
        $transaksi = Transaksi::with([HAS_DETAIL])->findOrFail($code);
        if($transaksi){

            Detail::find($transaksi->field_rfid)->update([
                Detail::field_status_process() => ProcessType::Bersih,
                Detail::field_status_transaction() => TransactionType::BersihKotor,
            ]);

            PluginsHistory::log($transaksi->field_rfid, ProcessType::DeleteTransaksi, 'Data di delete dari transaksi '.$transaksi->field_primary);
            Notes::delete($transaksi->get()->toArray());
            Alert::delete();

            $transaksi->delete();
        }
        return Response::redirectBack();
    }

    public function getDelete()
    {
        $code = request()->get('code');
        $transaksi = $this->getTransaksi($code);

        if($transaksi){
            $rfid = $transaksi->pluck(Transaksi::field_rfid());

            Detail::whereIn(Detail::field_primary(), $rfid)->update([
                Detail::field_status_process() => ProcessType::Bersih,
                Detail::field_status_transaction() => TransactionType::BersihKotor,
            ]);

            $bulk = $transaksi->get()->toArray();
            PluginsHistory::bulk($rfid, ProcessType::DeleteTransaksi, $bulk);
            Notes::delete($bulk);
            Alert::delete();
            $transaksi->delete();
        }

        return Response::redirectBack($transaksi);
    }

    public function barcode(BarcodeRequest $request, UpdateBarcodeService $service){
        $autoNumber = Query::autoNumber(Transaksi::getTableName(), Transaksi::field_barcode(), 'BRC'.date('Ymd'), env('AUTO_NUMBER', 15));
        $check = $service->update($request->rfid, $autoNumber);
        return $check;
    }
}
