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
use App\Http\Requests\GeneralRequest;
use App\Http\Requests\TransactionRequest;
use App\Http\Services\CreateService;
use App\Http\Services\SaveTransaksiService;
use App\Http\Services\SingleService;
use App\Http\Services\UpdateService;
use Plugins\Alert;
use Plugins\History as PluginsHistory;
use Plugins\Notes;
use Plugins\Response;

class TransaksiController extends MasterController
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

    private function getTransaksi($code){
        $view = ViewTransaksi::find($code);

        if($view){
            $transaksi = Transaksi::with([HAS_DETAIL, HAS_RS])
            ->where(Transaksi::field_key(), $view->field_key)
            ->where(Transaksi::field_status_transaction(), $view->field_status_transaction)
            ->where(Transaksi::field_report(), $view->field_report);

            return $transaksi;
        }

        return $view;
    }

    public function getUpdate($code)
    {
        $transaksi = $this->getTransaksi($code);
        if(!$transaksi){
            return Response::redirectTo(moduleRoute('getTable'));
        }

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

    public function kotor(TransactionRequest $request, SaveTransaksiService $service){
        $request[STATUS_TRANSAKSI] = TransactionType::Kotor;
        $request[STATUS_PROCESS] = ProcessType::Kotor;
        return $this->transaction($request, $service);
    }

    public function retur(TransactionRequest $request, SaveTransaksiService $service){
        $request[STATUS_TRANSAKSI] = TransactionType::Retur;
        $request[STATUS_PROCESS] = ProcessType::Kotor;
        return $this->transaction($request, $service);
    }

    public function rewash(TransactionRequest $request, SaveTransaksiService $service){
        $request[STATUS_TRANSAKSI] = TransactionType::Rewash;
        $request[STATUS_PROCESS] = ProcessType::Kotor;
        return $this->transaction($request, $service);
    }

    private function transaction($request, $service){
        if(env('TRANSACTION_ACTIVE_RS_ONLY', 1) && !(Rs::find($request->rs_id)->field_active)){
            return Notes::error($request->rs_id, 'Rs belum di registrasi');
        }

        $rfid = $request->rfid;
        $data = Detail::whereIn(Detail::field_primary() ,$rfid)
        ->get()->mapWithKeys(function($item) {
            return [$item[Detail::field_primary()] => $item];
        });

        $form_transaksi = $request->{STATUS_TRANSAKSI};
        $status_sync = BooleanType::No;

        $return = $transaksi = $linen = $log = [];

        foreach($rfid as $item){
            $date = date('Y-m-d H:i:s');
            $user = auth()->user()->id;

            if(isset($data[$item])){
                $detail = $data[$item];

                if(in_array($detail->field_status_transaction, BERSIH)){

                    if(now()->diffInDays($detail->field_updated_at) > env('TRANSACTION_DAY_ALLOWED', 1)){

                        $status_transaksi = TransactionType::Kotor;
                        $status_process = ProcessType::Kotor;
                        $status_sync = BooleanType::Yes;

                        $beda_rs = $request->rs_id == $detail->field_rs_id ? BooleanType::No : BooleanType::Yes;

                        $data_transaksi = [
                            Transaksi::field_key() => $request->key,
                            Transaksi::field_rfid() => $item,
                            Transaksi::field_status_transaction() => $form_transaksi,
                            Transaksi::field_rs_id() => $request->rs_id,
                            Transaksi::field_beda_rs() => $beda_rs,
                            Transaksi::field_report() => date('Y-m-d'),
                            Transaksi::CREATED_AT => $date,
                            Transaksi::CREATED_BY => $user,
                            Transaksi::UPDATED_AT => $date,
                            Transaksi::UPDATED_BY => $user,
                        ];

                        $transaksi[] = $data_transaksi;

                        $linen[] = (string)$item;

                        $log[] = [
                            History::field_name() => $item,
                            History::field_status() => ProcessType::Kotor,
                            History::field_created_by() => auth()->user()->name,
                            History::field_created_at() => $date,
                            History::field_description() => json_encode($data_transaksi),
                        ];

                    } else {
                        $status_transaksi = $detail->field_status_transaction;
                        $date = $detail->field_updated_at->format('Y-m-d H:i:s');
                        $status_process = $detail->field_status_process;
                    }

                } else {
                    $status_transaksi = $detail->field_status_transaction;
                    $date = $detail->field_updated_at->format('Y-m-d H:i:s');
                    $status_process = $detail->field_status_process;
                }

                $return[] = [
                    KEY => $request->key,
                    STATUS_SYNC => $status_sync,
                    STATUS_TRANSAKSI => $status_transaksi,
                    STATUS_PROCESS => $status_process,
                    RFID => $item,
                    TANGGAL_UPDATE => $date,
                ];
            }
            else{
                $return[] = [
                    KEY => $request->key,
                    STATUS_SYNC => $status_sync,
                    STATUS_TRANSAKSI => TransactionType::Unknown,
                    STATUS_PROCESS => ProcessType::Unknown,
                    RFID => $item,
                    TANGGAL_UPDATE => $date,
                ];
            }
        }
        $check = $service->save($form_transaksi, $transaksi, $linen, $log, $return);
        return $check;
    }
}
