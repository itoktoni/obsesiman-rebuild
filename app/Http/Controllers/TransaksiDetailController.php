<?php

namespace App\Http\Controllers;

use App\Dao\Builder\DataBuilder;
use App\Dao\Enums\DetailType;
use App\Dao\Enums\FilterType;
use App\Dao\Enums\ProcessType;
use App\Dao\Enums\TransactionType;
use App\Dao\Models\Jenis;
use App\Dao\Models\Rs;
use App\Dao\Models\Ruangan;
use App\Dao\Models\Transaksi;
use App\Dao\Models\User;
use App\Dao\Models\ViewDetailLinen;
use App\Dao\Repositories\TransaksiRepository;
use App\Http\Requests\DeleteRequest;
use App\Http\Services\DeleteService;
use App\Http\Services\SingleService;
use Illuminate\Support\Facades\DB;
use Plugins\Response;

class TransaksiDetailController extends MasterController
{
    public function __construct(TransaksiRepository $repository, SingleService $service)
    {
        self::$repository = self::$repository ?? $repository;
        self::$service = self::$service ?? $service;
    }

    public function getData()
    {
        $query = self::$repository->getQueryReportTransaksi()
                 ->leftJoinRelationship(HAS_RS)
                 ->orderBy('transaksi_created_at', 'DESC')
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

        return $query->fastPaginate(200);
    }

    public function getTable()
    {
        $data = $this->getData();
        $ruangan = Ruangan::getOptions();
        $rs = Rs::getOptions();
        $linen = Jenis::getOptions();
        $status = DetailType::getOptions();
        $user = User::getOptions();

        return moduleView(modulePathTable(), [
            'data' => $data,
            'ruangan' => $ruangan,
            'linen' => $linen,
            'rs' => $rs,
            'user' => $user,
            'status' => $status,
            'fields' => $this->fieldDatatable(),
        ]);
    }

    public function fieldDatatable(): array
    {
        return [
            DataBuilder::build(Transaksi::field_key())->name('No. Transaksi')->sort(),
            DataBuilder::build(Transaksi::field_rfid())->name('No. RFID')->sort(),
            DataBuilder::build(ViewDetailLinen::field_name())->name('Nama Linen')->sort(),
            DataBuilder::build(ViewDetailLinen::field_rs_name())->name('Rumah Sakit')->sort(),
            DataBuilder::build(ViewDetailLinen::field_ruangan_name())->name('Ruangan')->sort(),
            DataBuilder::build(Rs::field_name())->name('Scan Rumah Sakit')->sort(),
            DataBuilder::build(User::field_username())->name('User')->sort(),
        ];
    }

    public function getDelete()
    {
        $code = request()->get('code');

        $data = self::$service->delete(self::$repository, $code);
        return Response::redirectBack($data);
    }

    public function postDelete(DeleteRequest $request, DeleteService $service)
    {
        $code = $request->get('code');
        $data = $service->delete(self::$repository, $code);
        return Response::redirectBack($data);
    }
}
