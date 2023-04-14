<?php

namespace App\Dao\Repositories;

use App\Dao\Interfaces\CrudInterface;
use App\Dao\Models\Detail;
use App\Dao\Models\Kategori;
use App\Dao\Models\ViewDetailLinen;
use App\Dao\Models\ViewTransaksiCuci;
use Illuminate\Support\Facades\DB;
use Plugins\Notes;

class DetailRepository extends MasterRepository implements CrudInterface
{
    public function __construct()
    {
        $this->model = empty($this->model) ? new Detail() : $this->model;
    }

    public function dataRepository()
    {
        $query = $this->model
            ->select($this->model->getSelectedField())
            ->addSelect(ViewTransaksiCuci::field_total())
            ->leftJoinRelationship('has_cuci')
            ->leftJoinRelationship('has_jenis')
            ->leftJoinRelationship('has_ruangan')
            ->leftJoinRelationship('has_rs')
            ->sortable()->filter();

            if(request()->hasHeader('authorization')){
                if($paging = request()->get('paginate')){
                    return $query->paginate($paging);
                }

                if(method_exists($this->model, 'getApiCollection')){
                    return $this->model->getApiCollection($query->get());
                }

                return Notes::data($query->get());
            }

        $query = env('PAGINATION_SIMPLE') ? $query->simplePaginate(env('PAGINATION_NUMBER')) : $query->paginate(env('PAGINATION_NUMBER'));

        return $query;
    }

    public function getPrint(){
        return ViewDetailLinen::query()->filter();
    }

    public function getPrintDataMaster(){
        return ViewDetailLinen::query()
        ->addSelect([DB::raw('view_detail_linen.*'),
            Kategori::field_name(),
            ViewDetailLinen::field_bersih(),
            ViewDetailLinen::field_retur(),
            ViewDetailLinen::field_rewash(),
        ])
        ->leftJoinRelationship('has_bersih')
        ->leftJoinRelationship('has_retur')
        ->leftJoinRelationship('has_rewash')
        ->leftJoinRelationship('has_category')
        ->filter();
    }
}
