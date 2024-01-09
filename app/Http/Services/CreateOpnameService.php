<?php

namespace App\Http\Services;

use App\Dao\Enums\BooleanType;
use App\Dao\Enums\OpnameType;
use App\Dao\Enums\ProcessType;
use App\Dao\Interfaces\CrudInterface;
use App\Dao\Models\Detail;
use App\Dao\Models\OpnameDetail;
use Illuminate\Support\Facades\DB;
use Plugins\Alert;
use Plugins\History;

class CreateOpnameService
{
    public function save(CrudInterface $repository, $data)
    {
        $check = false;
        try {

            DB::beginTransaction();
            $check = $repository->saveRepository($data->all());

            if(isset($check['status']) && $check['status']){

                $opname = $check['data'];
                $opname_id = $opname->opname_id;
                $opname_status = $opname->opname_status;

                $data_rfid = Detail::where(Detail::field_rs_id(), $opname->opname_id_rs)->get();
                if($data_rfid){

                    $id = auth()->user()->id;
                    $tgl_mulai = $data->opname_mulai.' '.date('H:i:s');
                    $tgl = date('Y-m-d H:i:s');

                    $rfid = $data_rfid->map(function($item) use($opname_id, $id, $tgl, $tgl_mulai){

                        $data[OpnameDetail::field_rfid()] = $item->detail_rfid;
                        $data[OpnameDetail::field_transaksi()] = $item->detail_status_transaksi;
                        $data[OpnameDetail::field_proses()] = $item->detail_status_proses;
                        $data[OpnameDetail::field_created_at()] = $tgl;
                        $data[OpnameDetail::field_created_by()] = $id;
                        $data[OpnameDetail::field_updated_at()] = $item->detail_updated_at;
                        $data[OpnameDetail::field_updated_by()] = $id;
                        $data[OpnameDetail::field_waktu()] = $tgl;
                        $data[OpnameDetail::field_ketemu()] = in_array($item->detail_status_transaksi, BERSIH) ? BooleanType::No : BooleanType::Yes;
                        $data[OpnameDetail::field_opname()] = $opname_id;
                        $data[OpnameDetail::field_hilang()] = $item->detail_hilang_created_at;

                        return $data;
                    })->toArray();

                    foreach(array_chunk($rfid, env('TRANSACTION_CHUNK')) as $save_transaksi){
                        OpnameDetail::insert($save_transaksi);
                    }
                }

                History::bulk($data->rfid, ProcessType::Opname, 'Opname dibuat');

                Alert::create();
            }
            else{
                $message = env('APP_DEBUG') ? $check['data'] : $check['message'];
                Alert::error($message);
            }

            DB::commit();

        } catch (\Throwable $th) {
            DB::rollBack();
            Alert::error($th->getMessage());
            return $th->getMessage();
        }

        return $check;
    }
}
