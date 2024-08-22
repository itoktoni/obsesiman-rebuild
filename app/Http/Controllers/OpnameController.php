<?php

namespace App\Http\Controllers;

use App\Dao\Enums\BooleanType;
use App\Dao\Enums\OpnameType;
use App\Dao\Enums\ProcessType;
use App\Dao\Enums\TransactionType;
use App\Dao\Models\Detail;
use App\Dao\Models\OpnameDetail;
use App\Dao\Models\Rs;
use App\Dao\Repositories\OpnameRepository;
use App\Http\Requests\OpnameRequest;
use App\Http\Services\CaptureOpnameService;
use App\Http\Services\CreateService;
use App\Http\Services\SingleService;
use App\Http\Services\UpdateService;
use App\Jobs\StartOpname;
use Illuminate\Bus\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Laravie\SerializesQuery\Eloquent;
use Plugins\Alert;
use Plugins\Response;
use Throwable;

class OpnameController extends MasterController
{
    public function __construct(OpnameRepository $repository, SingleService $service)
    {
        self::$repository = self::$repository ?? $repository;
        self::$service = self::$service ?? $service;
    }

    protected function beforeForm()
    {
        $rs = Rs::getOptions();
        $status = OpnameType::getOptions();
        $detail = [];

        self::$share = [
            'rs' => $rs,
            'status' => $status,
            'detail' => $detail,
        ];
    }

    public function getData()
    {
        $query = self::$repository->dataRepository();
        return $query;
    }

    public function postCreate(OpnameRequest $request, CreateService $service)
    {
        $data = $service->save(self::$repository, $request);
        return Response::redirectBack($data);
    }

    public function getSelesai($code)
    {
        ini_set('max_execution_time', '0');
        $model = $this->get($code);
        $model->opname_status = OpnameType::Selesai;
        $model->save();

        Alert::create('Sukses', 'Opname selesai');

        return Response::redirectBack();
    }

    public function getCapture($code, CaptureOpnameService $service)
    {
        ini_set('max_execution_time', '0');
        $model = $this->get($code);
        if (!empty($model->opname_capture)) {
            Alert::error('Opname sudah di capture !');
            return Response::redirectBack();
        }

        $data = $service->save($model);
        return Response::redirectBack($data);
    }

    public function getUpdate($code)
    {
        $this->beforeForm();
        $this->beforeUpdate($code);

        $model = $this->get($code);
        $detail = OpnameDetail::with([
            'has_view',
        ])->where(OpnameDetail::field_opname(), $code)
        ->cursorPaginate(10);

        return moduleView(modulePathForm(), $this->share([
            'model' => $model,
            'detail' => $detail,
        ]));
    }

    public function postUpdate($code, Request $request, UpdateService $service)
    {
        $data = $service->update(self::$repository, $request, $code);

        $opnameId = $data['data']->field_primary;
        $userId = auth()->user()->id;
        $mod = $data['data'];

        if ($request->opname_status == OpnameType::Capture && empty($mod->opname_capture))
        {
            $mod->opname_capture = date('Y-m-d H:i:s');
            $mod->save();

            $query = Detail::where(Detail::field_rs_id(), $request->opname_id_rs);
            $total = $query->count();
            $chunkSize = 100;
            $numberOfChunks = ceil($total / $chunkSize);

            $serialize = Eloquent::serialize($query);

            for ($i = 1; $i <= $numberOfChunks; $i++) {
                $batches[] = new StartOpname($opnameId, $userId, $serialize, $i, $chunkSize);
            }

            $batch = Bus::batch($batches)->then(function (Batch $batch) {

                Alert::create('Opname Berhasil');

            })
            ->catch(function (Batch $batch, Throwable $e) {

                Alert::error($e->getMessage());

            })
            ->dispatch();

            if ($request->queue == 'batch') {
                $url = moduleRoute('getUpdate', array_merge(['code' => $mod->field_primary, 'batch' => $batch->id]));
                return redirect()->to($url);
            }
        }

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
        if (request()->exists('delete') and !empty(request()->get('code'))) {
            $code = array_unique(request()->get('code'));
            OpnameDetail::whereIn(OpnameDetail::field_opname(), $code)->delete();
            $data = self::$service->delete(self::$repository, $code);
        }

        return Response::redirectBack();
    }
}
