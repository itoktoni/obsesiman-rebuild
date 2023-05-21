<?php

namespace App\Http\Controllers;

use App\Dao\Models\Rs;
use App\Dao\Models\Ruangan;
use App\Dao\Repositories\RsRepository;
use App\Http\Requests\RsRequest;
use App\Http\Services\CreateRsService;
use App\Http\Services\SingleService;
use App\Http\Services\UpdateRsService;
use Plugins\Response;

class RsController extends MasterController
{
    public function __construct(RsRepository $repository, SingleService $service)
    {
        self::$repository = self::$repository ?? $repository;
        self::$service = self::$service ?? $service;
    }

    protected function beforeForm(){

        $ruangan = Ruangan::getOptions();

        self::$share = [
            'ruangan' => $ruangan,
        ];
    }

    public function postCreate(RsRequest $request, CreateRsService $service)
    {
        $data = $service->save(self::$repository, $request->all());
        return Response::redirectBack($data);
    }

    public function postUpdate($code, RsRequest $request, UpdateRsService $service)
    {
        $data = $service->update(self::$repository, $request->all(), $code);
        return Response::redirectBack($data);
    }

    public function getUpdate($code)
    {
        $data = $this->get($code, [Rs::field_has_ruangan()]);
        $selected = $data->has_ruangan->pluck(Ruangan::field_primary()) ?? [];

        $this->beforeForm();
        $this->beforeUpdate($code);
        return moduleView(modulePathForm(), $this->share([
            'model' => $data,
            'selected' => $selected,
        ]));
    }
}
