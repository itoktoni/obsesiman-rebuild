<?php

use App\Dao\Enums\BooleanType;
use App\Dao\Enums\CetakType;
use App\Dao\Enums\CuciType;
use App\Dao\Enums\LogType;
use App\Dao\Enums\OpnameType;
use App\Dao\Enums\ProcessType;
use App\Dao\Enums\RegisterType;
use App\Dao\Enums\TransactionType;
use App\Dao\Models\Cetak;
use App\Dao\Models\Detail;
use App\Dao\Models\History as ModelsHistory;
use App\Dao\Models\Opname;
use App\Dao\Models\Rs;
use App\Dao\Models\Transaksi;
use App\Dao\Models\ViewDetailLinen;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\PendingController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WebhookController;
use App\Http\Requests\DetailDataRequest;
use App\Http\Requests\DetailUpdateRequest;
use App\Http\Requests\OpnameDetailRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\DetailCollection;
use App\Http\Resources\DetailResource;
use App\Http\Resources\DownloadCollection;
use App\Http\Resources\OpnameResource;
use App\Http\Resources\RsResource;
use App\Http\Services\SaveOpnameService;
use App\Http\Services\SaveTransaksiService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Plugins\History;
use Plugins\Notes;
use Plugins\Query;
use Symfony\Component\Process\Process;

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

Route::post('deploy', [WebhookController::class, 'deploy']);

