<?php

namespace App\Http\Controllers;

use App\Dao\Enums\CetakType;
use App\Dao\Enums\ProcessType;
use App\Dao\Enums\RegisterType;
use App\Dao\Models\Cetak;
use App\Dao\Models\Detail;
use App\Dao\Models\Transaksi;
use App\Dao\Models\ViewBarcode;
use App\Dao\Models\ViewDetailLinen;
use App\Dao\Models\ViewTransaksi;
use App\Dao\Repositories\TransaksiRepository;
use App\Http\Requests\BarcodeRequest;
use App\Http\Requests\GeneralRequest;
use App\Http\Services\CreateService;
use App\Http\Services\SingleService;
use App\Http\Services\UpdateBarcodeService;
use App\Http\Services\UpdateService;
use Faker\Provider\Barcode;
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

            $transaksi->transaksi_delivery_at = null;
            $transaksi->transaksi_delivery_by = null;
            $transaksi->transaksi_delivery = null;

            $transaksi->transaksi_status_bersih = null;
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

                Transaksi::field_delivery_at() => null,
                Transaksi::field_delivery_by() => null,
                Transaksi::field_delivery() => null,

                Transaksi::field_status_bersih() => null,
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
        $check = $service->update($request->rfid, $request->code, $request->status_transaksi, $request->ruangan_id, $request->rs_id);
        return $check;
    }

    public function print($code){

        $total = Transaksi::where(Transaksi::field_barcode(), $code)
        ->join((new ViewDetailLinen())->getTable(), ViewDetailLinen::field_primary(), Transaksi::field_rfid())
        ->get();

        $data = null;
        $passing = [];

        if($total->count() > 0){

            $cetak = Cetak::where(Cetak::field_name(), $code)->first();
            if(!$cetak){
                $cetak = Cetak::create([
                    Cetak::field_date() => date('Y-m-d'),
                    Cetak::field_name() => $code,
                    Cetak::field_type() => CetakType::Barcode,
                    Cetak::field_user() => auth()->user()->name ?? 'Admin',
                    Cetak::field_rs_id() => $total[0]->transaksi_rs_ori ?? null,
                    Cetak::field_ruangan_id() => $total[0]->view_ruangan_id ?? null,
                ]);
            }

            $data = $total->mapToGroups(function($item){
                $parse = [
                    'id' => $item->view_linen_id,
                    'nama' => $item->view_linen_nama,
                ];

                return [$item[ViewDetailLinen::field_id()] => $parse];
            });

            foreach($data as $item){
                $return[] = [
                    'id' => $item[0]['id'],
                    'nama' => $item[0]['nama'],
                    'total' => count($item),
                ];
            }

            $passing['total'] = count($total);
            $passing['user'] = $cetak->field_user;
            $passing['rs_nama'] = $cetak->has_rs->field_name ?? null;
            $passing['ruangan_nama'] = $cetak->has_ruangan->field_name ?? null;
            $passing['tanggal_cetak'] = $cetak->field_date;

            $passing = Notes::data($return, $passing);

        }

        return $passing;
    }
}
