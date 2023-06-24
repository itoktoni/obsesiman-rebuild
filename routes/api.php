<?php

use App\Dao\Enums\BooleanType;
use App\Dao\Enums\CuciType;
use App\Dao\Enums\ProcessType;
use App\Dao\Enums\RegisterType;
use App\Dao\Enums\TransactionType;
use App\Dao\Models\Detail;
use App\Dao\Models\History as ModelsHistory;
use App\Dao\Models\Rs;
use App\Dao\Models\Transaksi;
use App\Dao\Models\ViewDetailLinen;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\UserController;
use App\Http\Requests\DetailDataRequest;
use App\Http\Requests\DetailUpdateRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\DetailResource;
use App\Http\Resources\DownloadLinenResource;
use App\Http\Resources\RsResource;
use App\Http\Services\SaveTransaksiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Plugins\History;
use Plugins\Notes;
use Plugins\Query;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::post('login', [UserController::class, 'postLoginApi'])->name('postLoginApi');

Route::middleware(['auth:sanctum'])->group(function () use ($routes) {

    Route::get('status_register', function(){

        $data = [];
        foreach(RegisterType::getInstances() as $value => $key){
            $data[] = [
                'status_id' => $key,
                'status_nama' => formatWorld($value),
            ];
        }

        return $data;
    });

    Route::get('status_cuci', function(){

        $data = [];
        foreach(CuciType::getInstances() as $value => $key){
            $data[] = [
                'status_id' => $key,
                'status_nama' => formatWorld($value),
            ];
        }

        return $data;
    });

    Route::get('status_proses', function(){

        $data = [];
        foreach(ProcessType::getInstances() as $value => $key){
            $data[] = [
                'status_id' => $key,
                'status_nama' => formatWorld($value),
            ];
        }

        return $data;
    });

    Route::get('status_transaksi', function(){

        $data = [];
        foreach(TransactionType::getInstances() as $value => $key){
            $data[] = [
                'status_id' => $key,
                'status_nama' => formatWorld($value),
            ];
        }

        return $data;
    });

    Route::get('download/{rsid}', function ($rsid){
        $data = ViewDetailLinen::where(ViewDetailLinen::field_rs_id(), $rsid)->get();
        $resource = DownloadLinenResource::collection($data);
        return response()->streamDownload(function () use ($resource) {
            echo json_encode($resource);
        }, $rsid . '_linen.json');
    });

    Route::get('rs', function (Request $request) {

        try {

            $rs = Rs::with([HAS_RUANGAN, HAS_JENIS])->get();
            $collection = RsResource::collection($rs);
            return Notes::data($collection);

        } catch (\Throwable$th) {

            return Notes::error($th->getMessage());
        }

    });

    Route::get('rs/{rsid}', function ($rsid) {

        try {

            $data = Rs::with([HAS_RUANGAN, HAS_JENIS])->findOrFail($rsid);
            $collection = new RsResource($data);
            return Notes::data($collection);

        } catch (\Throwable$th) {

            return Notes::error($th->getMessage());
        }

    });

    Route::post('register', function (RegisterRequest $request) {
        try {
            if(is_array($request->rfid)){

                DB::beginTransaction();

                $linen = collect($request->rfid)->map(function($item) use ($request){

                    return [
                        Detail::field_primary() => $item,
                        Detail::field_rs_id() => $request->rs_id,
                        Detail::field_ruangan_id() => $request->ruangan_id,
                        Detail::field_jenis_id() => $request->jenis_id,
                        Detail::field_status_cuci() => $request->status_cuci,
                        Detail::field_status_register() => RegisterType::Register,
                        Detail::field_status_process() => ProcessType::Register,
                        Detail::CREATED_AT => date('Y-m-d H:i:s'),
                        Detail::UPDATED_AT => date('Y-m-d H:i:s'),
                        Detail::CREATED_BY => auth()->user()->id,
                        Detail::UPDATED_BY => auth()->user()->id,
                    ];
                });

                Detail::insert($linen->toArray());

                $history = collect($request->rfid)->map(function($item) use($request){

                    return [
                        ModelsHistory::field_name() => $item,
                        ModelsHistory::field_status() => ProcessType::Register,
                        ModelsHistory::field_created_by() => auth()->user()->name,
                        ModelsHistory::field_created_at() => date('Y-m-d H:i:s'),
                        ModelsHistory::field_description() => json_encode([ ModelsHistory::field_name() => $item ]),
                    ];
                });

                ModelsHistory::insert($history->toArray());
                DB::commit();

                $return = ViewDetailLinen::whereIn(ViewDetailLinen::field_primary(), $request->rfid)->get();

                return Notes::data(DetailResource::collection($return));
            }
            else{
                DB::beginTransaction();
                $detail = Detail::create([
                    Detail::field_primary() => $request->rfid,
                    Detail::field_rs_id() => $request->rs_id,
                    Detail::field_ruangan_id() => $request->ruangan_id,
                    Detail::field_jenis_id() => $request->jenis_id,
                    Detail::field_status_cuci() => $request->status_cuci,
                    Detail::field_status_register() => RegisterType::Register,
                    Detail::field_status_process() => ProcessType::Register,
                ]);

                History::log($request->rfid, ProcessType::Register, $request->rfid);

                $collection = new DetailResource($detail->has_view);
                DB::commit();

                return Notes::data($collection);

            }

        }
        catch(\Illuminate\Database\QueryException $th){
            DB::rollBack();

            if($th->getCode() == 23000){
                return Notes::error($request->all() , 'data RFID yang dikirim sudah ada di database');
            }

            return Notes::error($request->all() ,$th->getMessage());
        }
        catch (\Throwable $th) {
            DB::rollBack();
            return Notes::error($request->all() ,$th->getMessage());
        }

    });

    Route::get('detail/{rfid}', function ($rfid) {
        try {
            $data = ViewDetailLinen::findOrFail($rfid);
            $collection = new DetailResource($data);
            return Notes::data($collection);

        }
        catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return Notes::error($rfid , 'RFID '.$rfid.' tidak ditemukan');
        }
        catch (\Throwable $th) {
            return Notes::error($rfid ,$th->getMessage());
        }
    });

    Route::post('detail/rfid', function (DetailDataRequest $request) {
        try {
            $data = ViewDetailLinen::whereIn(ViewDetailLinen::field_primary() , $request->rfid)->get();
            $collection = DetailResource::collection($data);
            return Notes::data($collection);
        }
        catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return Notes::error('data RFID tidak ditemukan');
        }
        catch (\Throwable $th) {
            return Notes::error($th->getMessage());
        }
    });

    Route::post('detail/{rfid}', function ($rfid, DetailUpdateRequest $request) {
        try {

            $data = Detail::with(HAS_VIEW)->findOrFail($rfid);

            if($data){

                $lama = clone $data;
                $data->{Detail::field_rs_id()} = $request->rs_id;
                $data->{Detail::field_ruangan_id()} = $request->ruangan_id;
                $data->{Detail::field_jenis_id()} = $request->jenis_id;

                if($request->status_cuci){
                    $data->{Detail::field_status_cuci()} = $request->status_cuci;
                }
                if($request->status_register){
                    $data->{Detail::field_status_register()} = $request->status_register;
                }

                $data->save();

                History::log($rfid, ProcessType::UpdateChip, [
                    'lama' => $lama->toArray(),
                    'baru' => $data->toArray(),
                ]);
            }

            return Notes::data(new DetailResource($data->has_view));

        } catch (\Throwable $th) {
            return Notes::error($rfid ,$th->getMessage());
        }
    });

    Route::post('kotor', [TransaksiController::class, 'kotor']);
    Route::post('retur', [TransaksiController::class, 'retur']);
    Route::post('rewash', [TransaksiController::class, 'rewash']);

    Route::get('grouping/{rfid}', function ($rfid, SaveTransaksiService $service) {
        try {
            $data = ViewDetailLinen::findOrFail($rfid);

            $data_transaksi = [];
            $linen[] = $rfid;

            $date = date('Y-m-d H:i:s');
            $user = auth()->user()->id;

            if(!in_array($data->field_status_transaction, [TransactionType::Kotor, TransactionType::Retur, TransactionType::Rewash])){

                $data_transaksi[] = [
                    Transaksi::field_key() => Query::autoNumber((new Transaksi())->getTable(), Transaksi::field_key(), 'GROUP'.date('Ymd', 15)),
                    Transaksi::field_rfid() => $rfid,
                    Transaksi::field_status_transaction() => TransactionType::Kotor,
                    Transaksi::field_rs_id() => $data->field_rs_id,
                    Transaksi::field_beda_rs() => BooleanType::No,
                    Transaksi::CREATED_AT => $date,
                    Transaksi::CREATED_BY => $user,
                    Transaksi::UPDATED_AT => $date,
                    Transaksi::UPDATED_BY => $user,
                ];

                $log[] = [
                    ModelsHistory::field_name() => $rfid,
                    ModelsHistory::field_status() => ProcessType::Kotor,
                    ModelsHistory::field_created_by() => auth()->user()->name,
                    ModelsHistory::field_created_at() => $date,
                    ModelsHistory::field_description() => json_encode($data_transaksi),
                ];
            }

            $log[] = [
                ModelsHistory::field_name() => $rfid,
                ModelsHistory::field_status() => ProcessType::Grouping,
                ModelsHistory::field_created_by() => auth()->user()->name,
                ModelsHistory::field_created_at() => $date,
                ModelsHistory::field_description() => json_encode($linen),
            ];

            $check = $service->save(TransactionType::Kotor, ProcessType::Grouping, $data_transaksi, $linen, $log);
            if(!$check['status']){
                return $check;
            }

            $update = ViewDetailLinen::findOrFail($rfid);

            $collection = new DetailResource($update);
            return Notes::data($collection);
        }
        catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return Notes::error($rfid , 'RFID '.$rfid.' tidak ditemukan');
        }
        catch (\Throwable $th) {
            return Notes::error($rfid ,$th->getMessage());
        }
    });

    Route::post('barcode', [BarcodeController::class, 'barcode']);
    Route::post('delivery', [DeliveryController::class, 'delivery']);

    Route::get('/opname', function (Request $request) {

    });

    Route::post('/opname', function (Request $request) {

    });
});
