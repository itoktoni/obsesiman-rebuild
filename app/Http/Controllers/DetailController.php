<?php

namespace App\Http\Controllers;

use App\Dao\Enums\CuciType;
use App\Dao\Enums\ProcessType;
use App\Dao\Enums\TransactionType;
use App\Dao\Models\History;
use App\Dao\Models\Jenis;
use App\Dao\Models\Rs;
use App\Dao\Models\Ruangan;
use App\Dao\Repositories\DetailRepository;
use App\Http\Requests\GeneralRequest;
use App\Http\Services\SingleService;
use App\Http\Services\UpdateService;
use Plugins\Response;

class DetailController extends MasterController
{
    public function __construct(DetailRepository $repository, SingleService $service)
    {
        self::$repository = self::$repository ?? $repository;
        self::$service = self::$service ?? $service;
    }

    public function postUpdate($code, GeneralRequest $request, UpdateService $service)
    {
        $data = $service->update(self::$repository, $request, $code);
        return Response::redirectBack($data);
    }

    protected function beforeForm()
    {
        $rs = Rs::getOptions();
        $ruangan = Ruangan::getOptions();
        $jenis = Jenis::getOptions();
        $cuci = CuciType::getOptions();
        $transaction = TransactionType::getOptions();
        $process = ProcessType::getOptions();

        self::$share = [
            'process' => $process,
            'transaction' => $transaction,
            'cuci' => $cuci,
            'jenis' => $jenis,
            'ruangan' => $ruangan,
            'rs' => $rs,
        ];
    }

    public function getHistory($code)
    {
        $this->beforeForm();

        $model = $this->get($code);
        $history = History::where(History::field_name(), $code)
        ->orderBy(History::field_created_at(), 'DESC')
        ->limit(10)->get();

        return moduleView(modulePathForm('history'), $this->share([
            'model' => $model,
            'history' => $history,
        ]));
    }
}
