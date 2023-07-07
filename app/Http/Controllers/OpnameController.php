<?php

namespace App\Http\Controllers;

use App\Dao\Enums\OpnameType;
use App\Dao\Models\OpnameDetail;
use App\Dao\Models\Rs;
use App\Dao\Repositories\OpnameRepository;
use App\Http\Requests\DeleteRequest;
use App\Http\Requests\OpnameRequest;
use App\Http\Services\CreateOpnameService;
use App\Http\Services\DeleteService;
use App\Http\Services\SingleService;
use App\Http\Services\UpdateService;
use Plugins\Response;

class OpnameController extends MasterController
{
    public function __construct(OpnameRepository $repository, SingleService $service)
    {
        self::$repository = self::$repository ?? $repository;
        self::$service = self::$service ?? $service;
    }

    protected function beforeForm(){
        $rs = Rs::getOptions();
        $status = OpnameType::getOptions();

        self::$share = [
            'rs' => $rs,
            'status' => $status,
        ];
    }

    public function postCreate(OpnameRequest $request, CreateOpnameService $service)
    {
        $data = $service->save(self::$repository, $request);
        return Response::redirectBack($data);
    }

    public function getUpdate($code)
    {
        $this->beforeForm();
        $this->beforeUpdate($code);

        $model = $this->get($code, ['has_detail', 'has_detail.has_view']);
        return moduleView(modulePathForm(), $this->share([
            'model' => $model,
            'detail' => $model->has_detail
        ]));
    }

    public function postUpdate($code, OpnameRequest $request, UpdateService $service)
    {
        $data = $service->update(self::$repository, $request, $code);
        return Response::redirectBack($data);
    }

    public function getDelete()
    {
        $code = request()->get('code');
        OpnameDetail::where(OpnameDetail::field_opname(), $code)->delete();
        $data = self::$service->delete(self::$repository, $code);
        return Response::redirectBack($data);
    }

    public function postTable()
    {
        if(request()->exists('delete')){
            $code = array_unique(request()->get('code'));
            OpnameDetail::whereIn(OpnameDetail::field_opname(), $code)->delete();
            $data = self::$service->delete(self::$repository, $code);
        }

        return Response::redirectBack($data);
    }
}
