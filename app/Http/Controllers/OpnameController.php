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
            'has_view.has_cuci',
        ])->where(OpnameDetail::field_opname(), $code)
            ->fastPaginate(200);

        return moduleView(modulePathForm(), $this->share([
            'model' => $model,
            'detail' => $detail,
        ]));
    }

    private function checkKetemu($item)
    {

        if (in_array($item->detail_status_proses, [ProcessType::Pending, ProcessType::Hilang])) {
            return BooleanType::Yes;
        }

        if (in_array($item->detail_status_transaksi, [TransactionType::Retur, TransactionType::Rewash])) {
            return BooleanType::Yes;
        }

        return BooleanType::No;
    }

    public function postUpdate($code, Request $request, UpdateService $service)
    {
        $data = $service->update(self::$repository, $request, $code);
        dispatch(new StartOpname(1000, auth()->user()->id , 1, 10));
        return Response::redirectBack($data);

        $opnameID = 1000;
        $chunkIndex = 1;
        $chunkSize = 100;

        // $data_rfid = Detail::query()
        //     ->orderBy(Detail::field_primary(), 'asc')
        //     ->skip(($chunkIndex - 1) * $chunkSize)
        //     ->take($chunkSize)
        //     ->get();

        //     dd($data_rfid);

        // $tgl = date('Y-m-d H:i:s');

        // $log = [];
        // if ($data_rfid) {
        //     $id = auth()->user()->id;

        //     foreach ($data_rfid as $item) {

        //         $ketemu = $this->checkKetemu($item);
        //         $insert[] = [
        //             OpnameDetail::field_rfid() => $item->detail_rfid,
        //             OpnameDetail::field_transaksi() => $item->detail_status_transaksi,
        //             OpnameDetail::field_proses() => $item->detail_status_proses,
        //             OpnameDetail::field_created_at() => $tgl,
        //             OpnameDetail::field_created_by() => $id,
        //             OpnameDetail::field_updated_at() => !empty($item->detail_updated_at) ? $item->detail_updated_at->format('Y-m-d H:i:s') : null,
        //             OpnameDetail::field_updated_by() => $id,
        //             OpnameDetail::field_waktu() => $tgl,
        //             OpnameDetail::field_ketemu() => $ketemu,
        //             OpnameDetail::field_opname() => $opnameID,
        //             OpnameDetail::field_pending() => !empty($item->detail_pending_created_at) ? $item->detail_pending_created_at->format('Y-m-d H:i:s') : null,
        //             OpnameDetail::field_hilang() => !empty($item->detail_hilang_created_at) ? $item->detail_hilang_created_at->format('Y-m-d H:i:s') : null,
        //         ];

        //     }

        //     OpnameDetail::insert($insert);
        // }

        // die();

        $opnameId = $data['data']->field_primary;

        if ($request->opname_status == OpnameType::Capture) {
            $total = Detail::where(Detail::field_rs_id(), $request->opname_id_rs)->count();
            $chunkSize = 100;
            $numberOfChunks = ceil($total / $chunkSize);

            for ($i = 1; $i <= $numberOfChunks; $i++) {
                $batches[] = new StartOpname($opnameId, $i, $chunkSize);
            }

            $batch = Bus::batch($batches)->then(function (Batch $batch) {

                Alert::info('Opname Berhasil');

            })
            ->catch(function (Batch $batch, Throwable $e) {

                Alert::error($e->getMessage());

            })
            ->dispatch();

            if ($request->queue == 'batch') {
                $url = moduleRoute('getUpdate', array_merge(['code' => $data['data']->field_primary, 'batch' => $batch->id], $request->all()));
                return redirect()->route($url);
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
