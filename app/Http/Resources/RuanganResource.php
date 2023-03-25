<?php

namespace App\Http\Resources;

use App\Dao\Models\Lokasi;
use Illuminate\Http\Resources\Json\JsonResource;

class RuanganResource extends JsonResource
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
            'id' => $this->field_primary,
            'nama' => $this->field_name,
        ];
        // return parent::toArray($request);
    }
}
