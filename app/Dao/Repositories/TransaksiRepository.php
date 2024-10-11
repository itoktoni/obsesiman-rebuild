<?php

namespace App\Dao\Repositories;

use App\Dao\Enums\ProcessType;
use App\Dao\Enums\TransactionType;
use App\Dao\Interfaces\CrudInterface;
use App\Dao\Models\Detail;
use App\Dao\Models\Transaksi;
use App\Dao\Models\ViewBarcode;
use App\Dao\Models\ViewDelivery;
use App\Dao\Models\ViewTotalJenis;
use App\Dao\Models\ViewTransaksi;
use Doctrine\DBAL\Query\QueryException;
use Plugins\Notes;
use Plugins\Query;

class TransaksiRepository extends MasterRepository implements CrudInterface
{
    public $barcode;
    public $delivery;

    public function __construct()
    {
        $this->model = empty($this->model) ? new ViewTransaksi() : $this->model;
        $this->delivery = empty($this->delivery) ? new ViewDelivery() : $this->delivery;
        $this->barcode = empty($this->barcode) ? new ViewBarcode() : $this->barcode;
    }

    public function filterRepository($query){
        if(request()->hasHeader('authorization')){
            if($paging = request()->get('paginate')){
                return $query->paginate($paging);
            }

            return Notes::data($query->get());
        }

        return $query;

        $query = env('PAGINATION_SIMPLE') ? $query->simplePaginate(env('PAGINATION_NUMBER')) : $query->paginate(env('PAGINATION_NUMBER'));
        return $query;
    }

    public function dataRepository()
    {
        $query = $this->model
            ->select($this->model->getSelectedField())
            ->sortable()->filter();

        return $this->filterRepository($query);
    }

    public function dataBarcode()
    {
        $query = $this->barcode
            ->select($this->barcode->getSelectedField())
            ->sortable()->filter();

        return $this->filterRepository($query);
    }

    public function dataDelivery()
    {
        $query = $this->delivery
            ->select($this->delivery->getSelectedField())
            ->sortable()->filter();

        return $this->filterRepository($query);
    }

    public function getDetailBersih($type = TransactionType::BersihKotor){
        return $this->getQueryReportTransaksi()
        ->leftJoinRelationship(HAS_RS_DELIVERY)
        // ->leftJoinRelationship(HAS_CUCI)
        ->where(Transaksi::field_status_bersih(), $type);
    }

    public function getDetailAllBersih($filter = BERSIH){
        return $this->getQueryReportTransaksi()
        // ->leftJoinRelationship(HAS_CUCI)
        ->leftJoinRelationship(HAS_RS_DELIVERY)
        ->whereIn(Transaksi::field_status_bersih(), $filter);
    }

    public function getDetailKotor($type = TransactionType::Kotor){
        return $this->getQueryReportTransaksi()
        // ->leftJoinRelationship(HAS_CUCI)
        ->leftJoinRelationship(HAS_RS)
        ->where(Transaksi::field_status_transaction(), $type);
    }

    public function getDetailAllKotor($filter = KOTOR){
        return $this->getQueryReportTransaksi()
        ->leftJoinRelationship(HAS_RS)
        ->whereIn(Transaksi::field_status_transaction(), $filter);
    }

    public function getQueryReportTransaksi(){
        return Transaksi::query()
        ->addSelect(['*'])
        ->leftJoinRelationship(HAS_DETAIL)
        ->leftJoinRelationship(HAS_USER)
        ->orderBy('view_linen_nama', 'ASC')
        ->filter();
    }

    public function deleteRepository($request)
    {
        CheckDelete();
        try {
            if(is_array($request)){
                $rfid = array_values($request);
                $data_rfid = Transaksi::whereIn(Transaksi::field_primary(), $rfid)->get()->pluck(Transaksi::field_rfid());
                Transaksi::whereIn(Transaksi::field_rfid(), array_values($rfid))->delete();
                Detail::whereIn(Detail::field_primary(), $data_rfid)
                ->update([
                    Detail::field_status_transaction() => TransactionType::BersihKotor,
                    Detail::field_status_process() => ProcessType::Bersih
                ]);

            } else {

                $data_rfid = Transaksi::where(Transaksi::field_primary(), $request)->first()->field_rfid ?? null;
                Transaksi::where(Transaksi::field_rfid(), $data_rfid)->delete();
                Detail::where(Detail::field_primary(), $data_rfid)
                ->update([
                    Detail::field_status_transaction() => TransactionType::BersihKotor,
                    Detail::field_status_process() => ProcessType::Bersih
                ]);
            }

            return Notes::delete($request);
        } catch (QueryException $ex) {
            return Notes::error($ex->getMessage());
        }
    }

    public function getRekapKotor(){
        return Query::getTransaction();
    }
}
