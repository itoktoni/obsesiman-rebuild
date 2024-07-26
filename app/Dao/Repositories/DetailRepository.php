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

        return $query;
    }

    public function getPrint(){
        $sql = ViewDetailLinen::query()
            ->orderBy('view_linen_nama', 'ASC')
            ->filter();
        return $sql;
    }

    public function getPrintDataMaster(){
        $sql = ViewDetailLinen::query()
        ->addSelect([DB::raw('view_detail_linen.*')])
        ->leftJoinRelationship('has_category')
        ->orderBy('view_linen_nama', 'ASC')
        ->filter();

        return $sql;
    }
}
