<?php

namespace App\Dao\Repositories;

use App\Dao\Interfaces\CrudInterface;
use App\Dao\Models\Detail;
use App\Dao\Models\Jenis;
use App\Dao\Models\ViewTotalJenis;
use Illuminate\Support\Facades\DB;
use Plugins\Notes;

class JenisRepository extends MasterRepository implements CrudInterface
{
    public function __construct()
    {
        $this->model = empty($this->model) ? new Jenis() : $this->model;
    }

    public function dataRepository()
    {
        $query = $this->model
            ->select($this->model->getSelectedField())
            ->addSelect(ViewTotalJenis::field_total())
            ->leftJoinRelationship('has_category')
            ->leftJoinRelationship('has_rs')
            ->leftJoinRelationship('has_total')
            ->sortable()->filter()
            ->orderBy('rs_nama', 'ASC')
            ->orderBy('jenis_nama', 'ASC');

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

    public function getParstok(){
        return Detail::query()
        ->addSelect(['*', DB::raw('count(detail_rfid) as qty')])
        ->leftJoinRelationship('has_jenis')
        ->leftJoinRelationship('has_rs')
        ->groupBy(Detail::field_jenis_id())
        ->filter();
    }
}
