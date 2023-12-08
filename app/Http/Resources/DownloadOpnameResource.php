<?php

namespace App\Http\Resources;

use App\Dao\Enums\StockType;
use App\Dao\Models\Lokasi;
use Illuminate\Http\Resources\Json\JsonResource;

class DownloadOpnameResource extends JsonResource
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
            'id' => $this->field_rfid,
            'ketemu' => $this->field_waktu,
        ];
        // return parent::toArray($request);
    }
}
