<?php

namespace App\Http\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Plugins\Debug;
use App\Dao\Interfaces\CrudInterface;
use App\Plugins\Views;
use App\Plugins\Helper;
use App\Plugins\Notes;
use Yajra\DataTables\Facades\DataTables;

class DataService
{
    protected $model;
    protected $filter;
    protected $query;
    protected $searching;
    protected $raw;
    protected $datatable;
    protected $status = null;
    protected $image = null;
    protected $action = null;
    protected $transform = null;
    protected $expired = null;
    protected $column = ['action', 'checkbox'];

    public function setModel(CrudInterface $repository)
    {
        $this->model = $repository->dataRepository();
        return $this;
    }

    public function EditStatus($data)
    {
        $this->status = $data;
        return $this;
    }

    public function EditImage($data)
    {
        $this->image = $data;
        return $this;
    }

    public function EditAction($data, $active = true)
    {
        $this->action = $data;
        return $this;
    }

    public function EditColumn($data){

        $this->transform = $data;
        return $this;
    }

    public function EditExpired($data){

        $this->expired = $data;
        return $this;
    }

    protected function setFilter()
    {
        $this->filter = Helper::filter($this->model);
        $request = request();
        if ($request->has('search')) {
            $code = $request->get('code') ?? null;
            $search = $request->get('search') ?? null;
            $check_model = $this->model->getModel();
            $check_transform = $check_model->datatable;
            if(isset($check_transform[$code]['status'])){
                $status_transform = $check_transform[$code]['status'];
                $get_transform = $check_model->{$status_transform};
                foreach($get_transform as $key_transform => $value_transform){
                    if(!strcasecmp($value_transform[0],$search)){
                        $search = $key_transform;
                    }
                }
            }
            $aggregate = $request->get('aggregate') ?? null;
            $search_field = empty($code) ? $this->model->getModel()->searching : $code;
            $aggregation = empty($aggregate) ? 'like' : $aggregate;
            $input = empty($aggregate) ? "%$search%" : "$search";
            $this->filter->where($search_field, $aggregation, $input);
        }
        if ($this->searching) {

            foreach ($this->searching as $key => $value) {
                if (is_array($value)) {
                    $this->filter->where($key, $value[0], $value[1]);
                } else {
                    $this->filter->where($key, $value);
                }
            }
        }

        return $this;
    }

    public function addFilter(array $where)
    {
        $this->searching = $where;
        return $this;
    }

    protected function setApi()
    {
        $pagination = request()->get('page') ? $this->filter->paginate(request()->get('limit') ?? env('WEBSITE_PAGINATION', 10)) : ['total' => $this->filter->count(), 'data' => $this->filter->get()->toArray()];
        return Notes::data($pagination);
    }

    protected function setAction()
    {
        $this->datatable->addColumn('checkbox', function($model){
           return view(Views::checkbox())->with(['model' => $model]);
        });

        if(!empty($this->action)){

            $view = $this->action;
            $this->datatable->addColumn('action', function($model) use ($view){
                return view(Views::action($view['page'], $view['folder']))->with(['model' => $model]);
             });
        }
        else{

            $this->datatable->addColumn('action', function($model){
                return view(Views::action())->with(['model' => $model]);
            });
        }
    }

    protected function setColumn()
    {
        if (!empty($this->transform)) {
            foreach ($this->transform as $key => $data) {
                $this->datatable->editColumn($key, function ($select) use ($key, $data) {
                    return $select->{$data};
                });
            }

            $this->column = array_merge($this->column, array_keys($this->transform));
        }

        if (!empty($this->expired)) {
            foreach ($this->expired as $key => $data) {
                $this->datatable->editColumn($key, function ($select) use ($key, $data) {
                    $expired = $select->{$data};
                    $compareDate = Carbon::createFromFormat('Y-m-d',  $expired);
                    $nowDate = Carbon::now();

                    $check = $nowDate->diffInDays($compareDate, false);
                    if($check < 0){
                        return "<b><h4 class='text-primary text-center'>$expired<h4></b>";
                    }
                    else if($check < 14){
                        return "<b><h4 class='text-danger text-center'>$expired<h4></b>";
                    }
                    else if($check < 20){
                        return "<b><h4 class='text-warning text-center'>$expired<h4></b>";
                    }
                    else if($check < 30 || $check > 29){
                        return "<b><h4 class='text-success text-center'>$expired<h4></b>";
                    }

                    return $expired;
                });
            }

            $this->column = array_merge($this->column, array_keys($this->expired));
        }
    }

    protected function setStatus()
    {
        if (!empty($this->status)) {

            foreach ($this->status as $key => $data) {
                $this->datatable->editColumn($key, function ($select) use ($key, $data) {
                    return Helper::createStatus($select->{$key}, $data);
                });
            }

            $this->column = array_merge($this->column, array_keys($this->status));
        }
    }

    protected function setImage()
    {
        if (!empty($this->image)) {

            foreach ($this->image as $key => $data) {
                $this->datatable->editColumn($key, function ($select) use ($key, $data) {
                    return Helper::createImage($data . '/thumbnail_' . $select->{$key});
                });
            }

            $this->column = array_merge($this->column, array_keys($this->image));
        }
    }

    public function make()
    {
        $this->setFilter();

        if (!request()->ajax()) {
            return $this->setApi();
        }
        $this->datatable = Datatables::of($this->filter);
        $this->setAction();
        $this->setStatus();
        $this->setImage();
        $this->setColumn();

        $this->datatable->rawColumns($this->column);
        return $this->datatable->make(true);
    }
}