Route::middleware(['auth:sanctum'])->group(function () use ($routes) {

    Route::get('status_register', function () {

        $data = [];
        foreach (RegisterType::getInstances() as $value => $key) {
            $data[] = [
                'status_id' => $key,
                'status_nama' => formatWorld($value),
            ];
        }

        return Notes::data($data);
    });

    Route::get('status_cuci', function () {

        $data = [];
        foreach (CuciType::getInstances() as $value => $key) {
            $data[] = [
                'status_id' => $key,
                'status_nama' => formatWorld($value),
            ];
        }

        return Notes::data($data);
    });

    Route::get('status_proses', function () {

        $data = [];
        foreach (ProcessType::getInstances() as $value => $key) {
            $data[] = [
                'status_id' => $key,
                'status_nama' => formatWorld($value),
            ];
        }

        return Notes::data($data);
    });

    Route::get('status_transaksi', function () {

        $data = [];
        foreach (TransactionType::getInstances() as $value => $key) {
            $data[] = [
                'status_id' => $key,
                'status_nama' => formatWorld($value),
            ];
        }

        return Notes::data($data);
    });

    Route::get('download/{rsid}', function ($rsid, Request $request) {
        set_time_limit(0);
        $data = ViewDetailLinen::where(ViewDetailLinen::field_rs_id(), $rsid)->get();
        if (count($data) == 0) {
            return Notes::error('Data Tidak Ditemukan !');
        }
        $request->request->add([
            'rsid' => $rsid,
        ]);
        $resource = new DownloadCollection($data);
        return $resource;
    });

    Route::get('rs', function (Request $request) {

        $status_register = [];
        foreach (RegisterType::getInstances() as $value => $key) {
            $status_register[] = [
                'status_id' => $key,
                'status_nama' => formatWorld($value),
            ];
        }

        $status_cuci = [];
        foreach (CuciType::getInstances() as $value => $key) {
            $status_cuci[] = [
                'status_id' => $key,
                'status_nama' => formatWorld($value),
            ];
        }

        $status_proses = [];
        foreach (ProcessType::getInstances() as $value => $key) {
            $status_proses[] = [
                'status_id' => $key,
                'status_nama' => formatWorld($value),
            ];
        }

        $status_transaksi = [];
        foreach (TransactionType::getInstances() as $value => $key) {
            $status_transaksi[] = [
                'status_id' => $key,
                'status_nama' => formatWorld($value),
            ];
        }

        try {

            $rs = Rs::with([HAS_RUANGAN, HAS_JENIS])->get();
            $collection = RsResource::collection($rs);
            $add = [
                'status_transaksi' => $status_transaksi,
                'status_proses' => $status_proses,
                'status_cuci' => $status_cuci,
                'status_register' => $status_register,
            ];

            $data = Notes::data($collection, $add);

            return $data;

        } catch (\Throwable $th) {

            return Notes::error($th->getMessage());
        }

    });

    Route::get('rs/{rsid}', function ($rsid) {

        $status_register = [];
        foreach (RegisterType::getInstances() as $value => $key) {
            $status_register[] = [
                'status_id' => $key,
                'status_nama' => formatWorld($value),
            ];
        }

        $status_cuci = [];
        foreach (CuciType::getInstances() as $value => $key) {
            $status_cuci[] = [
                'status_id' => $key,
                'status_nama' => formatWorld($value),
            ];
        }

        $status_proses = [];
        foreach (ProcessType::getInstances() as $value => $key) {
            $status_proses[] = [
                'status_id' => $key,
                'status_nama' => formatWorld($value),
            ];
        }

        $status_transaksi = [];
        foreach (TransactionType::getInstances() as $value => $key) {
            $status_transaksi[] = [
                'status_id' => $key,
                'status_nama' => formatWorld($value),
            ];
        }

        try {

            $rs = Rs::with([HAS_RUANGAN, HAS_JENIS])->findOrFail($rsid);
            $collection = new RsResource($rs);
            // $data['ruangan'] = RuanganResource::collection($rs->has_ruangan);
            // $data['linen'] = JenisResource::collection($rs->has_jenis);
            $add['status_transaksi'] = $status_transaksi;
            $add['status_proses'] = $status_proses;
            $add['status_cuci'] = $status_cuci;
            $add['status_register'] = $status_register;

            return Notes::data($collection, $add);

        } catch (\Throwable $th) {

            return Notes::error($th->getMessage());
        }

    });

    Route::post('register', function (RegisterRequest $request) {
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        try {

            $code = env('CODE_BERSIH', 'BSH');
            $autoNumber = Query::autoNumber(Transaksi::getTableName(), Transaksi::field_delivery(), $code . date('ymd'), env('AUTO_NUMBER', 15));

            if ($request->status_register == RegisterType::GantiChip) {
                $transaksi_status = TransactionType::Kotor;
                $proses_status = ProcessType::Kotor;
            } else {
                $transaksi_status = TransactionType::Register;
                $proses_status = ProcessType::Register;
            }

            if (is_array($request->rfid)) {

                DB::beginTransaction();

                $linen = collect($request->rfid)->map(function ($item) use ($request, $transaksi_status, $proses_status) {

                    return [
                        Detail::field_primary() => $item,
                        Detail::field_rs_id() => $request->rs_id,
                        Detail::field_ruangan_id() => $request->ruangan_id,
                        Detail::field_jenis_id() => $request->jenis_id,
                        Detail::field_status_cuci() => $request->status_cuci,
                        Detail::field_status_transaction() => $transaksi_status,
                        Detail::field_status_register() => $request->status_register ? $request->status_register : RegisterType::Register,
                        Detail::field_status_process() => $proses_status,
                        Detail::field_status_history() => $transaksi_status,
                        Detail::field_created_at() => date('Y-m-d H:i:s'),
                        Detail::field_updated_at() => date('Y-m-d H:i:s'),
                        Detail::field_created_by() => auth()->user()->id,
                        Detail::field_updated_by() => auth()->user()->id,
                    ];
                });

                Detail::insert($linen->toArray());

                $linen_transaksi = collect($request->rfid)->map(function ($item) use ($request, $autoNumber, $transaksi_status, $proses_status) {
                    Transaksi::where(Transaksi::field_rfid(), $item)
                        ->whereNull(Transaksi::field_rs_ori())
                        ->update([
                            Transaksi::field_status_transaction() => $transaksi_status,
                            Transaksi::field_rs_ori() => $request->rs_id
                        ]);

                    $check_transaksi = Transaksi::where(Transaksi::field_rfid(), $item)
                        ->whereNull(Transaksi::field_delivery())
                        ->count();

                    if ($check_transaksi == 0) {

                        return [
                            Transaksi::field_key() => $autoNumber,
                            Transaksi::field_rs_id() => $request->rs_id,
                            Transaksi::field_rs_ori() => $request->rs_id,
                            Transaksi::field_ruangan_id() => $request->ruangan_id,
                            Transaksi::field_rfid() => $item,
                            Transaksi::field_status_transaction() => $transaksi_status,
                            Transaksi::field_created_at() => date('Y-m-d H:i:s'),
                            Transaksi::field_updated_at() => date('Y-m-d H:i:s'),
                            Transaksi::field_created_by() => auth()->user()->id,
                            Transaksi::field_updated_by() => auth()->user()->id,
                        ];
                    }
                });

                $linen_transaksi = array_filter($linen_transaksi->toArray(), fn ($value) => !is_null($value));

                if(!empty($linen_transaksi)){
                    Transaksi::insert($linen_transaksi);
                }

                $history = collect($request->rfid)->map(function ($item) use ($transaksi_status) {

                    return [
                        ModelsHistory::field_name() => $item,
                        ModelsHistory::field_status() => $transaksi_status,
                        ModelsHistory::field_created_by() => auth()->user()->name,
                        ModelsHistory::field_created_at() => date('Y-m-d H:i:s'),
                        ModelsHistory::field_description() => LogType::getDescription($transaksi_status),
                    ];
                });

                ModelsHistory::upsert($history->toArray(), [ModelsHistory::field_name()]);
                DB::commit();

                $return = ViewDetailLinen::whereIn(ViewDetailLinen::field_primary(), $request->rfid)
                    ->get();

                return Notes::data(DetailResource::collection($return));
            } else {
                DB::beginTransaction();

                $detail = Detail::create([
                    Detail::field_primary() => $request->rfid,
                    Detail::field_rs_id() => $request->rs_id,
                    Detail::field_ruangan_id() => $request->ruangan_id,
                    Detail::field_jenis_id() => $request->jenis_id,
                    Detail::field_status_cuci() => $request->status_cuci,
                    Detail::field_status_register() => $request->status_register ? $request->status_register : RegisterType::Register,
                    Detail::field_status_transaction() => $transaksi_status,
                    Detail::field_status_process() => $proses_status,
                    Detail::field_status_history() => $transaksi_status,
                    Detail::field_created_at() => date('Y-m-d H:i:s'),
                    Detail::field_updated_at() => date('Y-m-d H:i:s'),
                    Detail::field_created_by() => auth()->user()->id,
                    Detail::field_updated_by() => auth()->user()->id,
                ]);

                Transaksi::where(Transaksi::field_rfid(), $request->rfid)
                        ->whereNull(Transaksi::field_rs_ori())
                        ->update([
                            Transaksi::field_status_transaction() => $transaksi_status,
                            Transaksi::field_rs_ori() => $request->rs_id
                        ]);

                $check_transaksi = Transaksi::where(Transaksi::field_rfid(), $request->rfid)
                    ->whereNull(Transaksi::field_delivery())
                    ->count();

                if ($check_transaksi == 0) {
                    $transaksi = Transaksi::create([
                        Transaksi::field_key() => $autoNumber,
                        Transaksi::field_rs_id() => $request->rs_id,
                        Transaksi::field_rs_ori() => $request->rs_id,
                        Transaksi::field_ruangan_id() => $request->ruangan_id,
                        Transaksi::field_rfid() => $request->rfid,
                        Transaksi::field_status_transaction() => $transaksi_status,
                        Transaksi::field_created_at() => date('Y-m-d H:i:s'),
                        Transaksi::field_updated_at() => date('Y-m-d H:i:s'),
                        Transaksi::field_created_by() => auth()->user()->id,
                        Transaksi::field_updated_by() => auth()->user()->id,
                    ]);
                }

                History::log($request->rfid, LogType::Register, $request->rfid);

                $view = ViewDetailLinen::findOrFail($request->rfid);
                $collection = new DetailResource($view);
                DB::commit();

                return Notes::data($collection);

            }

        } catch (\Illuminate\Database\QueryException $th) {
            DB::rollBack();

            if ($th->getCode() == 23000) {
                return Notes::error($request->all(), 'data RFID yang dikirim sudah ada di database');
            }

            return Notes::error($request->all(), $th->getMessage());
        } catch (\Throwable $th) {
            DB::rollBack();
            return Notes::error($request->all(), $th->getMessage());
        }

    });

    Route::get('detail/{rfid}', function ($rfid) {
        try {
            $data = ViewDetailLinen::findOrFail($rfid);
            $collection = new DetailResource($data);
            return Notes::data($collection);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return Notes::error($rfid, 'RFID ' . $rfid . ' tidak ditemukan');
        } catch (\Throwable $th) {
            return Notes::error($rfid, $th->getMessage());
        }
    });

    Route::match(['POST', 'GET'], 'detail', function (Request $request) {
        try {
            $query = ViewDetailLinen::query();
            $data = $query->filter()->paginate(env('PAGINATION_NUMBER', 10));

            $collection = new DetailCollection($data);
            return $collection;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return Notes::error('data RFID tidak ditemukan');
        } catch (\Throwable $th) {
            return Notes::error($th->getMessage());
        }
    });

    Route::post('detail/rfid', function (DetailDataRequest $request) {
        try {

            // $item = Detail::with([HAS_RS, HAS_RUANGAN, HAS_JENIS, HAS_USER])->whereIn(Detail::field_primary(), $request->rfid)->get();
            $item = Query::getDetail()->whereIn(Detail::field_primary(), $request->rfid)->get();

            if($item->count() == 0){
                return Notes::error('data RFID tidak ditemukan');
            }

            $collection = [];
            foreach($item as $data){

                $collection[] = [
                    'linen_id' => $data->detail_rfid,
                    'linen_nama' => $data->jenis_nama ?? '',
                    'rs_id' => $data->detail_id_rs,
                    'rs_nama' => $data->rs_nama ?? '',
                    'ruangan_id' => $data->detail_id_ruangan,
                    'ruangan_nama' => $data->ruangan_nama ?? '',
                    'status_register' => RegisterType::getDescription($data->detail_status_register),
                    'status_cuci' => CuciType::getDescription($data->detail_status_cuci),
                    'status_transaksi' => TransactionType::getDescription($data->detail_status_transaksi),
                    'status_proses' => ProcessType::getDescription($data->detail_status_proses),
                    'tanggal_create' => $data->detail_created_at ? $data->detail_created_at->format('Y-m-d') : null,
                    'tanggal_update' => $data->detail_updated_at ? $data->detail_updated_at->format('Y-m-d') : null,
                    'tanggal_delete' => $data->detail_deleted_at ? $data->detail_deleted_at->format('Y-m-d') : null,
                    'pemakaian' => $data->detail_total_cuci ?? 0,
                    'user_nama' => $data->name ?? null,
                ];
            }

            return Notes::data($collection);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return Notes::error('data RFID tidak ditemukan');
        } catch (\Throwable $th) {
            return Notes::error($th->getMessage());
        }
    });

    Route::post('linen_detail', function (DetailDataRequest $request) {
        try {
            $data = ViewDetailLinen::whereIn(ViewDetailLinen::field_primary(), $request->rfid)->get();
            $collection = DetailResource::collection($data);
            return Notes::data($collection);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return Notes::error('data RFID tidak ditemukan');
        } catch (\Throwable $th) {
            return Notes::error($th->getMessage());
        }
    });

    Route::post('detail/{rfid}', function ($rfid, DetailUpdateRequest $request) {
        try {

            $data = Detail::with([HAS_VIEW])->findOrFail($rfid);

            if ($data) {

                $lama = clone $data;
                $data->{Detail::field_rs_id()} = $request->rs_id;
                $data->{Detail::field_ruangan_id()} = $request->ruangan_id;
                $data->{Detail::field_jenis_id()} = $request->jenis_id;

                // $data->{Detail::field_updated_at()} = date('Y-m-d H:i:s');
                $data->{Detail::field_updated_by()} = auth()->user()->id;
                $data->{Detail::field_status_history()} = $data->field_status_terakhir ?? 0;

                if ($request->status_cuci) {
                    $data->{Detail::field_status_cuci()} = $request->status_cuci;
                }
                if ($request->status_register) {
                    $data->{Detail::field_status_register()} = $request->status_register;
                }

                $data->save();

                History::log($rfid, LogType::UpdateChip);
            }

            $view = ViewDetailLinen::findOrFail($rfid);
            return Notes::data(new DetailResource($view));

        } catch (\Throwable $th) {
            return Notes::error($rfid, $th->getMessage());
        }
    });

    Route::post('kotor', [TransaksiController::class, 'kotor']);
    Route::post('retur', [TransaksiController::class, 'retur']);
    Route::post('rewash', [TransaksiController::class, 'rewash']);

    Route::get('grouping/{rfid}', function ($rfid, SaveTransaksiService $service) {
        $flag = 'GROUPING';

        try {
            $data = Detail::with([HAS_RS, HAS_RUANGAN, HAS_JENIS])
            ->select([
                'detail_id_jenis',
                'detail_id_rs',
                'detail_id_ruangan',
                'detail_status_register',
                'detail_status_cuci',
                'detail_status_transaksi',
                'detail_status_proses',
                'detail_created_at',
                'detail_updated_at',
                'detail_deleted_at',
            ])->findOrFail($rfid);

            $save = Transaksi::where(Transaksi::field_rfid(), $rfid)
                ->whereNull(Transaksi::field_barcode());

            $jenis = $data->has_jenis;
            $rs = $data->has_rs;
            $ruangan = $data->has_ruangan;

            $data_transaksi = [];
            $linen[] = $rfid;

            $rs_id = $data->detail_id_rs;

            $date = date('Y-m-d H:i:s');
            $user = auth()->user()->id;
            $code_rs = $rs->rs_code ?? 'XXX';

            $status_transaksi = $data->field_status_transaction;
            $status_register = $data->field_status_register;

            $status_baru = TransactionType::Kotor;
            if (in_array($status_transaksi, [TransactionType::BersihKotor, TransactionType::BersihRetur, TransactionType::BersihRewash])) {
                $status_baru = TransactionType::Kotor;
            } elseif ($status_transaksi == TransactionType::Kotor) {
                $status_baru = TransactionType::Kotor;
            } elseif ($status_transaksi == TransactionType::Retur) {
                $status_baru = TransactionType::Retur;
            } elseif ($status_transaksi == TransactionType::Rewash) {
                $status_baru = TransactionType::Rewash;
            } elseif ($status_transaksi == TransactionType::Register) {
                $status_baru = TransactionType::Register;
                if ($status_register == RegisterType::GantiChip) {
                    $status_baru = TransactionType::Kotor;
                }

                $save->update([
                    Transaksi::field_status_transaction() => $status_baru,
                    Transaksi::field_rs_id() => $rs_id,
                    Transaksi::field_rs_ori() => $rs_id,
                ]);
            }

            $check_transaksi = Transaksi::where(Transaksi::field_rfid(), $rfid)
                ->whereNull(Transaksi::field_delivery())
                ->first();

            if (empty($check_transaksi) and (in_array($status_transaksi, [
                TransactionType::BersihKotor,
                TransactionType::BersihRetur,
                TransactionType::BersihRewash,
                TransactionType::Kotor,
                TransactionType::Retur,
                TransactionType::Rewash,
                TransactionType::Register,
            ]))) {

                $flag = 'TRANSAKSI';

                $startDate = Carbon::createFromFormat('Y-m-d H:i', date('Y-m-d') . ' 00:00');
                $endDate = Carbon::createFromFormat('Y-m-d H:i', date('Y-m-d') . ' 05:59');

                $check_date = Carbon::now()->between($startDate, $endDate);

                if ($check_date) {
                    $date = Carbon::now()->addDay(-1);
                }

                $data_transaksi[] = [
                    Transaksi::field_key() => Query::autoNumber((new Transaksi())->getTable(), Transaksi::field_key(), 'GROUP' . date('ymd') . $code_rs, 20),
                    Transaksi::field_rfid() => $rfid,
                    Transaksi::field_status_transaction() => $status_baru,
                    Transaksi::field_rs_id() => $rs_id,
                    Transaksi::field_rs_ori() => $rs_id,
                    Transaksi::field_beda_rs() => BooleanType::No,
                    Transaksi::field_created_at() => $date,
                    Transaksi::field_created_by() => $user,
                    Transaksi::field_updated_at() => $date,
                    Transaksi::field_updated_by() => $user,
                    Transaksi::field_grouping() => BooleanType::Yes,
                ];

                $log[] = [
                    ModelsHistory::field_name() => $rfid,
                    ModelsHistory::field_status() => ProcessType::Grouping,
                    ModelsHistory::field_created_by() => auth()->user()->name,
                    ModelsHistory::field_created_at() => $date,
                    ModelsHistory::field_description() => ProcessType::getDescription(ProcessType::Grouping),
                ];
            } else {

                $flag = 'GROUPING';

                if (!empty($check_transaksi->transaksi_pending_in) && empty($check_transaksi->transaksi_pending_out)) {

                    $flag = 'PENDING';
                }

                $log[] = [
                    ModelsHistory::field_name() => $rfid,
                    ModelsHistory::field_status() => ProcessType::Grouping,
                    ModelsHistory::field_created_by() => auth()->user()->name,
                    ModelsHistory::field_created_at() => $date,
                    ModelsHistory::field_description() => ProcessType::getDescription(ProcessType::Grouping),
                ];
            }

            Detail::find($rfid)->update([
                Detail::field_updated_by() => auth()->user()->id,
                Detail::field_status_history() => LogType::Grouping,
                // Detail::field_updated_at() => date('Y-m-d H:i:s'),
                // Detail::field_pending_created_at() => null,
                // Detail::field_pending_updated_at() => null,
                // Detail::field_hilang_created_at() => null,
                // Detail::field_hilang_updated_at() => null,
            ]);

            $collection = [
                'linen_id' => $data->detail_id_jenis,
                'linen_nama' => $jenis->field_name ?? '',
                'rs_id' => $data->detail_id_rs,
                'rs_nama' => $rs->field_name ?? '',
                'ruangan_id' => $data->detail_id_ruangan,
                'ruangan_nama' => $ruangan->field_name ?? '',
                'status_register' => RegisterType::getDescription($data->detail_status_register),
                'status_cuci' => CuciType::getDescription($data->detail_status_cuci),
                'status_transaksi' => TransactionType::getDescription($data->detail_status_transaksi),
                'status_proses' => ProcessType::getDescription($data->detail_status_proses),
                'tanggal_create' => $data->detail_created_at ? $data->detail_created_at->format('Y-m-d') : null,
                'tanggal_update' => $data->detail_updated_at ? $data->detail_updated_at->format('Y-m-d') : null,
                'tanggal_delete' => $data->detail_deleted_at ? $data->detail_deleted_at->format('Y-m-d') : null,
                'pemakaian' => 0,
                'user_nama' => auth()->user()->name ?? null,
                'status' => $flag ?? null,
            ];

            $status_grouping = ProcessType::Grouping;
            if(in_array($data->field_status_process, [ProcessType::Barcode, ProcessType::Delivery])){
                $status_grouping = $data->field_status_process;
            }

            return $service->save($status_baru, $status_grouping, $data_transaksi, $linen, $log, $collection);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return Notes::error($rfid, 'RFID ' . $rfid . ' tidak ditemukan');
        } catch (\Throwable $th) {
            return Notes::error($rfid, $th->getMessage());
        }
    });

    Route::post('barcode', [BarcodeController::class, 'barcode']);
    Route::get('barcode/{code}', [BarcodeController::class, 'print']);

    Route::get('list/barcode/{rsid}', function ($rsid) {
        $data = Cetak::select([Cetak::field_name()])
            ->where(Cetak::field_rs_id(), $rsid)
            ->where(Cetak::field_type(), CetakType::Barcode)
            ->where(Cetak::field_date(), '>=', now()->addDay(-30))
            ->orderBy(Cetak::field_date(), 'DESC')
            ->get();

        return Notes::data(['total' => $data]);
    });

    Route::post('delivery', [DeliveryController::class, 'delivery']);
    Route::get('delivery/{code}', [DeliveryController::class, 'print']);

    Route::get('list/delivery/{rsid}', function ($rsid) {
        $data = Cetak::select([Cetak::field_name()])
            ->where(Cetak::field_rs_id(), $rsid)
            ->where(Cetak::field_type(), CetakType::Delivery)
            ->where(Cetak::field_date(), '>=', now()->addDay(-30))
            ->orderBy(Cetak::field_date(), 'DESC');

        if (request()->get('tgl')) {
            $data->where(Cetak::field_date(), '=', request()->get('tgl'));
        }

        return Notes::data(['total' => $data->get()]);
    });

    Route::post('pending', [PendingController::class, 'pending']);
    Route::get('pending/{code}', [PendingController::class, 'print']);

    Route::get('total/delivery/{rsid}', function ($rsid) {
        $data = Transaksi::whereNull(Transaksi::field_delivery())
            ->whereNotNull(Transaksi::field_barcode())
            ->where(Transaksi::field_rs_ori(), $rsid)
            ->count();

        return Notes::data(['total' => $data]);
    });

    Route::get('total/delivery/{rsid}/{transaksi}', function ($rsid, $transaksi) {

        if ($transaksi == TransactionType::BersihKotor) {
            $transaksi = TransactionType::Kotor;
        } else if ($transaksi == TransactionType::BersihRetur){
            $transaksi = TransactionType::Retur;
        } else if ($transaksi == TransactionType::BersihRewash){
            $transaksi = TransactionType::Rewash;
        } else if ($transaksi == TransactionType::Unknown){
            $transaksi = TransactionType::Register;
        }

        $data = Transaksi::whereNull(Transaksi::field_delivery())
            ->whereNotNull(Transaksi::field_barcode())
            ->where(Transaksi::field_status_transaction(), $transaksi)
            ->where(Transaksi::field_rs_ori(), $rsid)
            ->count();

        return Notes::data(['total' => $data]);
    });

    Route::get('opname', function (Request $request) {
        try {
            $today = today()->format('Y-m-d');
            $data = Opname::with([HAS_RS])
                ->where(Opname::field_start(), '<=', $today)
                ->where(Opname::field_end(), '>=', $today)
                ->get();

            $collection = OpnameResource::collection($data);
            return Notes::data($collection);

        } catch (\Throwable $th) {
            return Notes::error($th->getCode(), $th->getMessage());
        }
    })->name('opname_data');

    Route::get('opname/{id}', function ($id, Request $request) {
        try {
            $data = Opname::with([HAS_RS])->find($id);

            $collection = new OpnameResource($data);
            return Notes::data($collection);

        } catch (\Throwable $th) {
            return Notes::error($th->getCode(), $th->getMessage());
        }
    })->name('opname_detail');

    Route::post('/opname', function (OpnameDetailRequest $request, SaveOpnameService $service) {
        $data = $service->save($request->{Opname::field_primary()}, $request->data);
        return $data;
    });
});
