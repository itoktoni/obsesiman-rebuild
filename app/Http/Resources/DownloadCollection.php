<?php

namespace App\Http\Resources;

use App\Dao\Enums\OpnameType;
use App\Dao\Enums\ProcessType;
use App\Dao\Models\Jenis;
use App\Dao\Models\Opname;
use App\Dao\Models\OpnameDetail;
use App\Dao\Models\Rs;
use App\Dao\Models\Ruangan;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\DB;

class DownloadCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $rsid = $request->rsid;

        $rs = Rs::find($rsid)->addSelect(
            [Rs::field_primary(), Rs::field_name()]
        )->first();

        $jenis = Jenis::where(Jenis::field_rs_id(), $rsid)
            ->addSelect([Jenis::field_primary(), Jenis::field_name()])
            ->get();

        $ruangan = Ruangan::addSelect([DB::raw('ruangan.ruangan_id, ruangan.ruangan_nama')])
            ->join('rs_dan_ruangan', 'rs_dan_ruangan.ruangan_id', 'ruangan.ruangan_id')
            ->where('rs_id', $rsid)
            ->get();

        $opname = Opname::with(['has_detail' => function($query){
            $query->whereNotNull(OpnameDetail::field_waktu());
        }])
            ->where(Opname::field_rs_id(), $rsid)
            ->where(Opname::field_status(), OpnameType::Proses)
            ->first();

        $sendOpname = [];
        if(!empty($opname)){
            if($opname->has_detail){
                $sendOpname = $opname->has_detail->pluck(OpnameDetail::field_rfid());
            }
        }

        $status = [];
        foreach(ProcessType::getInstances() as $value => $key){
            $status[] = [
                'status_id' => $key,
                'status_nama' => formatWorld($value),
            ];
        }

        return [
            'status' => true,
            'code' => 200,
            'name' => 'List',
            'message' => 'Data berhasil diambil',
            'data' => DownloadLinenResource::collection($this->collection),
            'rs' => $rs,
            'ruangan' => $ruangan,
            'jenis_linen' => $jenis,
            'status_proses' => $status,
            'opname' => $sendOpname,
        ];
        // return parent::toArray($request);
    }
}
