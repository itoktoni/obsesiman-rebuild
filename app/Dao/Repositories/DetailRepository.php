<?php

namespace App\Dao\Repositories;

use App\Dao\Interfaces\CrudInterface;
use App\Dao\Models\Detail;
use App\Dao\Models\ViewDetailLinen;
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
            ->select('*')
            ->leftJoinRelationship('has_cuci')
            ->leftJoinRelationship('has_return')
            ->leftJoinRelationship('has_rewash')
            ->leftJoinRelationship('has_view')
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
        $sql = ViewDetailLinen::query()->filter();
        return $sql;
    }

    public function getPrintDataMaster(){
        $sql = ViewDetailLinen::query()
        ->addSelect([DB::raw('view_detail_linen.*'),
            ViewDetailLinen::field_bersih(),
            ViewDetailLinen::field_retur(),
            ViewDetailLinen::field_rewash(),
        ])
        ->leftJoinRelationship('has_bersih')
        ->leftJoinRelationship('has_retur')
        ->leftJoinRelationship('has_rewash')
        ->leftJoinRelationship('has_category')
        ->filter();

        return $sql;
    }
}
