<?php

namespace App\Dao\Traits;

use App\Dao\Enums\BooleanType;
use App\Dao\Scopes\FilterScope;
use Facades\App\Models\User;
use Illuminate\Support\Facades\Cache;
use Facades\Modules\System\Dao\Models\Filter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

trait DataTableTrait
{
    abstract public function fieldDatatable(): array;

    public function getSelectedField(): array
    {
        return collect($this->fieldDatatable())->pluck('code')->toArray();
    }

    public function getShowField()
    {
        return collect($this->fieldDatatable())->where('show', true);
    }

    public function getExcelField()
    {
        return collect($this->fieldDatatable())->where('excel', true)->pluck( 'code', 'name')->toArray();
    }

    public function filter($query, $value)
    {
        return $this->queryFilter($query);
    }

    public function queryFilter($query){
        $search = request()->get('search');
        $value = request()->get('filter') ? request()->get('filter') : $this->fieldSearching();
        if($search){
            $query = $query->where($value, 'like', "%{$search}%");
        }

        return $query;
    }

    public function start_date($query){
        $date = request()->get('start_date');
        if($date){
            $query = $query->whereDate($this->field_reported_at(), '>=', $date);
        }

        return $query;
    }

    public function end_date($query){
        $date = request()->get('end_date');

        if($date){
            $query = $query->whereDate($this->field_reported_at(), '<=', $date);
        }

        return $query;
    }

    public function fieldSearching(){

        return $this->getKeyName();
    }
}