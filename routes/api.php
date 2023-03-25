<?php

use App\Dao\Enums\ProcessType;
use App\Dao\Enums\RegisterType;
use App\Dao\Enums\StatusType;
use App\Dao\Enums\StockType;
use App\Dao\Models\Detail;
use App\Dao\Models\DetailLinen;
use App\Dao\Models\History as ModelsHistory;
use App\Dao\Models\Kotor;
use App\Dao\Models\Rs;
use App\Dao\Models\ViewDetailLinen;
use App\Http\Controllers\UserController;
use App\Http\Requests\DetailDataRequest;
use App\Http\Requests\DetailRequest;
use App\Http\Requests\DetailUpdateRequest;
use App\Http\Requests\GantiRfidRequest;
use App\Http\Requests\KotorRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\DetailResource;
use App\Http\Resources\DownloadLinenResource;
use App\Http\Resources\LinenDetailResource;
use App\Http\Resources\RsResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Plugins\History;
use Plugins\Notes;

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

    Route::get('download/{rsid}', function ($rsid){
        $data = ViewDetailLinen::where(ViewDetailLinen::field_rs_id(), $rsid)->get();
        $resource = DownloadLinenResource::collection($data);
        return response()->streamDownload(function () use ($resource) {
            echo json_encode($resource);
        }, $rsid . '_linen.json');
    });

    Route::get('/rs', function (Request $request) {

        try {

            $rs = Rs::with([HAS_RUANGAN, HAS_JENIS])->get();
            $collection = RsResource::collection($rs);
            return Notes::data($collection);

        } catch (\Throwable$th) {

            return Notes::error($th->getMessage());
        }

    });

    Route::get('/rs/{rsid}', function ($rsid) {

        try {

            $data = Rs::with([HAS_RUANGAN, HAS_JENIS])->findOrFail($rsid);
            $collection = new RsResource($data);
            return Notes::data($collection);

        } catch (\Throwable$th) {

            return Notes::error($th->getMessage());
        }

    });

    Route::post('/register', function (RegisterRequest $request) {
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

                $collection = new DetailResource($detail->has_view);

                return Notes::data($collection);

                DB::commit();
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

    Route::get('/detail/{rfid}', function ($rfid) {
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

    Route::post('/detail/rfid', function (DetailDataRequest $request) {
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

    Route::post('/detail/{rfid}', function ($rfid, DetailUpdateRequest $request) {
        try {

            $data = Detail::with('has_view')->findOrFail($rfid);

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

                History::log($rfid, ProcessType::UpdateRfid, [
                    'lama' => $lama->toArray(),
                    'baru' => $data->toArray(),
                ]);
            }

            return Notes::data(new DetailResource($data->has_view));

        } catch (\Throwable $th) {
            return Notes::error($rfid ,$th->getMessage());
        }
    });

    // Route::post('/kotor', function (KotorRequest $request) {

    //     try {

    //         if(is_array($request->linen_rfid)){

    //             DB::beginTransaction();

    //             $linen = collect($request->linen_rfid)->map(function($item) use($request){

    //                 return [
    //                     Kotor::field_name() => $request->key,
    //                     Kotor::field_rfid() => $item,
    //                     Kotor::field_id_rs() => $request->rs_id,
    //                     Kotor::field_id_ruangan() => $request->ruangan_id,
    //                     Kotor::CREATED_AT => date('Y-m-d H:i:s'),
    //                     Kotor::UPDATED_AT => date('Y-m-d H:i:s'),
    //                     Kotor::CREATED_BY => auth()->user()->id,
    //                     Kotor::UPDATED_BY => auth()->user()->id,
    //                 ];
    //             });

    //             Kotor::insert($linen->toArray());

    //             $history = collect($request->linen_rfid)->map(function($item) use($request){

    //                 return [
    //                     ModelsHistory::field_name() => $item,
    //                     ModelsHistory::field_status() => StatusType::Kotor,
    //                     ModelsHistory::field_created_by() => auth()->user()->name,
    //                     ModelsHistory::field_created_at() => date('Y-m-d H:i:s'),
    //                     ModelsHistory::field_description() => json_encode([ Kotor::field_rfid() => $item ]),
    //                 ];
    //             });

    //             ModelsHistory::insert($history->toArray());
    //             DB::commit();

    //             $return = DetailLinen::whereIn(DetailLinen::field_primary(), $linen->pluck(DetailLinen::field_primary()))->get();

    //             return Notes::data(LinenDetailResource::collection($return));
    //         }
    //         else{

    //             $linen = DetailLinen::create([
    //                 DetailLinen::field_id_rs() => $request->rs_id,
    //                 DetailLinen::field_id_ruangan() => $request->ruangan_id,
    //                 DetailLinen::field_name_id() => $request->nama_id,
    //                 DetailLinen::field_primary() => $request->linen_rfid,
    //             ]);

    //             return Notes::data(new LinenDetailResource($linen));

    //         }

    //     } catch (\Throwable $th) {
    //         DB::rollBack();
    //         return Notes::error($request->all() ,$th->getMessage());
    //     }

    // });

    Route::get('/opname', function (Request $request) {

    });

    Route::post('/opname', function (Request $request) {

    });
});
