<?php

namespace App\Http\Controllers;

use App\Dao\Enums\CuciType;
use App\Dao\Enums\DetailType;
use App\Dao\Enums\ProcessType;
use App\Dao\Enums\RegisterType;
use App\Dao\Enums\TransactionType;
use App\Dao\Models\Detail;
use App\Dao\Models\History;
use App\Dao\Models\Jenis;
use App\Dao\Models\OpnameDetail;
use App\Dao\Models\Rs;
use App\Dao\Models\Ruangan;
use App\Dao\Models\Transaksi;
use App\Dao\Models\ViewDetailLinen;
use App\Dao\Repositories\DetailRepository;
use App\Http\Requests\DeleteRequest;
use App\Http\Requests\GeneralRequest;
use App\Http\Services\DeleteService;
use App\Http\Services\SingleService;
use App\Http\Services\UpdateService;
use Plugins\Response;

class DetailController extends MasterController
{
    public function __construct(DetailRepository $repository, SingleService $service)
    {
        self::$repository = self::$repository ?? $repository;
        self::$service = self::$service ?? $service;
    }

    public function postUpdate($code, GeneralRequest $request, UpdateService $service)
    {
        $data = $service->update(self::$repository, $request, $code);
        return Response::redirectBack($data);
    }

    protected function beforeForm()
    {
        $rs = Rs::getOptions();
        $ruangan = Ruangan::getOptions();
        $jenis = Jenis::getOptions();
        $cuci = CuciType::getOptions();
        $transaction = DetailType::getOptions();
        $process = ProcessType::getOptions();
        $register = RegisterType::getOptions();

        self::$share = [
            'register' => $register,
            'process' => $process,
            'transaction' => $transaction,
            'cuci' => $cuci,
            'jenis' => $jenis,
            'ruangan' => $ruangan,
            'rs' => $rs,
        ];
    }

    public function getData()
    {
        $query = self::$repository->dataRepository()
                //  ->showSql()
                ;

        if($status = request()->get('status')){
            if($status == DetailType::Register){
                $query = $query->where(Transaksi::field_status_transaction(), TransactionType::Register);
            } else if($status == DetailType::LinenBaru){
                $query = $query->where(Transaksi::field_status_bersih(), TransactionType::Register);
            } else if($status == DetailType::Kotor){
                $query = $query->where(Transaksi::field_status_transaction(), TransactionType::Kotor);
            } else if($status == DetailType::Retur){
                $query = $query->where(Transaksi::field_status_transaction(), TransactionType::Retur);
            } else if($status == DetailType::Rewash){
                $query = $query->where(Transaksi::field_status_transaction(), TransactionType::Rewash);
            } else if($status == DetailType::BersihKotor){
                $query = $query->where(Transaksi::field_status_bersih(), TransactionType::BersihKotor);
            } else if($status == DetailType::BersihRetur){
                $query = $query->where(Transaksi::field_status_bersih(), TransactionType::BersihRetur);
            } else if($status == DetailType::BersihRewash){
                $query = $query->where(Transaksi::field_status_bersih(), TransactionType::BersihRewash);
            } else if($status == DetailType::Pending){
                $query = $query->where(ViewDetailLinen::field_status_process(), ProcessType::Pending)
                                ->whereNULL(Transaksi::field_status_bersih())
                                ->groupBy(ViewDetailLinen::field_primary());
            } else if($status == DetailType::Hilang){
                $query = $query->where(ViewDetailLinen::field_status_process(), ProcessType::Hilang)
                                ->whereNULL(Transaksi::field_status_bersih())
                                ->groupBy(ViewDetailLinen::field_primary());
            }
        }

        if ($start = request()->get('start_date')) {
            $query = $query->whereDate(Detail::field_created_at(), '>=', $start);
        }

        if ($end = request()->get('end_date')) {
            $query = $query->whereDate(Detail::field_created_at(), '<=', $end);
        }

        if ($bulk = request()->get('bulk_rfid')) {
            $explode = array_map('trim', explode(',', $bulk));
            $collect = collect($explode)->unique();
            $query = $query->whereIn(Detail::field_primary(), $collect);
        }

        return $query->fastPaginate(100);
    }

    public function getTable()
    {
        $data = $this->getData();
        $transaction = TransactionType::getOptions();
        $process = ProcessType::getOptions();
        $register = RegisterType::getOptions();

        return moduleView(modulePathTable(), [
            'data' => $data,
            'register' => $register,
            'process' => $process,
            'transaction' => $transaction,
            'fields' => self::$repository->model->fieldDatatable(),
        ]);
    }

    public function getHistory($code)
    {
        $this->beforeForm();

        $model = $this->get($code);
        $history = History::where(History::field_name(), $code)
        ->orderBy(History::field_created_at(), 'DESC')
        ->limit(10)->get();

        return moduleView(modulePathForm('history'), $this->share([
            'model' => $model,
            'history' => $history,
        ]));
    }

    public function postDelete(DeleteRequest $request, DeleteService $service)
    {
        $code = $request->get('code');
        $data = $service->delete(self::$repository, $code);
        $this->deleteAll($code);

        return Response::redirectBack($data);
    }

    public function getDelete()
    {
        $code = request()->get('code');
        $data = self::$service->delete(self::$repository, $code);
        $this->deleteAll([$code]);
        return Response::redirectBack($data);
    }

    public function postTable()
    {
        if(request()->exists('delete')){
            $code = array_unique(request()->get('code'));
            $data = self::$service->delete(self::$repository, $code);
            $this->deleteAll($code);
        }

        if(request()->exists('sort')){
            $sort = array_unique(request()->get('sort'));
            $data = self::$service->sort(self::$repository, $sort);
        }

        return Response::redirectBack($data);
    }

    private function deleteAll($code) {
        if(is_array($code)){
            History::whereIn(History::field_name(), $code)->delete();
            OpnameDetail::whereIn(OpnameDetail::field_rfid(), $code)->delete();
            Transaksi::whereIn(Transaksi::field_rfid(), $code)->delete();
        } else {
            History::where(History::field_name(), $code)->delete();
            OpnameDetail::where(OpnameDetail::field_rfid(), $code)->delete();
            Transaksi::where(Transaksi::field_rfid(), $code)->delete();
        }
    }
}
