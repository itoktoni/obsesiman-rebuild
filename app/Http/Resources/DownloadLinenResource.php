<?php

namespace App\Http\Resources;

use App\Dao\Enums\StockType;
use App\Dao\Models\Lokasi;
use Illuminate\Http\Resources\Json\JsonResource;

class DownloadLinenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

    public static $model;

    public function toArray($request)
    {
        return [
            'linen_id' => $this->field_primary,
            'linen_nama' => $this->field_name ?? '',
            'rs_id' => $this->field_rs_id,
            'rs_nama' => $this->field_rs_name ?? '',
            'ruangan_id' => $this->field_ruangan_id,
            'ruangan_nama' => $this->field_ruangan_name ?? '',
            'status_transaksi' => $this->field_status_transaction_name,
            'status_proses' => $this->field_status_process_name,
            'tanggal_update' => formatDate($this->field_tanggal_update),
        ];
        // return parent::toArray($request);
    }
}
