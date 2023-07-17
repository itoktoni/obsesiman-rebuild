<?php

namespace App\Http\Resources;

use App\Dao\Enums\ProcessType;
use App\Dao\Models\Jenis;
use App\Dao\Models\Rs;
use Illuminate\Http\Resources\Json\ResourceCollection;

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
        $rs = Rs::all([Rs::field_primary(), Rs::field_name()]);
        $jenis = Jenis::all([Jenis::field_primary(), Jenis::field_name()]);
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
            'linen' => $jenis,
            'status_proses' => $status,
        ];
        // return parent::toArray($request);
    }
}
