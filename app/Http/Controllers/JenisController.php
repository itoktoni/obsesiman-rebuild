<?php

namespace App\Http\Controllers;

use App\Dao\Models\Kategori;
use App\Dao\Models\Rs;
use App\Dao\Repositories\JenisRepository;
use App\Http\Requests\GeneralRequest;
use App\Http\Requests\JenisRequest;
use App\Http\Requests\NamaLinenRequest;
use App\Http\Services\CreateService;
use App\Http\Services\SingleService;
use App\Http\Services\UpdateService;
use Plugins\Response;

class JenisController extends MasterController
{
    public function __construct(JenisRepository $repository, SingleService $service)
    {
        self::$repository = self::$repository ?? $repository;
        self::$service = self::$service ?? $service;
    }

    protected function beforeForm()
    {
        $rs = Rs::getOptions();
        $category = Kategori::getOptions();

        self::$share = [
            'category' => $category,
            'rs' => $rs,
        ];
    }

    public function postCreate(JenisRequest $request, CreateService $service)
    {
        $data = $service->save(self::$repository, $request);
        return Response::redirectBack($data);
    }

    public function postUpdate($code, JenisRequest $request, UpdateService $service)
    {
        $data = $service->update(self::$repository, $request, $code);
        return Response::redirectBack($data);
    }
}
