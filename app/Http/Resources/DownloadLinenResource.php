<?php

namespace App\Http\Resources;

use App\Dao\Enums\ProcessType;
use App\Dao\Enums\StockType;
use App\Dao\Enums\TransactionType;
use App\Dao\Models\Lokasi;
use App\Dao\Models\Transaksi;
use App\Dao\Models\ViewDetailLinen;
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
        $check = Transaksi::addSelect([Transaksi::field_rfid() , ViewDetailLinen::field_rs_id()])
            ->leftJoinRelationship(HAS_DETAIL)
            ->whereNull(Transaksi::field_delivery())
            ->where(ViewDetailLinen::field_rs_id(), $request->rsid)
            ->get()->pluck(Transaksi::field_rfid(), Transaksi::field_rfid())
            ->toArray() ?? [];

        return [
            'id' => $this->field_primary,
            'rs' => $this->field_rs_id,
            'loc' => $this->field_ruangan_id,
            'jns' => $this->field_id,
            'sts' => in_array($this->field_primary, $check) ? ProcessType::Kotor : $this->field_status_process,
            'tgl' => $this->field_tanggal_update->format('Y-m-d H:i:s') ?? date('Y-m-d H:i:s'),
        ];
        // return parent::toArray($request);
    }
}
