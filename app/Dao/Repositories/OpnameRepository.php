<?php

namespace App\Dao\Repositories;

use App\Dao\Interfaces\CrudInterface;
use App\Dao\Models\Opname;
use App\Dao\Models\OpnameDetail;
use Illuminate\Support\Facades\DB;
use Plugins\Notes;

class OpnameRepository extends MasterRepository implements CrudInterface
{
    public function __construct()
    {
        $this->model = empty($this->model) ? new Opname() : $this->model;
    }

    public function dataRepository()
    {
        $query = $this->model
            ->select($this->model->getSelectedField())
            ->leftJoinRelationship('has_rs')
            ->orderBy(Opname::field_primary(), 'desc')
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

    public function getOpnameByID($opname_id){
        $query = $this->getOpnameReport()
        ->where(OpnameDetail::field_opname(), $opname_id);

        return $query;
    }

    public function getOpnameReport(){
        $query = OpnameDetail::query()
            ->addSelect(DB::raw('*'))
            ->leftJoinRelationship('has_view')
            ->leftJoinRelationship('has_user')
            ->filter();

        return $query;
    }
}
