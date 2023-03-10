<?php

use App\Dao\Enums\StatusType;
use App\Dao\Enums\StockType;
use App\Dao\Models\DetailLinen;
use App\Dao\Models\History as ModelsHistory;
use App\Dao\Models\Kotor;
use App\Dao\Models\Rs;
use App\Http\Controllers\UserController;
use App\Http\Requests\DetailLinenRequest;
use App\Http\Requests\DetailRfidRequest;
use App\Http\Requests\GantiRfidRequest;
use App\Http\Requests\KotorRequest;
use App\Http\Resources\LinenDetailResource;
use App\Http\Resources\RfidDetailResource;
use App\Http\Resources\RfidResource;
use App\Http\Resources\RsDetailResource;
use App\Http\Resources\RsResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
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

    Route::get('/rs', function (Request $request) {

        try {

            $rs = Rs::with(['has_ruangan'])->get();
            $collection = RsResource::collection($rs);
            return Notes::data($collection);

        } catch (\Throwable$th) {

            return Notes::error($th->getMessage());
        }

    });

    Route::get('/rs/{id}', function ($id) {

        try {

            $rs = Rs::with(['has_ruangan'])->findOrFail($id);
            $collection = new RsDetailResource($rs);
            return Notes::data($collection);

        } catch (\Throwable$th) {

            return Notes::error($id, $th->getMessage());
        }

    });

    Route::get('/rfid/{code}', function ($code) {

        try {

            $detail = DetailLinen::with(['has_name', 'has_rs', 'has_ruangan'])->leftJoin('view_detail_linen_kotor', 'hl_rfid', 'dl_rfid')->findOrFail($code);
            $collection = new LinenDetailResource($detail);
            return Notes::data($collection);

        } catch (\Throwable $th) {
            return Notes::error($code ,$th->getMessage());
        }

    });

    Route::post('/rfid/{code}', function ($code, DetailRfidRequest $request) {

        try {

            $rs = DetailLinen::find($code);
            if($rs){
                $rs->{DetailLinen::field_id_rs()} = $request->rs_id;
                $rs->{DetailLinen::field_id_ruangan()} = $request->ruangan_id;
                $rs->{DetailLinen::field_name_id()} = $request->nama_id;
                $rs->{DetailLinen::field_primary()} = $request->linen_rfid;
                $rs->{DetailLinen::field_last_status()} = StatusType::UpdateRfid;
                $rs->save();

                History::log($code, StatusType::UpdateRfid, [
                    'lama' => $request->all(),
                    'baru' => $rs->toArray(),
                ]);
            }
            return Notes::data(new LinenDetailResource($rs));

        } catch (\Throwable $th) {
            return Notes::error($code ,$th->getMessage());
        }

    });

    Route::post('/rfid/ganti', function (GantiRfidRequest $request) {

        $rfid = DetailLinen::find($request->lama);

        try {

            $newPost = $rfid->replicate();
            $newPost->{DetailLinen::field_primary()} = $request->baru;
            $newPost->save();

            $rfid->{DetailLinen::field_stock_status()} = StockType::Unassign;
            $rfid->{DetailLinen::field_last_status()} = StatusType::GantiRfid;
            $rfid->save();

            History::log($request->lama, StatusType::GantiRfid, [
                'lama' => $rfid->all(),
                'baru' => $newPost->toArray(),
            ]);

            return Notes::data(new LinenDetailResource($newPost));

        } catch (\Throwable $th) {
            return Notes::error($request->all(), $th->getMessage());
        }

    });

    Route::post('/register', function (DetailRfidRequest $request) {

        try {

            if(is_array($request->linen_rfid)){

                DB::beginTransaction();

                $linen = collect($request->linen_rfid)->map(function($item) use($request){

                    return [
                        DetailLinen::field_primary() => $item,
                        DetailLinen::field_id_rs() => $request->rs_id,
                        DetailLinen::field_id_ruangan() => $request->ruangan_id,
                        DetailLinen::field_name_id() => $request->nama_id,
                        DetailLinen::field_stock_status() => StockType::Unassign,
                        DetailLinen::field_last_status() => StatusType::Register,
                        DetailLinen::CREATED_AT => date('Y-m-d H:i:s'),
                        DetailLinen::UPDATED_AT => date('Y-m-d H:i:s'),
                        DetailLinen::CREATED_BY => auth()->user()->id,
                        DetailLinen::UPDATED_BY => auth()->user()->id,
                    ];
                });

                DetailLinen::insert($linen->toArray());

                $history = collect($request->linen_rfid)->map(function($item) use($request){

                    return [
                        ModelsHistory::field_name() => $item,
                        ModelsHistory::field_status() => StatusType::Register,
                        ModelsHistory::field_created_by() => auth()->user()->name,
                        ModelsHistory::field_created_at() => date('Y-m-d H:i:s'),
                        ModelsHistory::field_description() => json_encode([ DetailLinen::field_primary() => $item ]),
                    ];
                });

                ModelsHistory::insert($history->toArray());
                DB::commit();

                $return = DetailLinen::whereIn(DetailLinen::field_primary(), $linen->pluck(DetailLinen::field_primary()))->get();

                return Notes::data(LinenDetailResource::collection($return));
            }
            else{

                $linen = DetailLinen::create([
                    DetailLinen::field_id_rs() => $request->rs_id,
                    DetailLinen::field_id_ruangan() => $request->ruangan_id,
                    DetailLinen::field_name_id() => $request->nama_id,
                    DetailLinen::field_primary() => $request->linen_rfid,
                ]);

                return Notes::data(new LinenDetailResource($linen));

            }

        } catch (\Throwable $th) {
            DB::rollBack();
            return Notes::error($request->all() ,$th->getMessage());
        }

    });

    Route::post('/kotor', function (KotorRequest $request) {

        try {

            if(is_array($request->linen_rfid)){

                DB::beginTransaction();

                $linen = collect($request->linen_rfid)->map(function($item) use($request){

                    return [
                        Kotor::field_name() => $request->key,
                        Kotor::field_rfid() => $item,
                        Kotor::field_id_rs() => $request->rs_id,
                        Kotor::field_id_ruangan() => $request->ruangan_id,
                        Kotor::CREATED_AT => date('Y-m-d H:i:s'),
                        Kotor::UPDATED_AT => date('Y-m-d H:i:s'),
                        Kotor::CREATED_BY => auth()->user()->id,
                        Kotor::UPDATED_BY => auth()->user()->id,
                    ];
                });

                Kotor::insert($linen->toArray());

                $history = collect($request->linen_rfid)->map(function($item) use($request){

                    return [
                        ModelsHistory::field_name() => $item,
                        ModelsHistory::field_status() => StatusType::Kotor,
                        ModelsHistory::field_created_by() => auth()->user()->name,
                        ModelsHistory::field_created_at() => date('Y-m-d H:i:s'),
                        ModelsHistory::field_description() => json_encode([ Kotor::field_rfid() => $item ]),
                    ];
                });

                ModelsHistory::insert($history->toArray());
                DB::commit();

                $return = DetailLinen::whereIn(DetailLinen::field_primary(), $linen->pluck(DetailLinen::field_primary()))->get();

                return Notes::data(LinenDetailResource::collection($return));
            }
            else{

                $linen = DetailLinen::create([
                    DetailLinen::field_id_rs() => $request->rs_id,
                    DetailLinen::field_id_ruangan() => $request->ruangan_id,
                    DetailLinen::field_name_id() => $request->nama_id,
                    DetailLinen::field_primary() => $request->linen_rfid,
                ]);

                return Notes::data(new LinenDetailResource($linen));

            }

        } catch (\Throwable $th) {
            DB::rollBack();
            return Notes::error($request->all() ,$th->getMessage());
        }

    });

    Route::get('/opname', function (Request $request) {

    });

    Route::post('/opname', function (Request $request) {

    });
});
