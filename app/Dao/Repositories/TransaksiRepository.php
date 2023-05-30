<?php

namespace App\Dao\Repositories;

use App\Dao\Enums\TransactionType;
use App\Dao\Interfaces\CrudInterface;
use App\Dao\Models\Transaksi;
use App\Dao\Models\ViewBarcode;
use App\Dao\Models\ViewTotalJenis;
use App\Dao\Models\ViewTransaksi;
use Plugins\Notes;

class TransaksiRepository extends MasterRepository implements CrudInterface
{
    public $barcode;

    public function __construct()
    {
        $this->model = empty($this->model) ? new ViewTransaksi() : $this->model;
        $this->barcode = empty($this->barcode) ? new ViewBarcode() : $this->barcode;
    }

    public function dataRepository()
    {
        $query = $this->model
            ->select($this->model->getSelectedField())
            ->sortable()->filter();

            if(request()->hasHeader('authorization')){
                if($paging = request()->get('paginate')){
                    return $query->paginate($paging);
                }

                return Notes::data($query->get());
            }

        $query = env('PAGINATION_SIMPLE') ? $query->simplePaginate(env('PAGINATION_NUMBER')) : $query->paginate(env('PAGINATION_NUMBER'));
        return $query;
    }

    public function dataBarcode()
    {
        $query = $this->barcode
            ->select($this->barcode->getSelectedField())
            ->sortable()->filter();

            if(request()->hasHeader('authorization')){
                if($paging = request()->get('paginate')){
                    return $query->paginate($paging);
                }

                return Notes::data($query->get());
            }

        $query = env('PAGINATION_SIMPLE') ? $query->simplePaginate(env('PAGINATION_NUMBER')) : $query->paginate(env('PAGINATION_NUMBER'));
        return $query;
    }

    public function getDetailKotor($type = TransactionType::Kotor){
        return $this->getQueryReportTransaksi()
        ->leftJoinRelationship(HAS_CUCI)
        ->where(Transaksi::field_status_transaction(), $type);
    }

    public function getDetailRetur($type = TransactionType::Retur){
        return $this->getQueryReportTransaksi()
        ->leftJoinRelationship(HAS_RETUR)
        ->where(Transaksi::field_status_transaction(), $type);
    }

    public function getDetailRewash($type = TransactionType::Rewash){
        return $this->getQueryReportTransaksi()
        ->leftJoinRelationship(HAS_REWASH)
        ->where(Transaksi::field_status_transaction(), $type);
    }

    public function getQueryReportTransaksi(){
        return Transaksi::query()
        ->addSelect(['*'])
        ->leftJoinRelationship(HAS_RS)
        ->leftJoinRelationship(HAS_DETAIL)
        ->leftJoinRelationship(HAS_USER)
        ->filter();
    }
}
